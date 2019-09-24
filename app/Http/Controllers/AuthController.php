<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    /**
     * Sign up user
     *
     * @param SignupRequest $request
     * @param User $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(SignupRequest $request, User $model)
    {
        $user = $model->create(
            $request->merge([
                'password' => Hash::make($request->get('password')),
                'firstName' => '',
                'lastName' => ''
            ])->all()
        );

        $token = auth()->login($user);

        $user->chats = $user->userChats;
        $user->messagesCount = $user->userMessages->count();

        return response()->json([
            'success' => true,
            'message' => 'User has been created',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->all(['username', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'The username or password you entered is incorrect'
            ]);
        }

        $user = auth()->user();

        $chats = $user->userChats;

        $newChats = [];
        foreach ($chats as $chat) {
            $newChat = $chat;
            $newChat->creator = $chat->chatCreator;
            $members = User::whereIn('_id', $chat->members)->get();
            $newChat->members = $members;
            $newChats[] = $newChat;
        }

        $user->chats = $newChats;

        $user->messagesCount = $user->userMessages->count();

        return response()->json([
            'success' => true,
            'message' => 'Success! You are logged in',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'You are now logged out'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json(auth()->refresh());
    }
}
