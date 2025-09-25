<?php

namespace App\Http\Controllers;

use App\Models\Cuisine;
use Illuminate\Http\Request;

class CuisineController extends Controller
{
    public function getAllCuisine()
    {
        return response()->json(Cuisine::all());
    }

    public function addNewCuisine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:cuisines'
        ]);

        $cuisine = Cuisine::create([
            'name' => $request->name
        ]);
        return response()->json([

            $cuisine
        ]);
    }

    public function getSingleCuisine($id)
    {
        $cuisine = Cuisine::find($id);
        if (!$cuisine) {
            return response()->json([
                'message' => 'Cuisine not found'
            ]);
        }
        return response()->json([
            $cuisine
        ]);
    }
}
