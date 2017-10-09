<?php

namespace App\Http\Controllers\Api;

use DB;
use Exception;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Photo;
use App\Jobs\CheckDuo;
use App\Jobs\CheckPlay;
use App\Jobs\CheckSpot;
use App\Models\Hashtag;
use App\Helpers\Expression;
use App\Models\PhotoReport;
use App\Models\PhotoHashtag;
use Illuminate\Http\Request;
use App\Models\ChallengePlay;
use App\Models\UserInvitation;
use App\Jobs\CheckAdultContent;
use Illuminate\Validation\Rule;
use App\Models\UserPhotoMention;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Notifications\UserPhotoMentionNotification;

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
                    'report' => $validator->messages()->first()
                ]);
            }

            if($user_id != 0
                && $user_id != \Auth::user()->user_id
                && User::find($user_id) && User::find($user_id)->account_private
                && !UserInvitation::where(['user_id' => \Auth::user()->user_id, 'profile_id' => $user_id, 'invitation_status' => config('constants.INVITATION_STATUS.ACCEPTED')])->first())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => 'account_private'
                ]);
            }

            $result = Photo::collection(\Auth::user(), $user_id, $limit, $page * $limit);

            return response()->json([
                'status' => TRUE,
                'photos' => $result->isEmpty() ?  [] : $result->toArray()
            ]);

        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get the recent photo collections
     *
     * @return \Illuminate\Http\Response
     */
    public function recent()
    {
        try
        {
            $page       = Input::get('page', 0);
            $limit      = Input::get('limit', config('app.photos_per_page'));

            $validator =
                Validator::make(
                    ['limit' => $limit, 'page'=> $page],
                    ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'likes' => $validator->messages()->first()
                ]);
            }

            $result = Photo::recent(\Auth::user(), $limit, $page * $limit);

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

            if($request->has("caption"))
            {
                $this->handlePhotoHashtags($photo);
                $this->handlePhotoMentions($photo);
            }

            if(\Auth::user()->password_type != config('constants.APP_PLATFORMS.wp') && \Auth::user()->original_platform != config('constants.APP_PLATFORMS.wp')) {
                if($request->has("latitude") && $request->has("latitude") && \Auth::user()->spot_enabled)
                    $this->dispatch(new CheckSpot($photo, [floatval($input['latitude']), floatval($input['longitude'])]));

                if(\Auth::user()->duo_enabled)
                    $this->dispatch(new CheckDuo($photo, rand(0, config('app.oxford_available_keys') - 1)));

                if(\Auth::user()->play_enabled)
                    $this->dispatch(new CheckPlay($photo));
            }

            $this->dispatch(new CheckAdultContent($photo, rand(0, config('app.oxford_vision_available_keys') - 1)));

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

            if ($result = Photo::single($id))
            {
                if($result->user_id != \Auth::user()->user_id)
                {
                    if($result->User->account_private
                        && !UserInvitation::where(['user_id' => \Auth::user()->user_id,
                            'profile_id' => $result->user_id,
                            'invitation_status' => config('constants.INVITATION_STATUS.ACCEPTED')])->first())
                    {
                        return response()->json([
                            'status' => TRUE,
                            'report' => 'account_private'
                        ]);
                    }

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


                $photo = Photo::find($id);

                if(!$photo)
                {
                    throw new Exception("resource_deleted");
                }

                if($photo->user_id == \Auth::user()->user_id)
                {
                    $photo->caption = $input['caption'];

                    $photo->touch();

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

            $photo = Photo::find($id);

            if(!$photo)
            {
                throw new Exception("resource_not_found");
            }

            if($photo->user_id == \Auth::user()->user_id || \Auth::user()->hasRole('system-administrator'))
            {

               $photo->delete();

                \Auth::user()->photos_count--;
                \Auth::user()->save();

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
     * Given B, we generate A , C
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


            $result = Photo::where('user_id', $user_id)->with('Challenges', 'Challenges.Object')->orderBy('likes_count', 'desc')->orderBy('views_count', 'desc')->orderBy('comments_count', 'desc')->get();


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
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function report(Request $request)
    {
        try
        {
            $input = $request->all();
            $id = $input['photo_id'];

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


            $photo = Photo::find($id);

            if(!$photo)
            {
                throw new Exception("resource_not_found");
            }

            if($photo->user_id == \Auth::user()->user_id)
            {
                throw new Exception('invalid_action');
            }

            if(boolval(count(PhotoReport::where(['user_id' => \Auth::user()->user_id, 'photo_id' => $id])->first())))
            {
                throw new Exception('invalid_action');
            }

            $photo->reports_count++;

            $photo->save();

            $report = new PhotoReport();
            $report->photo_id = $id;
            $report->user_id = \Auth::user()->user_id;
            $report->save();

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

    /**
     * Return hint typed hashtag suggestions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function hashtag_search()
    {
        try
        {
            $query    = Input::get('query', 0);
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);

            $validator = Validator::make(
                ['query' => $query, 'limit' => $limit, 'page'=> $page],
                ['query' => ['required', 'string'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $hashtags = Hashtag::where('hashtag_text', 'LIKE', '%'.$query.'%')->where('hashtag_status', 0)->get();

            return response()->json([
                'status' => TRUE,
                'hashtags' => $hashtags->isEmpty() ? [] : $hashtags->toArray()
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
     * Return a list of the top hashtags
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function popular_hashtags()
    {
        try
        {
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);

            $validatorData =  ['limit' => $limit, 'page'=> $page];
            $validatorRules = ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']];

            $validator = Validator::make($validatorData, $validatorRules);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $result = DB::table('hashtags')
                ->join('photo_hashtags', 'hashtags.hashtag_id', '=', 'photo_hashtags.hashtag_id')
                ->where('photo_hashtags.created_at', '>=', Carbon::now()->subDay())
                ->join('photos', 'photo_hashtags.photo_id', '=', 'photos.photo_id')
                ->select('hashtags.hashtag_text', 'hashtags.hashtag_id', DB::raw('count(*) as hashtag_relevance'))
                ->groupBy('hashtags.hashtag_text', 'hashtags.hashtag_id')->orderBy('hashtag_relevance', 'DESC')
                ->limit($limit)->offset($limit*$page)->get();

            $curated = [];
            $thumb_ids = [];
            $tags_ids = [];

            //Append promoted hashtags:
            $promoted = Hashtag::where('hashtag_group', config('constants.HASHTAG_GROUP.promoted'))
                ->orderBy('created_at', 'DESC')->get();

            foreach ($promoted as $tag)
            {
                if(in_array($tag->hashtag_id, $tags_ids)) continue;

                $curated[] = [
                    'hashtag_id' => $tag->hashtag_id,
                    'hashtag_text' => $tag->hashtag_text,
                    'is_play'=>  $tag->Play !== null,
                    'thumbnail' => $tag->Play->play_thumb,
                    'relevance' => $tag->hashtag_relevance
                ];

                $tags_ids[] = $tag->hashtag_id;
            }

            //Append thumbnail
            foreach($result as $item)
            {

                if(in_array($item->hashtag_id, $tags_ids)) continue;

                $thumb = null;
                $photo = PhotoHashtag::where('hashtag_id', $item->hashtag_id)
                    ->orderBy('created_at', 'DESC')
                    ->whereHas('Photo', function ($query) use ($thumb_ids)
                    {
                        $query->whereNotIn('photo_id', $thumb_ids)->whereHas('User', function($subQuery)
                        {
                            $subQuery->where('account_private', '=',  0)
                                ->orWhereIn('user_id', \Auth::user()->followingIds(true));
                        });

                    })->first();

                if($photo)
                {
                    $thumb = $photo->Photo->url;
                    $thumb_ids[] = $photo->Photo->photo_id;
                }

                $curated[] = [
                    'hashtag_id' => $item->hashtag_id,
                    'hashtag_text' => $item->hashtag_text,
                    'is_play'=> ChallengePlay::where('play_hashtag', $item->hashtag_id)->first() != null,
                    'thumbnail' => $thumb,
                    'relevance' => $item->hashtag_relevance
                ];

                $tags_ids[] = $item->hashtag_id;
            }

            return response()->json([
                'status' => TRUE,
                'hashtags' => $curated
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
     * Return the photos of a specific {hashtag}
     * if the hashtag does not exists return a similar one
     * @return \Illuminate\Http\JsonResponse
     */
    public function photos_hashtag()
    {
        try
        {
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);
            $hashtagWord = Input::get('hashtag');

            $validatorData  = ['limit' => $limit, 'page'=> $page, 'hashtag' => $hashtagWord];
            $validatorRules = [
                'hashtag' => 'required',
                'limit' => ['required', 'numeric', 'between:1,20'],
                'page' => ['required', 'numeric']
            ];

            $validator = Validator::make($validatorData, $validatorRules);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if(!$hashtag = Hashtag::where('hashtag_text', $hashtagWord)->first())
                $hashtag = Hashtag::where('hashtag_text', 'LIKE', '%'.$hashtagWord.'%')->first();

            $curated = [];

            if($hashtag)
            {
                $result = PhotoHashtag::where('hashtag_id', $hashtag->hashtag_id)
                    ->whereHas('Photo', function($query)
                    {
                        $query->whereHas('User', function($subQuery)
                        {
                            $subQuery->where('account_private', '=', 0)->orWhereIn('user_id', \Auth::user()->followingIds(true));
                        })->with('User');
                    })
                    ->has('Photo')->with('Photo.user')->limit($limit)->offset($limit*$page)->get();

                foreach($result as $entry)
                    $curated[] = $entry->Photo;
            }

            return response()->json([
                'status' => TRUE,
                'photos' => $curated
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
     * Detect user mentions in the photo caption
     *
     * @param Photo $photo
     */
    private function handlePhotoMentions(Photo $photo)
    {
        $usernames = Expression::parseText($photo->caption, 'mentions');

        foreach ($usernames as $u)
        {
            if($user = User::where('username',$u)->first())
            {
                if(!$mention = UserPhotoMention::where([
                    'user_id' => $user->id,
                    'photo_id' => $photo->photo_id])->first())
                {
                    $mention = new UserPhotoMention();
                    $mention->user_id = $user->user_id;
                    $mention->photo_id = $photo->photo_id;
                    $mention->save();

                    if($user->user_id != \Auth::user()->user_id)
                        $user->notify(new UserPhotoMentionNotification(\Auth::user(), $photo));
                }
            }
        }
    }

    /**
     * Detect hashtags in the photo caption
     *
     * @param Photo $photo
     */
    private function handlePhotoHashtags(Photo $photo)
    {
        $hashtags = Expression::parseText($photo->caption, 'hashtags');

        foreach($hashtags as $word)
        {
            if(!$hashtag = Hashtag::where('hashtag_text', $word)->first())
            {
                $hashtag = new Hashtag();
                $hashtag->hashtag_text = $word;
            }

            $hashtag->hashtag_relevance++;
            $hashtag->save();

            if(!$relation = PhotoHashtag::where([
                'photo_id' => $photo->photo_id,
                'hashtag_id' => $hashtag->hashtag_id])->first())
            {
                $relation = new PhotoHashtag();
                $relation->photo_id = $photo->photo_id;
                $relation->hashtag_id = $hashtag->hashtag_id;
                $relation->save();
            }

        }

    }
}
