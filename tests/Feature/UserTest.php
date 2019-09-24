<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class UserTest
 * @package Tests\Feature
 */
class UserTest extends TestCase
{
    public $testName = 'test';
    public $testPassword = '1234';
    public $userStructure = [
        'success',
        'message',
        'user' => [
            '_id',
            'createdAt',
            'updatedAt',
            'username',
            'firstName',
            'lastName',
            'chats' => [],
            'messagesCount'
        ]
    ];

    /**
     * Get all users test
     */
    public function testGetUsers()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->getJson('/api/users', [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'message',
            'users'
        ]);

        $users = $response->json('users');
        foreach ($users as $user) {
            $this->assertArrayHasKey('_id', $user);
            $this->assertArrayHasKey('username', $user);
            $this->assertArrayHasKey('firstName', $user);
            $this->assertArrayHasKey('lastName', $user);
        }
    }

    /**
     * Get me (current user) test
     */
    public function testGetMe()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->getJson('/api/users/me', [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure($this->userStructure);

        $user = $response->json('user');
        $this->assertEquals($this->testName, $user['username']);

        if($user['chats']) {
            foreach ($user['chats'] as $chat) {
                $this->assertArrayHasKey('_id', $chat);
                $this->assertArrayHasKey('createdAt', $chat);
                $this->assertArrayHasKey('updatedAt', $chat);
                $this->assertArrayHasKey('title', $chat);

                $creator = $chat['creator'];
                $this->assertArrayHasKey('_id', $creator);
                $this->assertArrayHasKey('username', $creator);
                $this->assertArrayHasKey('firstName', $creator);
                $this->assertArrayHasKey('lastName', $creator);

                $this->assertEquals($user['_id'], $creator['_id']);
                $this->assertEquals($user['username'], $creator['username']);
                $this->assertEquals($user['firstName'], $creator['firstName']);
                $this->assertEquals($user['lastName'], $creator['lastName']);

                if ($chat['members']) {
                    foreach ($chat['members'] as $member) {
                        $this->assertArrayHasKey('_id', $member);
                        $this->assertArrayHasKey('username', $member);
                        $this->assertArrayHasKey('firstName', $member);
                        $this->assertArrayHasKey('lastName', $member);

                        $this->assertNotEquals($user['_id'], $member['_id']);
                        $this->assertEquals($user['username'], $member['username']);
                    }
                }
            }
        }
    }

    /**
     * Edit current user test
     */
    public function testPostMe()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->postJson('/api/users/me', [
            'data' => [
                'username' => $this->testName,
                'firstName' => 'test',
                'lastName' => 'test',
            ]
        ], [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'message',
            'user' => [
                '_id',
                'createdAt',
                'updatedAt',
                'username',
                'firstName',
                'lastName',
            ]
        ]);

        $user = $response->json('user');
        $this->assertEquals($this->testName, $user['username']);
        $this->assertEquals('test', $user['firstName']);
        $this->assertEquals('test', $user['lastName']);
    }

    /**
     * Get user by id test
     */
    public function testGetUserById()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');
        $user = $response->json('user');

        $response = $this->getJson('/api/users/' . $user['_id'], [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure($this->userStructure);

        $user = $response->json('user');
        $this->assertEquals($this->testName, $user['username']);

        if($user['chats']) {
            foreach ($user['chats'] as $chat) {
                $this->assertArrayHasKey('_id', $chat);
                $this->assertArrayHasKey('createdAt', $chat);
                $this->assertArrayHasKey('updatedAt', $chat);
                $this->assertArrayHasKey('title', $chat);

                $creator = $chat['creator'];
                $this->assertArrayHasKey('_id', $creator);
                $this->assertArrayHasKey('username', $creator);
                $this->assertArrayHasKey('firstName', $creator);
                $this->assertArrayHasKey('lastName', $creator);

                $this->assertEquals($user['_id'], $creator['_id']);
                $this->assertEquals($user['username'], $creator['username']);
                $this->assertEquals($user['firstName'], $creator['firstName']);
                $this->assertEquals($user['lastName'], $creator['lastName']);

                if ($chat['members']) {
                    foreach ($chat['members'] as $member) {
                        $this->assertArrayHasKey('_id', $member);
                        $this->assertArrayHasKey('username', $member);
                        $this->assertArrayHasKey('firstName', $member);
                        $this->assertArrayHasKey('lastName', $member);

                        $this->assertNotEquals($user['_id'], $member['_id']);
                        $this->assertNotEquals($user['username'], $member['username']);
                    }
                }
            }
        }
    }
}
