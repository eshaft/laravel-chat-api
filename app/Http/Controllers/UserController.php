<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return response()->json([
            'users' => User::all(),
            'message' => 'Users has been found',
            'success' => true
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
        $chats = $user->userChats;
        $user->messagesCount = $user->userMessages->count();

        $newChats = [];
        foreach ($chats as $chat) {
            $newChat = $chat;
            $newChat->creator = $chat->chatCreator;
            $members = User::whereIn('_id', $chat->members)->get();
            $newChat->members = $members;
            $newChats[] = $newChat;
        }

        $user->chats = $newChats;

        return response()->json([
            'success' => true,
            'message' => 'User information has been retrieved',
            'user' => $user
        ]);
    }

    /**
     * Update the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request)
    {
        auth()->user()->update($request->get('data'));

        return response()->json([
            'success' => true,
            'message' => 'User has been updated',
            'user' => auth()->user()
        ]);
    }

    public function show(User $user)
    {
        $chats = $user->userChats;
        $user->messagesCount = $user->userMessages->count();

        $newChats = [];
        foreach ($chats as $chat) {
            $newChat = $chat;
            $newChat->creator = $chat->chatCreator;
            $members = User::whereIn('_id', $chat->members)->get();
            $newChat->members = $members;
            $newChats[] = $newChat;
        }

        $user->chats = $newChats;

        return response()->json([
            'user' => $user,
            'message' => 'User information has been retrieved',
            'success' => true
        ]);
    }
}
