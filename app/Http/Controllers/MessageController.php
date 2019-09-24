<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Http\Requests\MessageRequest;
use App\Message;

class MessageController extends Controller
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

    public function store(MessageRequest $request, Chat $chat, Message $model)
    {
        $data = $request->get('data');

        $message = $model->create([
            'chatId' => $chat->_id,
            'sender' => auth()->user()->_id,
            'content' => $data['content'],
            'statusMessage' => $data['statusMessage']
        ]);

        $message->sender = $message->messageSender;

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
