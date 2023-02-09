<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Image;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Image::where('enable', true)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'name'  => 'required|max:255',
            'file'  => 'required|max:255',
            'enable'=> 'required|boolean',
        ]);

        $image = new Image;
        $image->name = $validated['name'];
        $image->file = $validated['file'];
        $image->enable = $validated['enable'];
        $image->save();

        return response()->json(
            [
                'status' => 'OK',
                'data'   => $image,
            ],
            Response::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Image::findOrFail($id);
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
        $validated = $this->validate($request, [
            'name'  => 'filled|max:255',
            'file'  => 'filled|max:255',
            'enable'=> 'filled|boolean',
        ]);

        try {
            $image = Image::findOrFail($id);
            if (array_key_exists('name', $validated)) $image->name = $validated['name'];
            if (array_key_exists('file', $validated)) $image->file = $validated['file'];
            if (array_key_exists('enable', $validated)) $image->enable = $validated['enable'];
            $image->save();
                return response()->json(
                [
                    'status' => 'OK',
                    'data'   => $validated,
                ],
                Response::HTTP_OK,
            );
        } catch (Error $e) {
            return response()->noContent(Response::HTTP_UNPROCESSABLE_ENTITY);
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
        try {
            $image = Image::findOrFail($id);
            $image->products()->detach();
            $image->delete();
        } catch (Error $e) {
            // empty catch block to prevent id enumeration and maintain idempotency
        } finally {
            return response()->noContent(Response::HTTP_OK);
        }
    }
}
