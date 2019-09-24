<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class ChatTest
 * @package Tests\Feature
 */
class ChatTest extends TestCase
{
    public $testName = 'test';
    public $testName2 = 'test2';
    public $testPassword = '1234';

    /**
     * Get all chats test
     */
    public function testGetChats()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->getJson('/api/chats', [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'chats'
        ]);

        $chats = $response->json('chats');
        if ($chats) {
            foreach ($chats as $chat) {

                $this->assertArrayHasKey('_id', $chat);
                $this->assertArrayHasKey('updatedAt', $chat);
                $this->assertArrayHasKey('createdAt', $chat);
                $this->assertArrayHasKey('creator', $chat);
                $this->assertArrayHasKey('title', $chat);
                $this->assertArrayHasKey('members', $chat);

                $creator = $chat['creator'];
                $this->assertArrayHasKey('_id', $creator);
                $this->assertArrayHasKey('username', $creator);
                $this->assertArrayHasKey('firstName', $creator);
                $this->assertArrayHasKey('lastName', $creator);

                if ($chat['members']) {
                    foreach ($chat['members'] as $member) {
                        $this->assertArrayHasKey('_id', $member);
                        $this->assertArrayHasKey('username', $member);
                        $this->assertArrayHasKey('firstName', $member);
                        $this->assertArrayHasKey('lastName', $member);
                    }
                }
            }
        }
    }

    /**
     * Create new chat test
     * TODO проверить _id создателя
     */
    public function testPostChats()
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

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'message',
            'chat' => [
                'title',
                'members',
                'creator',
                '_id',
                'updatedAt',
                'createdAt'
            ]
        ]);

        $chat = $response->json('chat');
        $creator = $chat['creator'];
        $this->assertArrayHasKey('_id', $creator);
        $this->assertArrayHasKey('username', $creator);
        $this->assertArrayHasKey('firstName', $creator);
        $this->assertArrayHasKey('lastName', $creator);

        $this->assertEquals('Test Chat', $chat['title']);

        if ($chat['members']) {
            foreach ($chat['members'] as $member) {
                $this->assertArrayHasKey('_id', $member);
                $this->assertArrayHasKey('username', $member);
                $this->assertArrayHasKey('firstName', $member);
                $this->assertArrayHasKey('lastName', $member);
            }
        }
    }

    /**
     * Get my (current user) chats test
     * TODO протестировать что это действительно my и получать еще и по members
     */
    public function testGetMyChats()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->getJson('/api/chats/my', [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'chats'
        ]);

        $chats = $response->json('chats');
        if ($chats) {
            foreach ($chats as $chat) {

                $this->assertArrayHasKey('_id', $chat);
                $this->assertArrayHasKey('updatedAt', $chat);
                $this->assertArrayHasKey('createdAt', $chat);
                $this->assertArrayHasKey('creator', $chat);
                $this->assertArrayHasKey('title', $chat);
                $this->assertArrayHasKey('members', $chat);

                $creator = $chat['creator'];
                $this->assertArrayHasKey('_id', $creator);
                $this->assertArrayHasKey('username', $creator);
                $this->assertArrayHasKey('firstName', $creator);
                $this->assertArrayHasKey('lastName', $creator);

                if ($chat['members']) {
                    foreach ($chat['members'] as $member) {
                        $this->assertArrayHasKey('_id', $member);
                        $this->assertArrayHasKey('username', $member);
                        $this->assertArrayHasKey('firstName', $member);
                        $this->assertArrayHasKey('lastName', $member);
                    }
                }
            }
        }
    }

    /**
     * Get chat by id test
     */
    public function testGetChatById()
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

        $response = $this->getJson('/api/chats/' . $chat['_id'], [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'chat' => [
                'title',
                'members',
                'creator',
                '_id',
                'updatedAt',
                'createdAt'
            ]
        ]);

        $chat = $response->json('chat');

        $creator = $chat['creator'];
        $this->assertArrayHasKey('_id', $creator);
        $this->assertArrayHasKey('username', $creator);
        $this->assertArrayHasKey('firstName', $creator);
        $this->assertArrayHasKey('lastName', $creator);

        $this->assertEquals('Test Chat', $chat['title']);

        if ($chat['members']) {
            foreach ($chat['members'] as $member) {
                $this->assertArrayHasKey('_id', $member);
                $this->assertArrayHasKey('username', $member);
                $this->assertArrayHasKey('firstName', $member);
                $this->assertArrayHasKey('lastName', $member);
            }
        }
    }

    /**
     * Delete chat by id test
     */
    public function testDeleteChatById()
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

        $chatId = $chat['_id'];

        $response = $this->deleteJson('/api/chats/' . $chatId, [], [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'message',
            'chat'
        ]);

        $chat = $response->json('chat');
        $this->assertArrayHasKey('_id', $chat);

        $this->assertEquals($chatId, $chat['_id']);
    }

    /**
     * Join chat by id test
     * TODO проверить мемберов
     */
    public function testJoinChatById()
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

        $chatId = $chat['_id'];

        $response = $this->postJson('/api/signup', [
            'username' => $this->testName2,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->getJson('/api/chats/' . $chatId . '/join', [
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
            'chat'
        ]);

        $message = $response->json('message');
        $sender = $message['sender'];
        $this->assertArrayHasKey('_id', $sender);
        $this->assertArrayHasKey('username', $sender);
        $this->assertArrayHasKey('lastName', $sender);
        $this->assertArrayHasKey('firstName', $sender);
        $this->assertArrayHasKey('createdAt', $sender);
        $this->assertArrayHasKey('updatedAt', $sender);

        $chat = $response->json('chat');
        $this->assertArrayHasKey('_id', $chat);
        $this->assertArrayHasKey('createdAt', $chat);
        $this->assertArrayHasKey('updatedAt', $chat);
        $this->assertArrayHasKey('title', $chat);

        $this->assertEquals($chatId, $chat['_id']);

        $creator = $chat['creator'];
        $this->assertArrayHasKey('_id', $creator);
        $this->assertArrayHasKey('username', $creator);
        $this->assertArrayHasKey('firstName', $creator);
        $this->assertArrayHasKey('lastName', $creator);

        if ($chat['members']) {
            foreach ($chat['members'] as $member) {
                $this->assertArrayHasKey('_id', $member);
                $this->assertArrayHasKey('username', $member);
                $this->assertArrayHasKey('firstName', $member);
                $this->assertArrayHasKey('lastName', $member);
            }
        }
    }

    /**
     * Leave chat by id test
     * TODO проверить мемберов
     */
    public function testLeaveChatById()
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

        $chatId = $chat['_id'];

        $response = $this->postJson('/api/login', [
            'username' => $this->testName2,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->getJson('/api/chats/' . $chatId . '/join', [
            'Bearer' => $token
        ]);

        $response = $this->getJson('/api/chats/' . $chatId . '/leave', [
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
            'chat'
        ]);

        $message = $response->json('message');
        $sender = $message['sender'];
        $this->assertArrayHasKey('_id', $sender);
        $this->assertArrayHasKey('username', $sender);
        $this->assertArrayHasKey('lastName', $sender);
        $this->assertArrayHasKey('firstName', $sender);
        $this->assertArrayHasKey('createdAt', $sender);
        $this->assertArrayHasKey('updatedAt', $sender);

        $chat = $response->json('chat');
        $this->assertArrayHasKey('_id', $chat);
        $this->assertArrayHasKey('createdAt', $chat);
        $this->assertArrayHasKey('updatedAt', $chat);
        $this->assertArrayHasKey('title', $chat);

        $this->assertEquals($chatId, $chat['_id']);

        $creator = $chat['creator'];
        $this->assertArrayHasKey('_id', $creator);
        $this->assertArrayHasKey('username', $creator);
        $this->assertArrayHasKey('firstName', $creator);
        $this->assertArrayHasKey('lastName', $creator);

        if ($chat['members']) {
            foreach ($chat['members'] as $member) {
                $this->assertArrayHasKey('_id', $member);
                $this->assertArrayHasKey('username', $member);
                $this->assertArrayHasKey('firstName', $member);
                $this->assertArrayHasKey('lastName', $member);
            }
        }
    }
}
