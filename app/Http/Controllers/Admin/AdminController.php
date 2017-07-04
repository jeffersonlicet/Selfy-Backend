<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 5/31/2017
 * Time: 7:48 PM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengePlay;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{
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
        $values = $request->only(['play_title', 'play_description', 'play_sample']);

        $this->validate($request, [
            'play_title' => 'required',
            'play_description' => 'required',
            'play_sample' => 'required'
        ]);

        $play = new ChallengePlay();

        $message = (object) [
            'title' => 'Done',
            'body' => 'Play data updated',
            'type' => 'success',
            'duration' => 5000
        ];
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
        $values = $request->only(['play_title', 'play_description', 'play_sample']);

        $this->validate($request, [
            'play_title' => 'required',
            'play_description' => 'required',
            'play_sample' => 'required'
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
            $hashtag->hashtag_text = $values['hashtag_text'];
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

}