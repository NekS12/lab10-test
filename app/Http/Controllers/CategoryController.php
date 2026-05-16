<?php

namespace App\Http\Controllers;

use App\Models\CreativityType;
use App\Models\MasterClass;

class CategoryController extends Controller
{
    public function show($id)
    {
        $type = CreativityType::findOrFail($id);

        $classes = MasterClass::where('type_id', $id)
            ->where('date', '>=', now())
            ->with(['instructor', 'type'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('category', compact('type', 'classes'));
    }
}
