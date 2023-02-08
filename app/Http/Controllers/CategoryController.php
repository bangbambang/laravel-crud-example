<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::where('enable', true)->get();
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
            'enable'=> 'required|boolean',
        ]);

        $category = new Category;
        $category->name = $validated['name'];
        $category->enable = $validated['enable'];
        $category->save();

        return response()->json(
            [
                'status' => 'OK',
                'data'   => $category,
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
        return Category::findOrFail($id);
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
            'enable'=> 'filled|boolean',
        ]);

        try {
            $category = Category::find($id);
            $category->name = $validated['name'];
            $category->enable = $validated['enable'];
            $category->save();
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
            $category = Category::find($id);
            $category->products()->detach();
            $category->delete();
        } catch (Error $e) {
            // empty catch block to prevent id enumeration and maintain idempotency
        } finally {
            return response()->noContent(Response::HTTP_OK);
        }
    }
}
