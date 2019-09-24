<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Http\Requests\ChatRequest;
use App\Message;
use App\User;
use Illuminate\Support\Arr;

class ChatController extends Controller
{
    /**
     * Create a new ChatController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $chats = Chat::with('chatCreator')->get();
        $newChats = [];
        foreach ($chats as $chat) {
            $newChat = $chat;
            $newChat->creator = $chat->chatCreator;
            $members = User::whereIn('_id', $chat->members)->get();
            $newChat->members = $members;
            $newChats[] = $newChat;
        }

        return response()->json([
            'chats' => $newChats,
            'success' => true
        ]);
    }

    public function store(ChatRequest $request, Chat $model)
    {
        $data = $request->get('data');

        $chat = $model->create([
            'title' => $data['title'],
            'creator' => auth()->user()->_id,
            'members' => []
        ]);

        $chat->creator = $chat->chatCreator;

        return response()->json([
            'chat' => $chat,
            'success' => true,
            'message' => 'Chat has been created'
        ]);
    }

    public function my()
    {
        $chats = Chat::with('chatCreator')->my()->get();
        $newChats = [];
        foreach ($chats as $chat) {
            $newChat = $chat;
            $newChat->creator = $chat->chatCreator;
            $members = User::whereIn('_id', $chat->members)->get();
            $newChat->members = $members;
            $newChats[] = $newChat;
        }

        return response()->json([
            'chats' => $newChats,
            'success' => true
        ]);
    }

    public function show(Chat $chat)
    {
        $chat->creator = $chat->chatCreator;
        $members = User::whereIn('_id', $chat->members)->get();
        $chat->members = $members;

        return response()->json([
            'chat' => $chat,
            'success' => true
        ]);
    }

    public function join(Chat $chat, Message $model)
    {
        $isCreator = $chat->creator == auth()->user()->_id;

        $isMember = (bool) Arr::first($chat->members, function ($value, $key) {
            return $value == auth()->user()->_id;
        }) ?? false;

        if ($isMember || $isCreator) {
            return response()->json([
                'success' => false,
                'message' => 'User has already joined this chat'
            ]);
        } else {

            $members = $chat->members;
            $members[] = auth()->user()->_id;
            $chat->members = $members;
            $chat->save();

            $message = $model->create([
                'chatId' => $chat->_id,
                'sender' => auth()->user()->_id,
                'content' => ' joined',
                'statusMessage' => true
            ]);

            $message->sender = $message->messageSender;

            $chat->creator = $chat->chatCreator;

            $members = User::whereIn('_id', $chat->members)->get();

            $chat->members = $members;

            return response()->json([
                'success' => true,
                'message' => $message,
                'chat' => $chat
            ]);
        }
    }

    public function leave(Chat $chat, Message $model)
    {
        $isCreator = $chat->creator == auth()->user()->_id;

        $isMember = (bool) Arr::first($chat->members, function ($value, $key) {
                return $value == auth()->user()->_id;
            }) ?? false;

        if ($isCreator) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot leave your own chat! You can only delete you own chats'
            ]);
        }

        if(!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a member of this chat'
            ]);
        }

        $members = $chat->members;
        $key = array_search(auth()->user()->_id, $members);
        unset($members[$key]);
        $chat->members = $members;
        $chat->save();

        $message = $model->create([
            'chatId' => $chat->_id,
            'sender' => auth()->user()->_id,
            'content' => ' left',
            'statusMessage' => true
        ]);

        $message->sender = $message->messageSender;

        $chat->creator = $chat->chatCreator;

        $members = User::whereIn('_id', $chat->members)->get();

        $chat->members = $members;

        return response()->json([
            'success' => true,
            'message' => $message,
            'chat' => $chat
        ]);
    }

    public function destroy(Chat $chat)
    {
        if ($chat->creator == auth()->user()->_id) {

            $chatId = $chat->_id;

            $chat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat deleted',
                'chat' => [
                    '_id' => $chatId
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Chat not found. Perhaps it`s already deleted'
            ]);
        }
    }
}
