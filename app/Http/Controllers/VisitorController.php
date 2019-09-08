<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;

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
     * @param $id int to find
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
        $validator = $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'meeting_id' => 'required|Integer',
            'company_id' => 'required|Integer',
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s'
        ]);
        if (!$validator) {
            return response()->json($validator, 400);
        }

        // get relations
        $company = Company::find($request->get("company_id"));
        $meeting = Company::find($request->get("meeting_id"));
        // check if relations exists
        if (!isset($company) || !isset($meeting)) { // relations not
            return response("missing relation", 404);
        }

        $visitor = new Visitor();
        $visitor->name = $request->get("name");
        $visitor->email = $request->get("email");
        $visitor->company_id = $request->get("company_id");
        $visitor->meeting_id = $request->get("meeting_id");
        $visitor->check_in = $request->get("check_in") != "" ? $request->get('check_in') : null;
        $visitor->check_out = $request->get("check_out") != "" ? $request->get('check_out') : null;
        $visitor->save();

        return response()->json($visitor, 201);
    }

    /**
     * Update Visitor
     *
     * @param Request $request
     * @param $id int to update
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function updateVisitor(Request $request, $id)
    {
        // validate input data
        $validator = $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'meeting_id' => 'required|Integer',
            'company_id' => 'required|Integer',
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s'
        ]);
        if (!$validator) {
            return response()->json($validator, 400);
        }

        // find room
        $visitor = Visitor::with(array('meeting', 'company', 'meeting.user', 'meeting.room'))->where('id', $id)->first();
        // get relations
        $company = Company::find($request->get("company_id"));
        $meeting = Company::find($request->get("meeting_id"));
        // check if visitor and relations exists
        if (!isset($visitor) || !isset($company) || !isset($meeting)) { // not found
            return response(null, 404);
        }

        // update attributes
        $visitor->name = $request->get("name");
        $visitor->email = $request->get("email");
        $visitor->company_id = $request->get("company_id");
        $visitor->meeting_id = $request->get("meeting_id");
        $visitor->check_in = $request->get("check_in") != "" ? $request->get('check_in') : null;
        $visitor->check_out = $request->get("check_out") != "" ? $request->get('check_out') : null;
        $visitor->save();

        // return visitor
        return response()->json($visitor->toArray(), 200);
    }

    /**
     * Delete Visitor
     *
     * @param $id int to delete
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
}
