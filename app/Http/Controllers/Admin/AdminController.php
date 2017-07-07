<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 5/31/2017
 * Time: 7:48 PM
 */

namespace App\Http\Controllers\Admin;


use App\Helpers\Vision;
use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengePlay;
use App\Models\Hashtag;
use App\Models\ObjectCategory;
use App\Models\ObjectWord;
use App\Models\Photo;
use App\Models\PlayObject;
use Illuminate\Http\Request;
use SplFileObject;

class AdminController extends Controller
{
    public function seedWordWords()
    {
        ini_set('max_execution_time', 6180);
        $file = new SplFileObject(storage_path('/app/wordnet/words.txt'));
        while (!$file->eof())
        {
            $data = explode('	', $file->fgets());


                $word = new ObjectWord();
                $word->object_wnid = $data[0];
                $word->object_words = $data[1];
                $word->save();

        }
        $file = null;
        return  "done";
    }

    public function seedWordNet()
    {
        ini_set('max_execution_time', 6180);
        $file = new SplFileObject(storage_path('/app/wordnet/wordnet.is_a.txt'));

        while (!$file->eof())
        {
            $words = explode(' ', str_replace('\n', '', $file->fgets()));
            $parent_word = $words[0];
            $child_word = $words[1];


            $parent = new ObjectCategory();
            $parent->category_wnid = $child_word;
            $parent->parent_wnid = $parent_word;
            $parent->save();

        }

        $file = null;
        return  "done";
    }

    public function index()
    {
        return view('admin.pages.dashboard')->with(['pageTitle' => 'Selfy administration']);
    }

    public function createPlay()
    {
        return view('admin.pages.createPlaySingleton')->with(['pageTitle' => 'Create Play challenge']);
    }

    public function createPlaySingleton(Request $request)
    {
        $values = $request->only(['play_title', 'play_description', 'play_sample', 'play_thumb']);

        $this->validate($request, [
            'play_title' => 'required',
            'play_description' => 'required',
            'play_sample' => 'required',
            'play_thumb' => 'required'
        ]);

        $play = new ChallengePlay();
        $play->play_title = $values['play_title'];
        $play->play_description = $values['play_description'];
        $play->play_sample = $values['play_sample'];
        $play->play_thumb = $values['play_thumb'];
        $play->save();

        $challenge = new Challenge();
        $challenge->object_type = config('constants.CHALLENGE_TYPES_STR.PLAY');
        $challenge->object_id   = $play->play_id;
        $challenge->status = config('constants.DEV_CHALLENGE_STATUS.active');
        $challenge->completed_count = 0;
        $challenge->save();

        $message = (object) [
            'title' => 'Done',
            'body' => 'Play challenge created',
            'type' => 'success',
            'duration' => 5000
        ];

        return view('admin.pages.playSingleton')
            ->with(['pageTitle' => 'Play challenge', 'challenge' => $challenge, 'message' => $message]);
    }

    public function play()
    {
        $challenges = Challenge::where('object_type', config('constants.CHALLENGE_TYPES_STR.PLAY'))
            ->paginate(15);

        return view('admin.pages.play')->with(['pageTitle' => 'Play challenges',
            'challenges' => $challenges]);
    }

    public function playSingleton($playId = 0)
    {
        if($challenge = Challenge::with('Object')->find($playId))
        {
            return view('admin.pages.playSingleton')
                ->with(['pageTitle' => 'Play challenge', 'challenge' => $challenge, 'message' => false]);
        }

        else
            abort(404);

        return false;
    }

    public function updatePlaySingleton(Request $request, $playId)
    {
        $values = $request->only(['play_title', 'play_description', 'play_sample', 'play_thumb']);

        $this->validate($request, [
            'play_title' => 'required',
            'play_description' => 'required',
            'play_sample' => 'required',
            'play_thumb' => 'required'
        ]);

        if($challenge = Challenge::with('Object')->find($playId))
        {
            $challenge->Object->update($values);
            $challenge->Object->save();
            $message = (object) [
                'title' => 'Done',
                'body' => 'Play data updated',
                'type' => 'success',
                'duration' => 5000
            ];

            return view('admin.pages.playSingleton')
                ->with(['pageTitle' => 'Play challenge', 'challenge' => $challenge, 'message' => $message]);
        }
        else
            abort(404);

        return false;
    }

