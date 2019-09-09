<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;

class CompanyController extends Controller
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
    public function getCompanies()
    {
        // get all rooms
        return response()->json(Company::all(), 200);
    }

    /**
     * Get Company
     *
     * @param Integer $id to find
     * @return JsonResponse|Response|ResponseFactory
     */
    public function getCompany($id)
    {
        // find company
        $company = Company::find($id);
        // check if company exists
        if (!isset($company)) { // company not found
            return response(null, 404);
        }
        // return company
        return response()->json($company, 200);
    }

    /**
     * Create Company
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function createCompany(Request $request)
    {
        // validate input data
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        // create new model
        $company = new Company();
        $company->name = $request->get("name");
        $company->save();

        return response()->json($company, 201);
    }

    /**
     * Update Company
     *
     * @param Request $request
     * @param Integer $id to update
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function updateCompany(Request $request, $id)
    {
        // validate input data
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        // find company
        $company = Company::find($id);
        // check if company exists
        if (!isset($company)) { // company not found
            return response(null, 404);
        }

        // update attributes
        $company->name = $request->get("name");
        $company->save();

        // return company
        return response()->json($company, 200);
    }

    /**
     * Delete Company
     *
     * @param Integer $id to delete
     * @return Response|ResponseFactory
     */
    public function deleteCompany($id)
    {
        // find company
        $company = Company::find($id);
        // check if company exists
        if (!isset($company)) { // company not found
            return response(null, 404);
        }
        $company->delete();
        // return no content
        return response(null, 204);
    }

    /**
     * Get Visitors assigned to meeting
     *
     * @param Integer $companyId to filter
     * @return JsonResponse
     */
    public function getVisitors($companyId)
    {
        // get visitors for meeting
        $visitors = Visitor::with(array('meeting', 'company'))->where('company_id', $companyId)->get();
        return response()->json($visitors->toArray(), 200);
    }
}
