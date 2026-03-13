<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')->get();

        return response()->json(['categories' => $categories], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:128',
        ]);

        DB::table('categories')->insert([
            'name'       => $data['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Category created'], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $category = DB::table('categories')->where('id', $id)->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['category' => $category], Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:128',
        ]);

        $updated = DB::table('categories')->where('id', $id)->update([
            'name'       => $data['name'],
            'updated_at' => now(),
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Category updated'], Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        $deleted = DB::table('categories')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Category deleted'], Response::HTTP_OK);
    }
}