    public function updateChallengeStatusSingleton(Request $request)
    {
        $values = $request->only(['currentStatus', 'challengeId']);

        if ($challenge = Challenge::find($values['challengeId']))
        {
            $challenge->status = !$values['currentStatus'];
            $challenge->save();

            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
    }

    public function createHashtag(Request $request)
    {
        $values = $request->only(['hashtag_group', 'hashtag_status', 'hashtag_text']);

        if(!$hashtag = Hashtag::where('hashtag_text', $values['hashtag_text'])->first()){
            $hashtag = new Hashtag();
            $hashtag->hashtag_text = str_replace('#', '', $values['hashtag_text']);
            $hashtag->hashtag_group = filter_var($values['hashtag_group'], FILTER_VALIDATE_BOOLEAN)  ? 1 : 0;
            $hashtag->hashtag_status = filter_var($values['hashtag_status'], FILTER_VALIDATE_BOOLEAN)  ? 1 : 0;
            $hashtag->save();

            return response()->json(['status' => true, 'id' => $hashtag->hashtag_id]);
        }
        return response()->json(['status' => false]);
    }

    public function updatePlayHashtagSingleton(Request $request)
    {
        $values = $request->only(['play_id', 'hashtag_id']);

        if(($play = ChallengePlay::find($values['play_id'])) && Hashtag::find($values['hashtag_id']))
        {
            $play->play_hashtag  = $values['hashtag_id'];
            $play->save();
            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
    }

    public function managePlayObjects($playId = 0)
    {
        if($challenge = Challenge::with('Object')->find($playId))
        {
            return view('admin.pages.playSingletonObjects')
                ->with(['pageTitle' => 'Manage objects', 'challenge' => $challenge, 'message' => false]);
        }
        else
            abort(404);

        return false;
    }

    /**
     * @param Request $request
     * @called Ajax
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeObjectAssociation(Request $request)
    {
        $values = $request->only(['play_id', 'object_id']);

        if($association = PlayObject::where(['play_id' => $values['play_id'], 'category_id' => $values['object_id']])->first())
        {
            $association->delete();
            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
    }

    public function createPlayObject(Request $request)
    {

        $values = $request->only(['object_name', 'object_parent']);

        if(!$object = ObjectCategory::where('category_name', $values['object_name'])->first())
        {
            $object = new ObjectCategory();
            $object->category_parent = $values['object_parent'] >  0 ? $values['object_parent'] : null;
            $object->category_name = $values['object_name'];
            $object->save();

            return response()->json(['status' => true, 'id' => $object->category_id]);
        }
        return response()->json(['status' => false, 'message' => 'Already exists']);
    }
    
    public function associatePlayObject(Request $request)
    {
        $values = $request->only(['object_id',  'play_id',  'object_name']);

        if($object = ObjectCategory::where('category_id', $values['object_id'])->first())
        {
            if(!$association = PlayObject::where(['play_id' => $values['play_id'], 'category_id' => $values['object_id']])->first())
            {
                $assoc = new PlayObject();
                $assoc->play_id = $values['play_id'];
                $assoc->category_id = $values['object_id'];
                $assoc->save();

                return response()->json(['status' => true]);
            }
        }

        elseif(isset($values['object_name']) && !empty($values['object_name']))
        {
            $object = new ObjectCategory();
            $object->category_parent = null;
            $object->category_name = $values['object_name'];
            $object->save();

            $assoc = new PlayObject();
            $assoc->play_id = $values['play_id'];
            $assoc->category_id = $object->category_id;
            $assoc->save();

            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
    }
    
    public function playGenerateObjects(Request $request, $playId = 0)
    {
        ini_set('max_execution_time', 180);
        $images = explode(',', $request->get('image_sample'));
        $collection = collect([]);
        $saved = [];

        foreach($images as $image)
        {
            $photo = new Photo();
            $photo->url = $image;
            $response = Vision::recognize($photo);

            switch ($response->getStatusCode()) {
                case 200:
                    $content = \GuzzleHttp\json_decode($response->getBody()->getContents());
                    if($content->status) {

                        $words = $content->content;

                        foreach ($words as $word)
                        {
                            if(!in_array($word, $saved)) {
                                $exists = ObjectCategory::where('category_name', $word)->first();
                                $obj = new ObjectCategory();
                                $obj->category_name = $word;
                                $obj->category_id = $exists!== null ? $exists->category_id : '-';
                                $obj->exists =  $exists!== null;
                                $obj->associated =  $exists!== null ? PlayObject::where(['category_id' => $exists->category_id, 'play_id' => $playId])->first() !== null : false;

                                $saved[] = $word;
                                $collection->push($obj);
                            }
                        }
                    }
                    break;
            }
        }
        $challenge = Challenge::with('Object')->where(['object_type' => config('constants.CHALLENGE_TYPES_STR.PLAY'), 'object_id' => $playId])->first();

        return view('admin.pages.playSingletonObjects')
            ->with(['pageTitle' => 'Manage objects', 'challenge' => $challenge,
                'message' => false, 'objectsGen' => $collection]);
    }

}