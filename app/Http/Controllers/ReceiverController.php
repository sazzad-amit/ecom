<?php

namespace App\Http\Controllers;

use App\Models\Receiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $receivers = Receiver::latest()->get();
        return response()->json($receivers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_name_en' => 'required|string|max:255',
            'receiver_name_bn' => 'nullable|string|max:255',
            'receiver_auto_id' => 'required|string|unique:receivers,receiver_auto_id',
            'mobile_no'        => 'nullable|string|max:20',
            'details_en'       => 'nullable|string',
            'details_bn'       => 'nullable|string',
            'status'           => 'required|boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $receiver = Receiver::create($validated);

        return response()->json([
            'message' => 'Receiver created successfully',
            'data'    => $receiver
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Receiver $receiver)
    {
        return response()->json($receiver);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receiver $receiver)
    {
        $validated = $request->validate([
            'receiver_name_en' => 'required|string|max:255',
            'receiver_name_bn' => 'nullable|string|max:255',
            'receiver_auto_id' => 'required|string|unique:receivers,receiver_auto_id,' . $receiver->id,
            'mobile_no'        => 'nullable|string|max:20',
            'details_en'       => 'nullable|string',
            'details_bn'       => 'nullable|string',
            'status'           => 'required|boolean',
        ]);

        $validated['updated_by'] = Auth::id();

        $receiver->update($validated);

        return response()->json([
            'message' => 'Receiver updated successfully',
            'data'    => $receiver
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receiver $receiver)
    {
        $receiver->delete();

        return response()->json([
            'message' => 'Receiver deleted successfully'
        ]);
    }
}
