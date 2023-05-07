<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $user = new User();

        // Test the ID getter method
        $this->assertNull($user->getId());
    }

    public function testUsername()
    {
        $user = new User();

        // Test the username setter and getter methods
        $username = 'test_user';
        $user->setUsername($username);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($username, $user->getUserIdentifier());
    }

    public function testRoles()
    {
        $user = new User();

        // Test the roles setter and getter methods
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $user->setRoles($roles);
        $this->assertEquals($roles, $user->getRoles());
    }

    public function testPassword()
    {
        $user = new User();

        // Test the password setter and getter methods
        $password = 'test_password';
        $user->setPassword($password);
        $this->assertEquals($password, $user->getPassword());
    }

    public function testToken()
    {
        $user = new User();

        // Test the token setter and getter methods
        $token = 'test_token';
        $user->setToken($token);
        $this->assertEquals($token, $user->getToken());
    }
}
