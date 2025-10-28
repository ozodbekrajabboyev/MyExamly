<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        return response()->json(Region::all(['id', 'name']));
    }

    public function show($id)
    {
        return Region::with('districts:id,name,region_id')
            ->select('id', 'name')
            ->findOrFail($id);
    }
}
