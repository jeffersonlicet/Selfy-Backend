<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CheckDuo;
use App\Jobs\CheckSpot;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;
use Validator;


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
            $id = Input::get('id', 0);
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.photos_per_page'));

            if($limit < 1 or $limit > 20)
            {
                throw new Exception('invalid limit range');
            }

            $result = Photo::collection(\Auth::user(), $id, $limit, $page * $limit);

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

            if($request->has("latitude") && $request->has("latitude"))
            {
               $this->dispatch(new CheckSpot($photo, [floatval($input['latitude']), floatval($input['longitude'])]));
            }

           $this->dispatch(new CheckDuo($photo, rand(0, config('app.oxford_available_keys') - 1)));

            return response()->json([
                'status' => TRUE,
                'report' => 'photo_uploaded'
            ]);
        }

        return response()->json([
            'status' => FALSE,
            'report' => $validator->messages()->toArray()
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
        try {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($result = Photo::single($id))
            {
                return response()->json([
                    'status' => TRUE,
                    'photos' => Photo::single($id)->toArray()
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
