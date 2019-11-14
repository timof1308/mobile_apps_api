<?php

namespace App\Http\Controllers;

use App\Mail\VisitorCheckedIn;
use App\Mail\VisitorCreated;
use App\Models\Company;
use App\Models\Meeting;
use App\Models\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VisitorController extends Controller
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
     * Get all Visitors
     *
     * @return JsonResponse
     */
    public function getVisitors()
    {
        // get all visitors
        $visitors = Visitor::with(array('meeting', 'company', 'meeting.user', 'meeting.room'))->get();

        return response()->json($visitors->toArray(), 200);
    }

    /**
     * Get Visitor
     *
     * @param Integer $id to find
     * @return JsonResponse|Response|ResponseFactory
     */
    public function getVisitor($id)
    {
        // find visitor
        $visitor = Visitor::with(array('meeting', 'company', 'meeting.user', 'meeting.room'))->where('id', $id)->first();
        // check if room exists
        if (!isset($visitor)) { // visitor not found
            return response(null, 404);
        }
        // return meeting
        return response()->json($visitor->toArray(), 200);
    }

    /**
     * Create Visitor
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function createVisitor(Request $request)
    {
        // validate input data
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'tel' => 'required|max:255',
            'meeting_id' => 'required|Integer|exists:meetings,id',
            'company_id' => 'required|Integer|exists:companies,id',
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        // create new model
        $visitor = new Visitor();
        $visitor->name = $request->get("name");
        $visitor->email = $request->get("email");
        $visitor->tel = $request->get("tel");
        $visitor->company_id = $request->get("company_id");
        $visitor->meeting_id = $request->get("meeting_id");
        $visitor->check_in = $request->get("check_in") != "" ? $request->get('check_in') : null;
        $visitor->check_out = $request->get("check_out") != "" ? $request->get('check_out') : null;
        $visitor->save();

        // get full visitor model
        $v = Visitor::with(array('meeting', 'company', 'meeting.user', 'meeting.room'))->where('id', $visitor->id)->first();
        // send mail
        $this->sendMail($v);

        return response()->json($v->toArray(), 201, ['Location' => route('get_visitor', ['id' => $visitor->id])]);
    }

    /**
     * Update Visitor
     *
     * @param Request $request
     * @param Integer $id to update
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function updateVisitor(Request $request, $id)
    {
        // validate input data
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'tel' => 'required|max:255',
            'meeting_id' => 'required|Integer|exists:meetings,id',
            'company_id' => 'required|Integer|exists:companies,id',
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        // find room
        $visitor = Visitor::with(array('meeting', 'company', 'meeting.user', 'meeting.room'))->where('id', $id)->first();
        // get relations
        $company = Company::find($request->get("company_id"));
        $meeting = Meeting::with(array('user', 'room'))->where('id', $request->get("meeting_id"))->first();
        // check if visitor and relations exists
        if (!isset($visitor) || !isset($company) || !isset($meeting)) { // not found
            return response(null, 404);
        }

        // check if qr code needs to get resend by comparing email address
        $resendMail = false;
        if ($visitor->email != $request->get('email')) {
            $resendMail = true;
        }

        // update attributes
        $visitor->name = $request->get("name");
        $visitor->email = $request->get("email");
        $visitor->tel = $request->get("tel");
        $visitor->company_id = $request->get("company_id");
        $visitor->meeting_id = $request->get("meeting_id");
        if ($request->has("check_in"))
            $visitor->check_in = $request->get("check_in");
        if ($request->has("check_out"))
            $visitor->check_out = $request->get("check_out");
        $visitor->save();

        // update model collection
        $visitor->company = $company;
        $visitor->meeting = $meeting;

        // only send mail if email has changed
        if ($resendMail) {
            $this->sendMail($visitor);
        }

        // return visitor
        return response()->json($visitor->toArray(), 200);
    }

    /**
     * Delete Visitor
     *
     * @param Integer $id to delete
     * @return Response|ResponseFactory
     */
    public function deleteVisitor($id)
    {
        // find visitor
        $visitor = Visitor::find($id);
        // check if visitor exists
        if (!isset($visitor)) { // visitor not found
            return response(null, 404);
        }
        $visitor->delete();
        // return no content
        return response(null, 204);
    }

    /**
     * Check in visitor
     *
     * @param Integer $visitorId
     * @return JsonResponse|Response|ResponseFactory
     * @throws \Exception
     */
    public function checkInVisitor($visitorId)
    {
        // get visitor
        $visitor = Visitor::with(array('company', 'meeting', 'meeting.user', 'meeting.room'))->where('id', $visitorId)->first();
        // check if visitor exists
        if (!isset($visitor)) { // does not exist
            return response(null, 404);
        }

        // check if meeting is in the past or future
        if (date('Ymd', strtotime($visitor->meeting->date)) != date('Ymd')) {
            return response()->json(array("message" => "can not check in visitor scheduled for a meeting in the past or future"));
        }

        // check if visitor has been checked in
        if ($visitor->check_in != null) { // has already been checked in
            return response()->json(array("message" => "already checked in"), 412);
        }

        // set current timestamp to check_in
        $now = new \DateTime();
        $visitor->check_in = $now->format('Y-m-d H:i:s');
        $visitor->save();

        // send mail to host
        Mail::to($visitor->meeting->user->email)->send(new VisitorCheckedIn($visitor));

        return response()->json($visitor->toArray(), 200);
    }

    /**
     * Check out visitor
     *
     * @param Integer $visitorId
     * @return JsonResponse|Response|ResponseFactory
     * @throws \Exception
     */
    public function checkOutVisitor($visitorId)
    {
        // get visitor
        $visitor = Visitor::with(array('company', 'meeting', 'meeting.user', 'meeting.room'))->where('id', $visitorId)->first();
        // check if visitor exists
        if (!isset($visitor)) { // does not exist
            return response(null, 404);
        }

        // check if visitor has been checked in or already checked out
        if ($visitor->check_in == null || $visitor->check_out != null) { // has already not been checked in OR has already been checked out
            return response()->json(array("message" => "not checked in or already checked out"), 412);
        }

        // set current timestamp to check_out
        $now = new \DateTime();
        $visitor->check_out = $now->format('Y-m-d H:i:s');
        $visitor->save();

        return response()->json($visitor->toArray(), 200);
    }

    /**
     * Generate QR Code for Visitor
     *
     * @param Integer $visitorId to generate QR Code for
     * @return Response|ResponseFactory|BinaryFileResponse
     */
    public function generateQrCode($visitorId)
    {
        // find visitor
        $visitor = Visitor::with(array('meeting', 'company', 'meeting.user', 'meeting.room'))->where('id', $visitorId)->first();
        // check if room exists
        if (!isset($visitor)) { // visitor not found
            return response(null, 404);
        }

        // specify content
        $content = array(
            'id' => $visitor->id,
            'name' => $visitor->name,
            'tel' => $visitor->tel,
            'company' => $visitor->company->name,
            'date' => $visitor->meeting->date,
            'host_id' => $visitor->meeting->user->id,
            'host' => $visitor->meeting->user->name
        );

        // define output
        $path = base_path("storage/files/qrcode_$visitorId.png");

        // generate qr code
        QrCode::format('png')
            ->size(400)
            ->generate(json_encode($content), $path);

        // return file path
        return $path;
    }

    /**
     * Get QR Code as image download
     *
     * @param Integer $visitorId to get QR Code Image for
     * @return BinaryFileResponse
     */
    public function getQrCode($visitorId)
    {
        // generate code and get file path
        $path = $this->generateQrCode($visitorId);

        // specify header for response
        $headers = $headers = ['Content-Type' => 'image/png'];
        // prepare image response
        $response = new BinaryFileResponse($path, 200, $headers);
        return $response;
    }

    /**
     * Send mail with attached QR Code to visitor
     *
     * @param Visitor $visitor
     * @throws \Exception
     */
    public function sendMail(Visitor $visitor)
    {
        // generate meeting file
        $meeting_path = \iCalendar::new_calender_entry($visitor->meeting->id, $visitor->meeting->user->name, $visitor->meeting->user->email, "Meeting details", "Created by VMS-Mobile", (new \DateTime($visitor->meeting->date)), (new \DateTime($visitor->meeting->date))->add(new \DateInterval('PT' . $visitor->meeting->duration . 'M')));
        // generate qr code
        $path = $this->generateQrCode($visitor->id);
        // send mail
        Mail::to($visitor->email)->send(new VisitorCreated($visitor, $path));
        // delete qr code
        unlink($path);
        unlink($meeting_path);
    }
}
