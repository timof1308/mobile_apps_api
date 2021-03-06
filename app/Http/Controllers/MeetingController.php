<?php

namespace App\Http\Controllers;

use App\Mail\MeetingBundleCreated;
use App\Mail\MeetingCanceled;
use App\Mail\MeetingUpdated;
use App\Models\Meeting;
use App\Models\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;
use phpDocumentor\Reflection\Types\Integer;

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
        $this->validate($request, [
            'user_id' => 'required|Integer|exists:users,id',
            'room_id' => 'required|Integer|exists:rooms,id',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'duration' => 'required|Integer'
        ]);

        // create new model
        $meeting = new Meeting();
        $meeting->user_id = $request->get("user_id");
        $meeting->room_id = $request->get("room_id");
        $meeting->date = $request->get("date");
        $meeting->duration = $request->get("duration");
        $meeting->save();

        return response()->json($meeting, 201, ['Location' => route('get_meeting', ['id' => $meeting->id])]);
    }

    /**
     * Update Meeting
     *
     * @param Request $request
     * @param Integer $id to update
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     * @throws \Exception
     */
    public function updateMeeting(Request $request, $id)
    {
        // validate input data
        $this->validate($request, [
            'user_id' => 'required|Integer|exists:users,id',
            'room_id' => 'required|Integer|exists:rooms,id',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'duration' => 'required|Integer'
        ]);

        // find meeting
        $meeting = Meeting::with(array('user', 'room', 'visitors', 'visitors.meeting'))->where('id', $id)->first();
        // check if meeting exists
        if (!isset($meeting)) { // meeting not found
            return response(null, 404);
        }

        $sendUpdate = false; // checker
        // check if meeting has been rescheduled
        if ($request->get("date") != $meeting->date || $request->get("duration") != $meeting->duration) { // compare if dates or duration are the same
            $sendUpdate = true;
            $old_date = $meeting->date; // temp save old date for update mail
            $old_duration = $meeting->duration; // temp save old duration for update mail
        }

        // update attributes
        $meeting->user_id = $request->get("user_id");
        $meeting->room_id = $request->get("room_id");
        $meeting->date = $request->get("date");
        $meeting->duration = $request->get("duration");
        $meeting->save();

        // send update mail
        if ($sendUpdate) {
            // create meeting file to attach
            $meeting_path = \iCalendar::update_calender_entry($meeting->id, $meeting->user->name, $meeting->user->email, "Meeting details", "Created by VMS-Mobile", (new \DateTime($meeting->date)), (new \DateTime($meeting->date))->add(new \DateInterval('PT' . $meeting->duration . 'M')));
            // for each visitor that will participate at the meeting
            foreach ($meeting->visitors as $visitor) {
                // tmp update meeting date for collection model
                $visitor->meeting->date = $request->get("date");
                $visitor->meeting->duration = $request->get("duration");
                // send mail
                Mail::to($visitor->email)->send(new MeetingUpdated($visitor, $old_date, $old_duration));
            }
            // delete meeting file
            unlink($meeting_path);
        }

        // return meeting
        return response()->json($meeting->toArray(), 200);
    }

    /**
     * Delete Meeting
     * send Mail to all participants / visitors
     *
     * @param Integer $id to delete
     * @return Response|ResponseFactory
     * @throws \Exception
     */
    public function deleteMeeting($id)
    {
        // find meeting
        $meeting = Meeting::with(array('user', 'room', 'visitors', 'visitors.meeting'))->where('id', $id)->first();
        // check if meeting exists
        if (!isset($meeting)) { // meeting not found
            return response(null, 404);
        }

        // for each visitor from meeting
        $meeting_path = \iCalendar::cancel_calender_entry($meeting->id, $meeting->user->name, $meeting->user->email, "Meeting details", "Created by VMS-Mobile", (new \DateTime($meeting->date)), (new \DateTime($meeting->date))->add(new \DateInterval('PT' . $meeting->duration . 'M')));
        foreach ($meeting->visitors as $visitor) {
            // send mail
            Mail::to($visitor->email)->send(new MeetingCanceled($visitor));
        }
        // delete meeting file
        unlink($meeting_path);

        $meeting->delete();
        // return no content
        return response(null, 204);
    }

    /**
     * Create meeting and assigned visitors at once
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function createBundle(Request $request)
    {
        // validate input data
        $this->validate($request, [
            'user_id' => 'required|Integer|exists:users,id', // meeting details
            'room_id' => 'required|Integer|exists:rooms,id', // check if relation exists
            'date' => 'required|date_format:Y-m-d H:i:s',
            'duration' => 'required|Integer',
            'visitor' => 'required|array|min:1', // array is required with at least one member
            'visitor.*.name' => 'required|max:255', // each visitor name ...
            'visitor.*.email' => 'required|email',
            'visitor.*.tel' => 'required|max:255',
            'visitor.*.company_id' => 'required|Integer|exists:companies,id',
            'visitor.*.check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'visitor.*.check_out' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        // create new meeting
        $meeting = new Meeting();
        $meeting->user_id = $request->get('user_id');
        $meeting->room_id = $request->get('room_id');
        $meeting->date = $request->get('date');
        $meeting->duration = $request->get('duration');
        $meeting->save();

        // for each visitor in array
        foreach ($request->get('visitor') as $v) {
            $visitor = new Visitor();
            $visitor->name = $v["name"];
            $visitor->email = $v["email"];
            $visitor->tel = $v["tel"];
            $visitor->company_id = $v["company_id"];
            $visitor->meeting_id = $meeting->id;
            $visitor->check_in = isset($v["check_in"]) ? ($v["check_in"] != "" ? $v["check_in"] : null) : null;
            $visitor->check_out = isset($v["check_in"]) ? ($v["check_out"] != "" ? $v["check_out"] : null) : null;
            $visitor->save();

            // send mail to created visitor
            (new VisitorController())->sendMail($visitor);
        }

        // send mail bundle
        $this->sendMailBundle($meeting->id);

        // return response
        return response()->json($meeting->toArray(), 201, ['Location' => route('get_meeting', ['id' => $meeting->id])]);
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

    /**
     * Send Mail Bundle to meeting participants
     *
     * @param Integer $meetingId
     * @throws \Exception
     */
    public function sendMailBundle($meetingId)
    {
        $meeting = Meeting::with(array('user', 'room', 'visitors'))->where('id', $meetingId)->first();

        // create calendar entry file
        $meeting_path = \iCalendar::new_calender_entry($meeting->id, $meeting->user->name, $meeting->user->email, "Meeting details", "Created by VMS-Mobile", (new \DateTime($meeting->date)), (new \DateTime($meeting->date))->add(new \DateInterval('PT' . $meeting->duration . 'M')));

        // send mail
        Mail::to($meeting->user->email)->send(new MeetingBundleCreated($meeting));

        // delete calendar file
        unlink($meeting_path);
    }
}
