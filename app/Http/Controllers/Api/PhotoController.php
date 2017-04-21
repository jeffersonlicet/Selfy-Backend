<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CheckDuo;
use App\Jobs\CheckSpot;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Mockery\Exception;
use Validator;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class PhotoController extends Controller
{
    /**
     * Show the photo feed
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
        {
            $user_id    = Input::get('user_id', 0);
            $page       = Input::get('page', 0);
            $limit      = Input::get('limit', config('app.photos_per_page'));

            $validator =
                Validator::make(
                    ['user_id' => $user_id, 'limit' => $limit, 'page'=> $page],
                    ['user_id' => ['required', 'numeric'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'likes' => $validator->messages()->first()
                ]);
            }

            $result = Photo::collection(\Auth::user(), $user_id, $limit, $page * $limit);

            /** @noinspection PhpUndefinedMethodInspection */
            return response()->json([
                'status' => TRUE,
                'photos' => $result->isEmpty() ?  [] : $result->toArray()
            ]);

        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'url' => 'required'
        ]);

        if($validator->passes())
        {
            $photo = new Photo();
            $photo->user_id = \Auth::user()->user_id;
            $photo->url = $input['url'];

            if($request->has("caption"))
            {
                $photo->caption = $input['caption'];
            }

            $photo->saveOrFail();

            if($request->has("latitude") && $request->has("latitude") && \Auth::user()->spot_enabled)
            {
               $this->dispatch(new CheckSpot($photo, [floatval($input['latitude']), floatval($input['longitude'])]));
            }
            if(\Auth::user()->duo_enabled)
            {
                $this->dispatch(new CheckDuo($photo, rand(0, config('app.oxford_available_keys') - 1)));
            }

            \Auth::user()->photos_count++;
            \Auth::user()->save();

            return response()->json([
                'status' => TRUE,
                'report' => 'photo_uploaded'
            ]);
        }

        return response()->json([
            'status' => FALSE,
            'report' => $validator->messages()->first()
        ]);
    }

    /**
     * Display a specific photo
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $validator =
                Validator::make(
                    ['id' => $id],
                    ['id' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'likes' => $validator->messages()->first()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            if ($result = Photo::single($id))
            {

                if($result->user_id != \Auth::user()->user_id)
                {
                    $result->views_count++;
                    $result->save();
                }

                return response()->json([
                    'status' => TRUE,
                    'photos' => [$result->toArray()]
                ]);
            }

            throw new Exception('resource_not_found');
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try
        {
            $validator =
                Validator::make(
                    ['id' => $id],
                    ['id' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if ($request->has('caption'))
            {
                $input = $request->all();

                /** @noinspection PhpUndefinedMethodInspection */
                $photo = Photo::find($id);

                if(!$photo)
                {
                    throw new Exception("resource_deleted");
                }

                if($photo->user_id == \Auth::user()->user_id)
                {
                    $photo->caption = $input['caption'];
                    /** @noinspection PhpUndefinedMethodInspection */
                    $photo->touch();
                    /** @noinspection PhpUndefinedMethodInspection */
                    $photo->save();

                    return response()->json([
                        'status' => true,
                        'report' => 'resource_updated'
                    ]);
                }

                throw new Exception("access_denied");

            }

            throw new Exception("invalid_request");
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $validator =
                Validator::make(
                    ['id' => $id],
                    ['id' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }
            /** @noinspection PhpUndefinedMethodInspection */
            $photo = Photo::find($id);

            if(!$photo)
            {
                throw new Exception("resource_not_found");
            }

            if($photo->user_id == \Auth::user()->user_id)
            {
                /** @noinspection PhpUndefinedMethodInspection */
               $photo->delete();

                return response()->json([
                    'status' => true,
                    'report' => 'resource_deleted'
                ]);

            }

            throw new Exception("access_denied");
        }

        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate photo borders
     *  Given B, we generate A , C
     * @param $photo_id
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function borders($photo_id)
    {
        try
        {
            $user_id    = Input::get('user_id', \Auth::user()->user_id);
            $type       = Input::get('type', 'both');

            $validator =
                Validator::make(
                    [   'id' => $photo_id,
                        'user_id' => $user_id,
                        'type' =>$type
                    ],
                    ['id' => ['required', 'numeric'], 'user_id' => ['required', 'numeric'], 'type' => ['required', Rule::in(['both', 'top', 'bottom'])]]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }


            $top    = [];
            $bottom = [];

            switch ($type)
            {
                case 'both':
                    $result_top = Photo::related($photo_id, $user_id, '>', config('app.photos_per_border'));
                    $result_bottom = Photo::related($photo_id, $user_id, '<' , config('app.photos_per_border'));
                    $top      =  $result_top->isEmpty() ?  [] : $result_top->toArray();
                    $bottom   =  $result_bottom->isEmpty() ?  [] : $result_bottom->toArray();
                    break;

                case 'top':
                    $result_top = Photo::related($photo_id, $user_id, '>', config('app.photos_per_border'));
                    $top     =  $result_top->isEmpty() ?  [] : $result_top->toArray();
                    break;

                case 'bottom':
                    $result_bottom = Photo::related($photo_id, $user_id, '<' , config('app.photos_per_border'));
                    $bottom   =  $result_bottom->isEmpty() ?  [] : $result_bottom->toArray();
                    break;
            }

            return response()->json([
                'status' => TRUE,
                'top' => $top,
                'bottom' => $bottom
            ]);
        }

        catch (\Exception $e)
        {
            return response()->json([
                'status' => TRUE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get the best photos
     *
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function bests($user_id)
    {
        try
        {
            $user_id = $user_id == 0 ? \Auth::user()->user_id  : $user_id;
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);

            $validator =
                Validator::make(
                    ['user_id' => $user_id, 'limit' => $limit, 'page'=> $page],
                    ['user_id' => ['required', 'numeric'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $result = Photo::where('user_id', $user_id)->with('Challenges', 'Challenges.Object')->orderBy('likes_count', 'desc')->orderBy('views_count', 'desc')->orderBy('comments_count', 'desc')->get();

            /** @noinspection PhpUndefinedMethodInspection */
            return response()->json([
                'status' => TRUE,
                'photos' => $result->isEmpty() ?  [] : $result->toArray()
            ]);

        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Report a photo
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function report($id)
    {
        try
        {
            $validator =
                Validator::make(
                    ['id' => $id],
                    ['id' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $photo = Photo::find($id);

            if(!$photo)
            {
                throw new Exception("resource_not_found");
            }

            if($photo->user_id == \Auth::user()->user_id)
            {
                throw new Exception('invalid_action');
            }

            $photo->reports_count++;
            /** @noinspection PhpUndefinedMethodInspection */
            $photo->save();

            return response()->json([
                'status' => true,
                'report' => 'resource_reported'
            ]);

        }

        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    public function clothes($id)
    {
        try
        {
            $validator =
                Validator::make(
                    ['id' => $id],
                    ['id' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $photo = Photo::find($id);

            if(!$photo)
            {
                throw new Exception("resource_not_found");
            }

            $client     = new GuzzleClient(['base_uri' => "https://westus.api.cognitive.microsoft.com/face/api/"]);
            $adapter    = new GuzzleAdapter($client);
            $headers = [];
            $request    = new GuzzleRequest('POST', 'clothes', $headers, json_encode(['photo_url' => $photo->url]));
            $response   = $adapter->sendRequest($request);

            switch ($response->getStatusCode())
            {
                case 200:

                    $content = \GuzzleHttp\json_decode($response->getBody()->getContents());

                    return response()->json([
                        'status' => true,
                        'information' => $content
                    ]);

                    break;

                default:
                    throw new Exception('header response error '.$response->getStatusCode().' in get clothes info');
                    break;
            }
        }

        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }
}
