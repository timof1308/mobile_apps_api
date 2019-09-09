<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;

class EquipmentController extends Controller
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
     * Get all Equipment
     *
     * @return JsonResponse
     */
    public function getAllEquipment()
    {
        // get all equipment
        return response()->json(Equipment::all(), 200);
    }

    /**
     * Get Equipment
     *
     * @param Integer $id to find
     * @return JsonResponse|Response|ResponseFactory
     */
    public function getEquipment($id)
    {
        // find equipment
        $equipment = Equipment::find($id);
        // check if equipment exists
        if (!isset($equipment)) { // user not found
            return response(null, 404);
        }
        // return equipment
        return response()->json($equipment, 200);
    }

    /**
     * Create Equipment
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function createEquipment(Request $request)
    {
        // validate input data
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        // create new equipment
        $equipment = new Equipment();
        $equipment->name = $request->get("name");
        $equipment->save();

        // return equipment
        return response()->json($equipment, 201);
    }

    /**
     * Update Equipment
     *
     * @param Request $request
     * @param Integer $id to update
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function updateEquipment(Request $request, $id)
    {
        // validate input data
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        // find equipment
        $equipment = Equipment::find($id);
        // check if equipment exists
        if (!isset($equipment)) { // equipment not found
            return response(null, 404);
        }

        // update attributes
        $equipment->name = $request->get("name");
        $equipment->save();

        // return equipment
        return response()->json($equipment, 200);
    }

    /**
     * Delete Equipment
     *
     * @param Integer $id to delete
     * @return Response|ResponseFactory
     */
    public function deleteEquipment($id)
    {
        // find equipment
        $equipment = Equipment::find($id);
        // check if equipment exists
        if (!isset($equipment)) { // equipment not found
            return response(null, 404);
        }
        $equipment->delete();
        // return no content
        return response(null, 204);
    }
}
