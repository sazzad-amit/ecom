<?php


namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    public function index()
    {
        $sources = Source::all();
        return response()->json($sources);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_name_en' => 'required|string|max:255',
            'source_name_bn' => 'nullable|string|max:255',
            'source_auto_id' => 'required|string|unique:sources,source_auto_id',
            'mobile_no' => 'nullable|string|max:20',
            'details_en' => 'nullable|string',
            'details_bn' => 'nullable|string',
        ]);

        $source = Source::create($validated);

        return response()->json($source, 201);
    }

    public function show(Source $source)
    {
        return response()->json($source);
    }

    public function update(Request $request, Source $source)
    {
        $validated = $request->validate([
            'source_name_en' => 'required|string|max:255',
            'source_name_bn' => 'nullable|string|max:255',
            'source_auto_id' => 'required|string|unique:sources,source_auto_id,' . $source->id,
            'mobile_no' => 'nullable|string|max:20',
            'details_en' => 'nullable|string',
            'details_bn' => 'nullable|string',
        ]);

        $source->update($validated);

        return response()->json($source);
    }

    public function destroy(Source $source)
    {
        $source->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
