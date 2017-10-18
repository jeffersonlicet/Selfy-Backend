<?php

namespace App\Http\Controllers\Admin;

use App\Mail\MissMail;
use Carbon\Carbon;
use Exception;
use App;
use App\Models\User;
use DB;
use Hash;
use App\Models\Photo;
use App\Models\Place;
use Mail;
use Storage;
use Vision;
use App\Models\Hashtag;
use App\Models\Challenge;
use App\Models\PlayObject;
use Illuminate\Http\Request;
use App\Models\ChallengePlay;
use App\Models\TargetProduct;
use App\Models\ObjectCategory;
use App\Models\ProductStorage;
use App\Http\Controllers\Controller;
class AdminController extends Controller
{
    public function missu($page)
    {
        $limit = 300;
        $offset = $page*$limit;
        $users = User::limit($limit)->offset($offset+50)->orderBy('user_id', 'DESC')->get();

        foreach($users as $user)
        {
            Mail::to($user)->queue(new MissMail($user->firstname));
        }

        print("Done");
    }
    public function sendEmails()
    {
        Mail::to(\Auth::user())->queue(new MissMail(\Auth::user()->firstname));
    }
    public function seedPhotos()
    {

        //print(Hash::make(sha1(strtolower('jefferson.licet@studentpartner.com') . Hash::make($input['password'])));
        /*ini_set('max_execution_time', 0);
        $base = file(storage_path('app/newusers.tsv'));
        $users = [];
        $s = 0;

        $output = fopen(storage_path('app/photosOutput.tsv'), 'w');
        print(count($base) . " total users");
        foreach($base as $line)
        {
            $data = explode("\t", $line);
            if($data[39] > 0)
                $users[$data[39]] = $data[0];
        }

        $photosBase = file(storage_path('app/photos.tsv'));
        foreach($photosBase as $line)
        {
            $old = explode("\t", $line);

            $photo = [];

            if(isset($users[$old[1]]))
            {
                $photo[0] = $users[$old[1]];
                $insert = str_replace("\r", "", str_replace("\\", "", str_replace("\n", "", str_replace("'", "", trim($old[3])))));
                $photo[1] = empty( $insert) ? "" :  json_encode($insert);
                $photo[2] = $old[4];
                $photo[3] = $old[6] != null ? Carbon::createFromTimestamp($old[6])->toDateTimeString() : $old[6];
                $photo[4] = $old[0];
                fwrite($output, implode("\t", $photo).PHP_EOL);
                $s++;
            } else {
                print( $old[4] . "</ br>");
            }



        }

        fclose($output);
        print($s. " photos saved");*/
    }
    public function seedFollowers($page)
    {
        ini_set('max_execution_time', 0);
        $base = file(storage_path('app/newusers.tsv'));

        $file = file(storage_path('app/followers.tsv'));
        $output = fopen(storage_path('app/followersOut.tsv'), 'w');
        $max = 90000000;
        $i = 0;
        $s = 0;
        $baseID = 241;

        $offset = $page*$max;
        $users =[];
        foreach($base as $line)
        {
            $data = explode("\t", $line);
            if($data[39] > 0)
                $users[$data[39]] = $data[0];
        }


        foreach($file as $line)
        {
            if($i < $offset) {
                $i++;
             continue;
            }

            if($s == $max)
                break;

            $baseID++;
            $data = explode("\t", $line);
            $insert = [];
            if(isset($users[$data[1]]) && isset($users[$data[2]]))
            {
                $insert[0] = $users[$data[1]];
                $insert[1] = $users[$data[2]];
                $string = implode("\t", $insert).PHP_EOL;
                fwrite($output, $string);
                $s++;
            }
        }

        fclose($output);
        print($s. " followers saved");
    }
    public  function mssql_escape($unsafe_str)
    {
        if (get_magic_quotes_gpc())
        {
            $unsafe_str = stripslashes($unsafe_str);
        }
        return $escaped_str = str_replace("'", "''", $unsafe_str);
    }
    public function oldUsersSeeder($page)
    {
        ini_set('max_execution_time', 0);
        $file = file(storage_path('app/data.tsv'));
        $output = fopen(storage_path('app/usersCrude2.tsv'), 'w');
        $max = 400000;
        $i = 0;
        $s = 0;
        $baseID = 311860;

        $offset = $page*$max;
        $limit = $max;

        foreach($file as $line)
        {
            if($i < $offset) {
                $i++;
                continue;
            }

            if($s == $max)
                break;

            $baseID++;
            $data = explode("\t", $line);
            $email = $data[17];
            $avatar = $data[18];
            $id = $data[0];
            $nombre  = str_replace("'", "", htmlentities($data[9]));
            $acerca = str_replace("'", "", htmlentities($data[26]));
            $uri = $data[37];
            $pass = $data[16];

            $pattern = ['-', '.'];
            $patternReplace = ['1', '2'];
            $username = strtolower(str_replace($pattern, $patternReplace, explode('@', $email)[0])).str_random(4);
            $new = new User();

            if(str_contains($avatar, 'imgur'))
                $new->avatar = $avatar;

            $new->email = $email;

            if(!empty(trim($nombre)))
                $new->firstname = trim($nombre);

            if(!empty(trim($acerca))){
                $new->bio = str_limit(trim($acerca), 250);
            } else $new->bio = "";

            if(!empty(trim($uri)))
                $new->wp_token = trim($uri);

            $new->password = Hash::make($pass);
            $new->password_type = config('constants.APP_PLATFORMS.wp');
            $new->original_platform = config('constants.APP_PLATFORMS.wp');
            $new->user_locale = App::getLocale();

            $new->username = $username;
            $new->old_user_id = $id;

            $insertion = [];
            $insertion[0] = $baseID;
            $insertion[1]= $new->username;
            $insertion[2]= $new->email;
            $insertion[3]= $new->password;
            $insertion[4]= $new->firstname;
            $insertion[5]= $new->bio;
            $insertion[6] = 'es';
            $insertion[7] = $new->avatar;
            $insertion[8] =  $new->original_platform;
            $insertion[9] =  $new->old_user_id;
            $insertion[10] =  $new->wp_token;
            $insertion[11] =  config('constants.APP_PLATFORMS.wp');

            $string = implode("\t", $insertion).PHP_EOL;
            fwrite($output, $string);

            $s++;


        }

        fclose($output);
        print($s. " users saved");


        /*ini_set('max_execution_time', 0); //3 minutes
        $max = 20000;


        $users = DB::connection('old')
            ->table('usuarios_foodgram')
            ->offset($page*$max)
            ->limit($max)
            ->orderBy("id", "ASC")
            ->get();

            foreach($users as $user)
            {
                $pattern = ['-', '.'];
                $patternReplace = ['1', '2'];
                $username = strtolower(str_replace($pattern, $patternReplace, explode('@', $user->email)[0])).str_random(4);

                $new = new User();

                    if(str_contains($user->avatar, 'imgur'))
                        $new->avatar = $user->avatar;

                    $new->email = $user->email;

                    if(!empty(trim($user->nombre)))
                        $new->firstname = trim($user->nombre);

                    if(!empty(trim($user->acerca)))
                        $new->bio = str_limit(trim($user->acerca), 250);

                    if(!empty(trim($user->uri)))
                        $new->wp_token = trim($user->uri);

                    $new->password = Hash::make($user->pass);
                    $new->password_type = config('constants.APP_PLATFORMS.wp');
                    $new->original_platform = config('constants.APP_PLATFORMS.wp');
                    $new->user_locale = App::getLocale();

                    $new->username = $username;
                    $new->old_user_id = $user->id;

                try {
                    $new->save();
                } catch(Exception $ex) {
                    echo $ex->getMessage()."<br/>";
                }

            }*/

    }
    public function seedLikes()
    {
        ini_set('max_execution_time', 0);
        $s = 0;
        $users_source = file(storage_path('app/newusers.tsv'));
        $users = [];
        foreach($users_source as $line)
        {
            $data = explode("\t", $line);
            if($data[39] > 0)
                $users[$data[39]] = $data[0];
        }

        $photos_source = file(storage_path('app/newphotos.tsv'));
        $photos = [];
        foreach($photos_source as $line)
        {
            $data = explode("\t",  $line);
            $data[13] = str_replace("\n", '', $data[13]);

            if($data[13] > 0)
                $photos[$data[13]] = $data[0];
        }

        $likes_source = file(storage_path('app/selfy_data_big_dbo_fj_likes_foodgram.tsv'));

        $output = fopen(storage_path('app/likes_out.tsv'), 'w');
        foreach($likes_source as $line)
        {
            $data = explode("\t", $line);
            $oldUser = $data[1];
            $oldPhoto = $data[2];

            if(isset($users[$oldUser]) && isset($photos[$oldPhoto]))
            {
                $insert[0] = $users[$oldUser];
                $insert[1] = $photos[$oldPhoto];

                $write = implode("\t", $insert).PHP_EOL;
                fwrite($output, $write);
                $s++;
            }
        }

        fclose($output);
        print($s. " likes saved");
    }
    public function seedComments()
    {
        ini_set('max_execution_time', 0);
        $s = 0;
        $users_source = file(storage_path('app/newusers.tsv'));
        $users = [];
        foreach($users_source as $line)
        {
            $data = explode("\t", $line);
            if($data[39] > 0)
                $users[$data[39]] = $data[0];
        }

        $photos_source = file(storage_path('app/newphotos.tsv'));
        $photos = [];
        foreach($photos_source as $line)
        {
            $data = explode("\t",  $line);
            $data[13] = str_replace("\n", '', $data[13]);

            if($data[13] > 0)
                $photos[$data[13]] = $data[0];
        }

        $comments_source = file(storage_path('app/selfy_data_big_dbo_fj_coments_foodgram.tsv'));

        $output = fopen(storage_path('app/comments_out.tsv'), 'w');
        foreach($comments_source as $line)
        {
            $data = explode("\t", $line);

            $oldUser = $data[1];
            $oldPhoto = $data[2];

            if(isset($users[$oldUser]) && isset($photos[$oldPhoto]))
            {
                $insert[0] = $users[$oldUser];
                $insert[1] = $photos[$oldPhoto];
                $comment = $data[3];
                $date = $data[4];

                $comment = str_replace("\r", "", str_replace("\\", "", str_replace("\n", "", str_replace("'", "", trim( $comment)))));
                $comment = empty($comment) ? "" :  json_encode($comment);
                $date = $date != null ? Carbon::createFromTimestamp($date)->toDateTimeString() : $date;
                $insert[2] = str_replace('"', '', $comment);
                $insert[3] = $date;

                $write = implode("\t", $insert).PHP_EOL;
                fwrite($output, $write);
                $s++;
            }
        }

        fclose($output);
        print($s. " comments saved");
    }

