<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::where('enable', true)->get();
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
            'name'          => 'required|max:255',
            'description'   => 'required',
            'enable'        => 'required|boolean',
            'categories'    => 'required|array',
            'images'        => 'required|array',
        ]);

        $product = new Product;
        $product->name = $validated['name'];
        $product->description = $validated['description'];
        $product->enable = $validated['enable'];
        $product->save();
        if ($validated['categories']) {
            foreach ($validated['categories'] as $categoryId) {
                $product->categories()->attach($categoryId);
            }
        }
        if ($validated['images']) {
            foreach ($validated['images'] as $imageId) {
                $product->images()->attach($imageId);
            }
        }
        return response()->json(
            [
                'status' => 'OK',
                'data'   => $product,
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
        return Product::with(['images', 'categories'])->findOrFail($id);
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
            'name'          => 'filled|max:255',
            'description'   => 'filled',
            'enable'        => 'filled|boolean',
            'categories'    => 'filled|array',
            'images'        => 'filled|array',
        ]);

        try {
            $product = Product::findOrFail($id);
            if (array_key_exists('name', $validated)) $product->name = $validated['name'];
            if (array_key_exists('description', $validated)) $product->description = $validated['description'];
            if (array_key_exists('enable', $validated)) $product->enable = $validated['enable'];
            $product->save();

            if (array_key_exists('categories', $validated)) {
                $product->categories()->detach();
                foreach ($validated['categories'] as $categoryId) {
                    $product->categories()->attach($categoryId);
                }
            }

            if (array_key_exists('images', $validated)) {
                $product->images()->detach();
                foreach ($validated['images'] as $imageId) {
                    $product->images()->attach($imageId);
                }
            }

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
            $product = Product::findOrFail($id);
            $product->categories()->detach();
            $product->images()->detach();
            $product->delete();
        } catch (Error $e) {
            // empty catch block to prevent id enumeration and maintain idempotency
        } finally {
            return response()->noContent(Response::HTTP_OK);
        }
    }
}
