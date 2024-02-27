<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="Endpoints for managing payments"
 * )
 */
class PaymentController extends Controller
{

    /**
     * Record a payment against a booking.
     *
     * @OA\Post(
     *     path="/api/payments",
     *     summary="Record a payment against a booking",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"booking_id", "amount", "payment_date", "status"},
     *             @OA\Property(property="booking_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=50.25),
     *             @OA\Property(property="payment_date", type="string", format="date-time", example="2022-02-25T14:00:00Z"),
     *             @OA\Property(property="status", type="string", example="success"),
     *         )
     *     ),
     *     @OA\Response(response="201", description="Payment recorded successfully", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="booking_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=50.25),
     *             @OA\Property(property="payment_date", type="string", format="date-time", example="2022-02-25T14:00:00Z"),
     *             @OA\Property(property="status", type="string", example="success"),
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
                'booking_id' => ['required', 'exists:bookings,id'],
                'amount' => ['required', 'numeric'],
                'payment_date' => ['required', 'date'],
                'status' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $payment = Payment::create($request->all());

            return response()->json(['data' => $customer, 'message' => 'Payment created successfully.'], 201);
        } else {
            return response()->json([
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }
}
