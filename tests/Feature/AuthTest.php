<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Class AuthTest
 * @package Tests\Feature
 */
class AuthTest extends TestCase
{
    public $testName = 'test';
    public $testPassword = '1234';
    public $authStructure = [
        'success',
        'message',
        'token',
        'user' => [
            '_id',
            'createdAt',
            'updatedAt',
            'username',
            'firstName',
            'lastName',
            'chats',
            'messagesCount'
        ]
    ];

    /**
     * Signup test
     */
    public function testSignup()
    {
        $response = $this->postJson('/api/signup', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'success' => true,
        ]);
        $response->assertJsonStructure($this->authStructure);

        $user = $response->json('user');

        $this->assertEquals($this->testName, $user['username']);
        $this->assertEquals(0, $user['messagesCount']);
        $this->assertEquals([], $user['chats']);
        $this->assertEquals('', $user['lastName']);
        $this->assertEquals('', $user['firstName']);
    }

    /**
     * Login test
     */
    public function testLogin()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'success' => true,
        ]);
        $response->assertJsonStructure($this->authStructure);

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
     * Logout test
     */
    public function testLogout()
    {
        $response = $this->postJson('/api/login', [
            'username' => $this->testName,
            'password' => $this->testPassword
        ]);

        $token = $response->json('token');

        $response = $this->getJson('/api/logout', [
            'Bearer' => $token
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }
}
