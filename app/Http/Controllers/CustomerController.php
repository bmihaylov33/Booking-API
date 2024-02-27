<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Customers",
 *     description="Endpoints for managing customers"
 * )
 */
class CustomerController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Retrieve all customers",
     *     tags={"Customers"},
     *     @OA\Response(response="200", description="List of customers"),
     * )
     */
    public function index()
    {
        $customers = Customer::all();
        return response()->json(['data' => $customers]);
    }

    /**
     * Create a new customer.
     *
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Create a new customer",
     *     tags={"Customers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "phone_number"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone_number", type="string", example="123456789"),
     *         )
     *     ),
     *     @OA\Response(response="201", description="Customer created successfully", @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="data", type="object",
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="name", type="string", example="John Doe"),
    *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
    *             @OA\Property(property="phone_number", type="string", example="123456789"),
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
                'name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:customers'],
                'phone_number' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if (!preg_match('/^\d{10}$/', $phoneNumber)) {
                return response()->json(['error' => 'Invalid phone number provided.'], 400);
            }

            $customer = Customer::create($request->all());

            return response()->json(['data' => $customer, 'message' => 'Customer created successfully.'], 201);
        } else {
            return response()->json([
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }
}
