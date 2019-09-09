<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Room;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;

class MeetingController extends Controller
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
     * Get all Meetings with relations
     *
     * @return JsonResponse
     */
    public function getMeetings()
    {
        // get all meetings with relations
        $meetings = Meeting::with(array('room', 'user'))->get();

        return response()->json($meetings->toArray(), 200);
    }

    /**
     * Get Meeting with relation
     *
     * @param Integer $id to find
     * @return JsonResponse|Response|ResponseFactory
     */
    public function getMeeting($id)
    {
        // find meeting
        $meeting = Meeting::with(array('user', 'room'))->where('id', $id)->first();
        // check if meeting exists
        if (!isset($meeting)) { // user not found
            return response(null, 404);
        }
        // return meeting
        return response()->json($meeting->toArray(), 200);
    }

    /**
     * Create Meeting
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function createMeeting(Request $request)
    {
        // validate input data
        $validator = $this->validate($request, [
            'user_id' => 'required|Integer',
            'room_id' => 'required|Integer',
            'date' => 'required|date_format:Y-m-d H:i:s'
        ]);
        if (!$validator) {
            return response()->json($validator, 400);
        }

        // get relations
        $user = User::find($request->get("user_id"));
        $room = Room::find($request->get("room_id"));
        // check if relations exists
        if (!isset($user) || !isset($room)) { // relations not
            return response("missing relation", 404);
        }

        $meeting = new Meeting();
        $meeting->user_id = $request->get("user_id");
        $meeting->room_id = $request->get("room_id");
        $meeting->date = $request->get("date");
        $meeting->save();

        return response()->json($meeting, 201);
    }

    /**
     * Update Meeting
     *
     * @param Request $request
     * @param Integer $id to update
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function updateMeeting(Request $request, $id)
    {
        // validate input data
        $validator = $this->validate($request, [
            'user_id' => 'required|Integer',
            'room_id' => 'required|Integer',
            'date' => 'required|date_format:Y-m-d H:i:s'
        ]);
        if (!$validator) {
            return response()->json($validator, 400);
        }

        // get relations
        $user = User::find($request->get("user_id"));
        $room = Room::find($request->get("room_id"));
        // check if relations exists
        if (!isset($user) || !isset($room)) { // relations not
            return response("missing relation", 404);
        }

        // find meeting
        $meeting = Meeting::with(array('user', 'room'))->where('id', $id)->first();
        // check if meeting exists
        if (!isset($meeting)) { // meeting not found
            return response(null, 404);
        }

        // update attributes
        $meeting->user_id = $request->get("user_id");
        $meeting->room_id = $request->get("room_id");
        $meeting->date = $request->get("date");
        $meeting->save();

        // return meeting
        return response()->json($meeting->toArray(), 200);
    }

    /**
     * Delete Meeting
     *
     * @param Integer $id to delete
     * @return Response|ResponseFactory
     */
    public function deleteMeeting($id)
    {
        // find meeting
        $meeting = Meeting::find($id);
        // check if meeting exists
        if (!isset($meeting)) { // meeting not found
            return response(null, 404);
        }
        $meeting->delete();
        // return no content
        return response(null, 204);
    }

    /**
     * Get Visitors assigned to meeting
     *
     * @param Integer $meetingId to filter
     * @return JsonResponse
     */
    public function getVisitors($meetingId)
    {
        // get visitors for meeting
        $visitors = Visitor::with(array('meeting', 'company'))->where('meeting_id', $meetingId)->get();
        return response()->json($visitors->toArray(), 200);
    }
}
