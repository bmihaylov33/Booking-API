<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Rooms",
 *     description="Endpoints for managing rooms"
 * )
 */
class RoomController extends Controller
{
    /**
     * Get available rooms.
     *
     * @OA\Get(
     *     path="/api/rooms/available",
     *     summary="Get available rooms",
     *     tags={"Rooms"},
     *     @OA\Response(response="200", description="List of available rooms", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="data", type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="number", type="string", example="101"),
     *             @OA\Property(property="type", type="string", example="Standard"),
     *             @OA\Property(property="price_per_night", type="number", format="float", example=100.50),
     *             @OA\Property(property="status", type="string", example="available"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )),
     *     )),
     * )
     *
     * @return JsonResponse
     */
    public function index()
    {
        if (Auth::user()) {
            $rooms = Room::all();
            return response()->json(['data' => $rooms]);
        } else {
            return response()->json([
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }

    /**
     * Get a specific room.
     *
     * @OA\Get(
     *     path="/api/rooms/{id}",
     *     summary="Get a specific room",
     *     tags={"Rooms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the room",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response="200", description="Room details", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="number", type="string", example="101"),
     *             @OA\Property(property="type", type="string", example="Standard"),
     *             @OA\Property(property="price_per_night", type="number", format="float", example=100.50),
     *             @OA\Property(property="status", type="string", example="available"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         ),
     *     )),
     *     @OA\Response(response="404", description="Room not found"),
     * )
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function getRoom(string $id)
    {
        $room = Room::findOrFail($id);
        return response()->json(['data' => $room]);
    }

    /**
     * Get available rooms.
     *
     * @OA\Get(
     *     path="/api/rooms/available",
     *     summary="Get available rooms",
     *     tags={"Rooms"},
     *     @OA\Response(response="200", description="List of available rooms", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="data", type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="number", type="string", example="101"),
     *             @OA\Property(property="type", type="string", example="Standard"),
     *             @OA\Property(property="price_per_night", type="number", format="float", example=100.50),
     *             @OA\Property(property="status", type="string", example="available"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )),
     *     )),
     * )
     *
     * @return JsonResponse
     */
    public function getAvailableRooms()
    {
        $availableRooms = Room::where('status', 'available')->paginate(5);
        return response()->json(['data' => $availableRooms], 200);
    }

    /**
     * Store a new room.
     *
     * @OA\Post(
     *     path="/api/rooms",
     *     summary="Store a new room",
     *     tags={"Rooms"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"number", "type", "price_per_night", "status"},
     *             @OA\Property(property="number", type="string", example="101"),
     *             @OA\Property(property="type", type="string", example="Standard"),
     *             @OA\Property(property="price_per_night", type="number", format="float", example=100.50),
     *             @OA\Property(property="status", type="string", example="available"),
     *         )
     *     ),
     *     @OA\Response(response="201", description="Room created successfully", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="number", type="string", example="101"),
     *             @OA\Property(property="type", type="string", example="Standard"),
     *             @OA\Property(property="price_per_night", type="number", format="float", example=100.50),
     *             @OA\Property(property="status", type="string", example="available"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         ),
     *         @OA\Property(property="message", type="string", example="Room created successfully."),
     *     )),
     *     @OA\Response(response="401", description="Failed to authenticate"),
     *     @OA\Response(response="422", description="Validation errors", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="errors", type="object", example={"number": {"The number field is required."}}),
     *     )),
     * )
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'number' => ['required', 'string', 'unique:rooms'],
                'type' => ['required', 'string'],
                'price_per_night' => ['required', 'numeric'],
                'status' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $room = Room::create($request->all());

            return response()->json(['data' => $room, 'message' => 'Room created successfully.'], 201);
        } else {
            return response()->json([
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }
}
