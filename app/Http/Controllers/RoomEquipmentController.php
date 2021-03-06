<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomEquipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;

class RoomEquipmentController extends Controller
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
     * Get Room Equipment
     *
     * @param Integer $roomId to search for
     * @return JsonResponse
     */
    public function getRoomEquipment($roomId)
    {
        // get room equipment
        $equipment = RoomEquipment::with(array('room', 'equipment'))->where('room_id', $roomId)->get();
        return response()->json($equipment->toArray(), 200);
    }

    /**
     * Create Room Equipment
     *
     * @param Request $request
     * @param Integer $roomId to create for room
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function createRoomEquipment(Request $request, $roomId)
    {
        // validate input data
        $this->validate($request, [
            'equipment_id' => 'required|Integer|exists:equipment,id'
        ]);

        // get relations
        $room = Room::find($roomId);
        // check if relations exist
        if (!isset($room)) { // relations not found
            return response(null, 404);
        }

        // create new model
        $room_equipment = new RoomEquipment();
        $room_equipment->equipment_id = $request->get('equipment_id');
        $room_equipment->room_id = $roomId;
        $room_equipment->save();

        // return new model
        return response()->json($room_equipment, 201);
    }

    /**
     * Delete Room Equipment
     *
     * @param $roomId
     * @param Integer $id to delete
     * @return Response|ResponseFactory
     */
    public function deleteRoomEquipment($roomId, $id)
    {
        // find room_equipment
        $room_equipment = RoomEquipment::find($id);
        // check if room_equipment exists
        if (!isset($room_equipment)) { // room_equipment not found
            return response(null, 404);
        }
        $room_equipment->delete();
        // return no content
        return response(null, 204);
    }
}
