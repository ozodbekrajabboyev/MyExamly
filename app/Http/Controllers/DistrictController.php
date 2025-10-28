<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function byRegion($regionId)
    {
        return District::where('region_id', $regionId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }
}
