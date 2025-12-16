<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Source::query(); // Changed from Source::all() to Source::query()

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('source_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('source_name_bn', 'LIKE', "%{$search}%");
            });
        }

        $sources = $query->paginate(10);

        return Inertia::render('Sources/Index', [
            'sources' => $sources,
            'filters' => $request->only(['search']),
        ]);
    }
    
    public function create(Request $request)
    {
        // Check if it's an edit request
        $source = null;
        if ($request->has('id')) {
            $source = Source::find($request->input('id'));
        }

        return Inertia::render('Sources/Create', [
            'source' => $source, // Pass existing source for editing
            'sources' => Source::all(),
        ]);
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
            'status' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $validated['status'] = $request->boolean('status', true);
            $validated['created_by'] = auth()->id();

            Source::create($validated);

            DB::commit();

            return redirect()->route('sources.index') // Changed from 'Sources.index' to 'sources.index'
                ->with('success', 'Source created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create Source: ' . $e->getMessage()]);
        }
    }

    public function edit(Source $source)
    {
        return Inertia::render('Sources/Create', [ // Use same view for create/edit
            'source' => $source,
            'sources' => Source::all(),
        ]);
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
            'status' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $validated['status'] = $request->boolean('status', true);
            $validated['updated_by'] = auth()->id();

            $source->update($validated);

            DB::commit();

            return redirect()->route('sources.index') // Changed from 'Sources.index' to 'sources.index'
                ->with('success', 'Source updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update Source: ' . $e->getMessage()]);
        }
    }

    public function destroy(Source $source)
    {
        $source->delete();
        return redirect()->route('sources.index')
            ->with('success', 'Source deleted successfully');
    }

    public function show(Source $source)
    {
        return response()->json($source);
    }
}