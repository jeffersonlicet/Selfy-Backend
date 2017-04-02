<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CheckSpot;
use App\Models\Photo;
use Illuminate\Http\Request;
use Validator;

class PhotoController extends Controller
{
    /**
     * Show a list of photos
     *
     * @param int $id
     * @param int $page
     * @param int $limit
     * @return \Illuminate\Http\Response
     */
    public function index($id = 0, $page = 0, $limit = 5)
    {
        if($id == 0)
        {
            $id = \Auth::user()->user_id;
        }

        $photos = Photo::where('user_id', $id);
        var_dump($photos);
        return null;
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
