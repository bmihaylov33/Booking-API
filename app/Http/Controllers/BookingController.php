<?php

namespace App\Http\Controllers;

use App\Events\CancelBookingEvent;
use App\Events\NewBookingEvent;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Bookings",
 *     description="Endpoints for managing bookings"
 * )
 */
class BookingController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/bookings",
     *     summary="Retrieve all bookings",
     *     tags={"Bookings"},
     *     @OA\Response(response="200", description="List of bookings"),
     * )
     */
    public function index()
    {
        $bookings = Booking::all();
        return response()->json(['data' => $bookings]);
    }


    /**
     * Create a new booking.
     *
     * @OA\Post(
     *     path="/api/bookings",
     *     summary="Create a new booking",
     *     tags={"Bookings"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"room_id", "customer_id", "check_in_date", "check_out_date", "total_price"},
     *             @OA\Property(property="room_id", type="integer", example=1),
     *             @OA\Property(property="customer_id", type="integer", example=2),
     *             @OA\Property(property="check_in_date", type="string", format="date-time", example="2022-02-25T10:00:00Z"),
     *             @OA\Property(property="check_out_date", type="string", format="date-time", example="2022-02-28T12:00:00Z"),
     *             @OA\Property(property="total_price", type="number", format="float", example=150.75),
     *         )
     *     ),
     *     @OA\Response(response="201", description="Booking created successfully", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="room_id", type="integer", example=1),
     *             @OA\Property(property="customer_id", type="integer", example=2),
     *             @OA\Property(property="check_in_date", type="string", format="date-time", example="2022-02-25T10:00:00Z"),
     *             @OA\Property(property="check_out_date", type="string", format="date-time", example="2022-02-28T12:00:00Z"),
     *             @OA\Property(property="total_price", type="number", format="float", example=150.75),
     *         )
     *     )),
     *     @OA\Response(response="422", description="Validation errors"),
     * )
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'room_id' => ['required', 'exists:rooms,id'],
                'customer_id' => ['required', 'exists:customers,id'],
                'check_in_date' => ['required', 'date'],
                'check_out_date' => ['required', 'date', 'after:check_in_date'],
                'total_price' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $room = Room::find($request->input('room_id'));
            if ($room->status !== 'available') {
                return response()->json(['error' => 'Room is not available.'], 400);
            }

            $checkInDate = Carbon::parse($checkInDate);
            $checkOutDate = Carbon::parse($checkOutDate);
            $daysDifference = $checkOutDate->diffInDays($checkInDate);

            if (
                ($daysDifference * $room->price_per_night)
                !== $request->input('total_price')
            ) {
                return response()->json(['error' => 'Not enough money.'], 400);
            }

            $booking = Booking::create($request->all());

            if (!$booking) {
                return response()->json(['error' => 'Booking failed. Try again.'], 400);
            }

            $room->status = 'occupied';
            $room->save();

            event(new NewBookingEvent($newBooking));

            return response()->json(['message' => 'Successful booking.'], 201);
        } else {
            return response()->json([
                'message' => 'Failed to authenticate.',
            ], 401);
        }

        return response()->json(['data' => $booking, 'message' => 'Booking created successfully.'], 201);
    }

    /**
     * Cancel a booking.
     *
     * @OA\Delete(
     *     path="/api/bookings/{id}",
     *     summary="Cancel a booking",
     *     tags={"Bookings"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the booking to be canceled",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(response="200", description="Booking canceled successfully", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="message", type="string", example="Booking canceled successfully"),
     *     )),
     *     @OA\Response(response="401", description="Failed to authenticate", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="message", type="string", example="Failed to authenticate."),
     *     )),
     *     @OA\Response(response="404", description="Booking not found", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="message", type="string", example="Booking not found"),
     *     )),
     * )
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function cancelBooking($id)
    {
        if (Auth::user()) {
            $booking = Booking::findOrFail($id);
            $booking->delete();

            event(new CancelBookingEvent($booking));

            return response()->json(['message' => 'Booking canceled successfully']);
        } else {
            return response()->json([
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }
}
