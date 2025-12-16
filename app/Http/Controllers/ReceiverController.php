<?php

namespace App\Http\Controllers;

use App\Models\Receiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReceiverController extends Controller
{
    public function index(Request $request)
    {
        $query = Receiver::query(); // Changed from Receiver::all() to Receiver::query()

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('receiver_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('receiver_name_bn', 'LIKE', "%{$search}%");
            });
        }

        $receivers = $query->paginate(10);

        return Inertia::render('Receivers/Index', [
            'receivers' => $receivers,
            'filters' => $request->only(['search']),
        ]);
    }
    
    public function create(Request $request)
    {
        // Check if it's an edit request
        $receiver = null;
        if ($request->has('id')) {
            $receiver = Receiver::find($request->input('id'));
        }

        return Inertia::render('Receivers/Create', [
            'receiver' => $receiver, // Pass existing receiver for editing
            'receivers' => Receiver::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_name_en' => 'required|string|max:255',
            'receiver_name_bn' => 'nullable|string|max:255',
            'receiver_auto_id' => 'required|string|unique:receivers,receiver_auto_id',
            'mobile_no' => 'nullable|string|max:20',
            'details_en' => 'nullable|string',
            'details_bn' => 'nullable|string',
            'status' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $validated['status'] = $request->boolean('status', true);
            $validated['created_by'] = auth()->id();

            Receiver::create($validated);

            DB::commit();

            return redirect()->route('receivers.index') // Changed from 'Receivers.index' to 'receivers.index'
                ->with('success', 'Receiver created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create Receiver: ' . $e->getMessage()]);
        }
    }

    public function edit(Receiver $receiver)
    {
        return Inertia::render('Receivers/Create', [ // Use same view for create/edit
            'receiver' => $receiver,
            'receivers' => Receiver::all(),
        ]);
    }

    public function update(Request $request, Receiver $receiver)
    {
        $validated = $request->validate([
            'receiver_name_en' => 'required|string|max:255',
            'receiver_name_bn' => 'nullable|string|max:255',
            'receiver_auto_id' => 'required|string|unique:receivers,receiver_auto_id,' . $receiver->id,
            'mobile_no' => 'nullable|string|max:20',
            'details_en' => 'nullable|string',
            'details_bn' => 'nullable|string',
            'status' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $validated['status'] = $request->boolean('status', true);
            $validated['updated_by'] = auth()->id();

            $receiver->update($validated);

            DB::commit();

            return redirect()->route('receivers.index') // Changed from 'Receivers.index' to 'receivers.index'
                ->with('success', 'Receiver updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update Receiver: ' . $e->getMessage()]);
        }
    }

    public function destroy(Receiver $receiver)
    {
        $receiver->delete();
        return redirect()->route('receivers.index')
            ->with('success', 'Receiver deleted successfully');
    }

    public function show(Receiver $receiver)
    {
        return response()->json($receiver);
    }
}
