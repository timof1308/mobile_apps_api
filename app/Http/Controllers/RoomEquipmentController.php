<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Equipment;
use App\Models\RoomEquipment;
use Illuminate\Http\Request;

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

    public function getRoomEquipment($roomId)
    {
        // get room equipment
        $equipment = RoomEquipment::with(array('room', 'equipment'))->where('room_id', $roomId)->get();
        return response()->json($equipment->toArray(), 200);
    }

    public function createRoomEquipment(Request $request, $roomId)
    {
        // validate input data
        $validator = $this->validate($request, [
            'equipment_id' => 'required|Integer'
        ]);
        if (!$validator) {
            return response()->json($validator, 400);
        }

        // get relations
        $room = Room::find($roomId);
        $equipment = Equipment::find($request->get('equipment_id'));
        // check if relations exist
        if (!isset($room) || !isset($equipment)) { // relations not found
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
