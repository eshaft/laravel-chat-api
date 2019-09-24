<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class MessageTest
 * @package Tests\Feature
 */
class MessageTest extends TestCase
{
    public $testName = 'test';
    public $testPassword = '1234';

    /**
     * Create new message test
     */
    public function testPostMessage()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->postJson('/api/chats', [
            'data' => [
                'title' => 'Test Chat'
            ]
        ], [
            'Bearer' => $token
        ]);

        $chat = $response->json('chat');

        $response = $this->postJson('/api/chats/' . $chat['_id'], [
            'data' => [
                'content' => 'Test message',
                'statusMessage' => false
            ]
        ], [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'message' => [
                '_id',
                'createdAt',
                'updatedAt',
                'content',
                'statusMessage',
                'chatId',
                'sender'
            ],
        ]);

        $message = $response->json('message');
        $sender = $message['sender'];
        $this->assertArrayHasKey('_id', $sender);
        $this->assertArrayHasKey('username', $sender);
        $this->assertArrayHasKey('lastName', $sender);
        $this->assertArrayHasKey('firstName', $sender);
        $this->assertArrayHasKey('createdAt', $sender);
        $this->assertArrayHasKey('updatedAt', $sender);

        $this->assertEquals('Test message', $message['content']);
    }
}
