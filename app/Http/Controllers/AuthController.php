<?php

namespace App\Http\Controllers;

use App\Mail\UserResetPassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    /**
     * The request instance.
     *
     * @var Request
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Create a new token.
     *
     * @param User $user
     * @return string
     */
    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "mobile_apps_api", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'role' => $user->role,
            'token' => $user->token,
            'iat' => time(), // Time when JWT was issued.
            //'exp' => time() + 60 * 60 * 2 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param User $user
     * @return mixed
     * @throws ValidationException
     */
    public function authenticate(User $user)
    {
        $this->validate($this->request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        // Find the user by email
        $user = User::where('email', $this->request->input('email'))->first();
        // check if user exists
        if (!$user) {
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }
        // Verify the password and generate the token
        if (hash("sha256", $this->request->get('password')) == $user->password) {
            return response()->json([
                'token' => $this->jwt($user)
            ], 200);
        }
        // Bad Request response
        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);
    }

    /**
     * Create new user account by calling UserController method
     *
     * @throws ValidationException
     */
    public function register()
    {
        (new UserController())->createUser($this->request);
    }

    /**
     * Request token to reset password
     *
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function forget()
    {
        $this->validate($this->request, [
            'email' => 'required|email'
        ]);

        $user = User::where('email', $this->request->input('email'))->first();
        // check if user exists
        if (!$user) {
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }
        // generate key
        $token = $this->generate_key();
        // assign key
        $user->token = $token;
        $user->save();

        // send token to user
        Mail::to($user->email)->send(new UserResetPassword($token));

        return response(null, 200);
    }

    /**
     * Set new password for user
     *
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function reset()
    {
        $this->validate($this->request, [
            'email' => 'required|email',
            'token' => 'required|Integer',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        // get user where email and token match record
        $user = User::where('email', $this->request->input('email'))
            ->where('token', $this->request->input('token'))
            ->first();
        // check if user exists
        if (!$user) {
            return response()->json([
                'error' => 'Email and token combination does not exist.'
            ], 400);
        }

        // update password with sha256 hash
        $user->password = hash("sha256", $this->request->input('password'));
        $user->save();

        return response(null, 200);
    }

    /**
     * Generate random key with 4 digits
     * @return string
     */
    function generate_key()
    {
        $numbers = '0123456789';
        $numbers_length = strlen($numbers);
        $random_key = '';
        for ($i = 0; $i < 4; $i++) {
            // first integer must be greater than 0 to avoid trimmed integer
            $random = $numbers[rand(0, $numbers_length - 1)];
            if ($i = 0) {
                // repeat as long as int is not 0
                while ($random == "0") {
                    $random = $numbers[rand(0, $numbers_length - 1)];
                }
            }
            $random_key .= $random;
        }
        return $random_key;
    }
}
