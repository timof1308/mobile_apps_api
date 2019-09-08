<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;

class RoomController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get all Rooms
     *
     * @return JsonResponse
     */
    public function getRooms()
    {
        // get all rooms
        return response()->json(Room::all(), 200);
    }

    /**
     * Get Room
     *
     * @param $id int to find
     * @return JsonResponse|Response|ResponseFactory
     */
    public function getRoom($id)
    {
        // find room
        $room = Room::find($id);
        // check if room exists
        if (!isset($room)) { // user not found
            return response(null, 404);
        }
        // return meeting
        return response()->json($room, 200);
    }

    /**
     * Create Room
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function createRoom(Request $request)
    {
        // validate input data
        $validator = $this->validate($request, [
            'name' => 'required|max:255'
        ]);
        if (!$validator) {
            return response()->json($validator, 400);
        }

        $room = new Room();
        $room->name = $request->get("name");
        $room->save();

        return response()->json($room, 201);
    }

    /**
     * Update Room
     *
     * @param Request $request
     * @param $id int to update
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function updateRoom(Request $request, $id)
    {
        // validate input data
        $validator = $this->validate($request, [
            'name' => 'required|max:255'
        ]);
        if (!$validator) {
            return response()->json($validator, 400);
        }

        // find room
        $room = Room::find($id);
        // check if room exists
        if (!isset($room)) { // room not found
            return response(null, 404);
        }

        // update attributes
        $room->name = $request->get("name");
        $room->save();

        // return room
        return response()->json($room, 200);
    }

    /**
     * Delete Room
     *
     * @param $id int to delete
     * @return Response|ResponseFactory
     */
    public function deleteRoom($id)
    {
        // find room
        $room = Room::find($id);
        // check if room exists
        if (!isset($room)) { // room not found
            return response(null, 404);
        }
        $room->delete();
        // return no content
        return response(null, 204);
    }
}