    public function index()
    {
        return view('admin.pages.dashboard')->with(['pageTitle' => 'Selfy administration']);
    }

    public function createPlay()
    {
        return view('admin.pages.createPlaySingleton')->with(['pageTitle' => 'Create Play challenge', 'languages' => config('lang-detector.languages')]);
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

    public function places()
    {
        $places = Place::where('status', config('constants.PLACE_STATUS.normal'))
            ->orderBy('created_at', 'DESC')->paginate(15);

        return view('admin.pages.places')->with(['pageTitle' => 'Normal Places',
            'places' => $places]);
    }

    public function createSpot(Request $request)
    {
        $values = $request->only(['place_id']);
        if($place = Place::find($values['place_id']))
        {
            if(!Challenge::where(['object_type'=> config('constants.CHALLENGE_TYPES_STR.SPOT'), 'object_id' => $values['place_id']])->first())
            {
                $challenge = new Challenge();
                $challenge->object_type = config('constants.CHALLENGE_TYPES_STR.SPOT');
                $challenge->object_id = $values['place_id'];
                $challenge->save();

                $place->status = config('constants.PLACE_STATUS.challenge');
                $place->save();

                return response()->json(['status' => true]);
            }
        }

        return response()->json(['status' => false]);
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

    public function associatePlayObject(Request $request)
    {
        $values = $request->only(['object_id',  'play_id']);

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
        return response()->json(['status' => false]);
    }
    
    public function playGenerateObjects(Request $request, $playId = 0)
    {
        ini_set('max_execution_time', 180);
        $images = explode(',', $request->get('image_sample'));
        $collection = [];
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
                            $objects = ObjectCategory::with('Parent')->where('category_wnid', $word)->get();

                            foreach($objects as $exists)
                            {
                                $tempCollection = [];

                                $exists->associated =  PlayObject::where(['category_id' => $exists->category_id, 'play_id' => $playId])->first() !== null;
                                $tempCollection[] = $exists;

                                $parent = $exists->Parent;

                                while($parent != null)
                                {
                                    $parent->associated =  PlayObject::where(['category_id' => $exists->Parent->category_id, 'play_id' => $playId])->first() !== null;
                                    $tempCollection[] = $parent;
                                    $parent = $parent->Parent;
                                }

                                $collection[] = array_reverse($tempCollection);

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

    public function meliDashboard()
    {
        $targets = TargetProduct::paginate(15);
        return view('admin.pages.product_targets')->with(['targets'=> $targets, 'pageTitle' => 'Palabras clave']);
    }
    public function meliCreateTargetForm()
    {
        return view('admin.pages.createTargetSingleton')->with(['pageTitle' => 'Nueva palabra clave']);

    }
    public function meliCreateTarget(Request $request)
    {
        $values = $request->only(['name']);

        if(!$target = TargetProduct::where('name', $values['name'])->first())
        {
            $target = new TargetProduct();
            $target->name = $values['name'];
            $target->save();
        }

        $targets = TargetProduct::paginate(15);
        return view('admin.pages.product_targets')->with(['targets'=> $targets, 'pageTitle' => 'Palabras clave']);
    }

    public function meliProducts($targetId)
    {
        $items = ProductStorage::where('target_id', $targetId)->orderBy('created_at', 'DESC')->orderBy('found', 'ASC')
        ->paginate(50);

        return view('admin.pages.products')->with(['products'=> $items, 'pageTitle' => 'Productos']);

    }
}