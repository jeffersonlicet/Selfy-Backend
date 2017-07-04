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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.pages.dashboard')->with(['pageTitle' => 'Selfy administration']);
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
                ->with(['pageTitle' => 'Play challenge', 'challenge' => $challenge, 'updated' => false]);
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

            return view('admin.pages.playSingleton')
                ->with(['pageTitle' => 'Play challenge', 'challenge' => $challenge,'updated' => true]);
        }
        else
            abort(404);

        return false;
    }
}