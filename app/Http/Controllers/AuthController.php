<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\UserRegistered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints for user authentication"
 * )
 */
class AuthController extends Controller
{

    /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *         )
     *     ),
     *     @OA\Response(response="201", description="User registered successfully", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="token", type="string", example="your_generated_token")
     *     )),
     *     @OA\Response(response="422", description="Validation errors"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        event(new UserRegistered($user));

        $token = $user->createToken('BookingAuthToken')->accessToken;

        return response()->json([
            'token' => $token['token'],
        ], 201);
    }


    /**
     * Login an existing user.
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login an existing user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *         )
     *     ),
     *     @OA\Response(response="200", description="Login successful", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="token", type="string", example="your_generated_token")
     *     )),
     *     @OA\Response(response="401", description="Failed to authenticate"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $token = auth()->user()
                ->createToken('BookingAuthToken')->accessToken;

            return response()->json([
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to authenticate.',
            ], 401);
        }
        return response()->json(['error' => 'Wrong credentials!'], 401);
    }


    /**
     * Logout the authenticated user.
     *
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *     tags={"Authentication"},
     *     @OA\Response(response="200", description="Logged out successfully"),
     *     @OA\Response(response="401", description="Failed to authenticate"),
     * )
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        if (Auth::user()) {
            $request->user()->token()->revoke();

            return response()->json([
                'message' => 'Logged out successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }
}
