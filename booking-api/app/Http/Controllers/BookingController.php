<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $booking = $request->user()->bookings()->orderBy('starts_at')->get();

        return response()->json($booking, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $data = $request->validate([
            'room_name' => 'required|string|max:100',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'note' => 'nullable|string|max:500',
        ]);

        $booking = $request->user()->bookings()->create($data);

        return response()->json($booking, 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Booking $booking): JsonResponse
    {
         if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Доступ запрещён.'], 403);
        }

        return response()->json($booking);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'room_name' => 'sometimes|string|max:100',
            'starts_at' => 'sometimes|date|after:now',
            'ends_at' => 'sometimes|date|after:starts_at',
            'note' => 'nullable|string|max:500',
        ]);

        $conflict = Booking::where('room_name', $data['room_name'] ?? $booking->room_name)
            ->where('id', '!=', $booking->id)
            ->where(function ($query) use ($data, $booking) {
                $start = $data['starts_at'] ?? $booking->starts_at;
                $end = $data['ends_at'] ?? $booking->ends_at;

                $query->where('starts_at', '<', $end)
                    ->where('ends_at', '>', $start);
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Комната занята в это время'
            ], 409);
        }

        $booking->update($data);

        // dd($booking);
        return response()->json($booking, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Booking $booking): JsonResponse
    {
        $booking->delete();
        return response()->json(["message" => "Встреча отменена"], 200);
    }
}
