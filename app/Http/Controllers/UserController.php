<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;

class UserController extends Controller
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
     * Get all Users
     *
     * @return JsonResponse
     */
    public function getUsers()
    {
        // return all users
        return response()->json(User::all(), 200);
    }

    /**
     * Get User
     *
     * @param Integer $id to find
     * @return JsonResponse|Response|ResponseFactory
     */
    public function getUser($id)
    {
        // get user
        $user = User::find($id);
        // check if user exists
        if (!isset($user)) { // user not found
            return response(null, 404);
        }
        // return user
        return response()->json($user, 200);
    }

    /**
     * Create User
     *
     * @param Request $request
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function createUser(Request $request)
    {
        // validate input data
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
            'role' => 'required|Integer'
        ]);

        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = hash('sha256', $request->get('password'));
        $user->role = $request->get('role');
        $user->save();

        return response()->json($user, 201, ['Location' => route('get_user', ['id' => $user->id])]);
    }

    /**
     * Update User
     *
     * @param Request $request
     * @param Integer $id to update
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function updateUser(Request $request, $id)
    {
        // validate input data
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|max:255',
            'token' => 'nullable|Integer',
            'role' => 'required|Integer'
        ]);

        // find user
        $user = User::find($id);

        // check if user exists
        if (!isset($user)) { // user not found
            return response(null, 404);
        }

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = hash('sha256', $request->get('password'));
        $user->token = $request->get('token');
        $user->role = $request->get('role');
        $user->save();

        return response()->json($user, 200);
    }

    /**
     * Delete User
     *
     * @param Integer $id to delete
     * @return Response|ResponseFactory
     */
    public function deleteUser($id)
    {
        // find user
        $user = User::find($id);

        // check if user exists
        if (!isset($user)) { // user not found
            return response(null, 404);
        }
        // delete user
        $user->delete();
        return response(null, 204);
    }

    /**
     * Get meetings assigned to user
     *
     * @param Integer $userId to filter
     * @return JsonResponse
     */
    public function getMeetings($userId)
    {
        // get meetings for user
        $meetings = Meeting::with(array('room', 'user', 'visitors', 'visitors.company'))
            ->where('user_id', $userId)
            ->whereDate('date', '>=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get();
        return response()->json($meetings->toArray(), 200);
    }
}
