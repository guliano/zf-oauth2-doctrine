<?php

namespace ZFTest\OAuth2\Doctrine\ORM;

use OAuth2\Storage\UserCredentialsInterface;

class UserCredentialsTest extends BaseTest
{
    /** @dataProvider provideStorage */
    public function testCheckUserCredentials(UserCredentialsInterface $storage)
    {
        if ($storage instanceof NullStorage) {
            $this->markTestSkipped('Skipped Storage: ' . $storage->getMessage());

            return;
        }

        // correct credentials
        $this->assertTrue($storage->checkUserCredentials('oauth_test_user', 'testpass'));
        // invalid password
        $this->assertFalse($storage->checkUserCredentials('oauth_test_user', 'wronpass'));
        // invalid username
        $this->assertFalse($storage->checkUserCredentials('wrongusername', 'testpass'));

        // invalid username
        $this->assertFalse($storage->getUserDetails('wrongusername'));

        // ensure all properties are set
        $user = $storage->getUserDetails('oauth_test_user');
        $this->assertTrue($user !== false);
        $this->assertArrayHasKey('user_id', $user);
        $this->assertEquals($user['user_id'], '1');
    }

    /** @dataProvider provideStorage */
    public function testUserClaims(UserCredentialsInterface $storage)
    {
        $profile = $storage->getUserClaims('oauth_test_user', 'profile');
        $this->assertEquals(array('profile' => 'profile'), $profile);

        $email = $storage->getUserClaims('oauth_test_user', 'email');
        $this->assertEquals(array('email' => 'doctrine@zfcampus'), $email);

        $address = $storage->getUserClaims('oauth_test_user', 'address');
        $this->assertEquals(array('country' => 'US'), $address);

        $phone = $storage->getUserClaims('oauth_test_user', 'phone');
        $this->assertEquals(array('phone_number' => 'phone'), $phone);

        $this->assertFalse($storage->getUserClaims('oauth_test_user', 'invalid'));
        $this->assertFalse($storage->getUserClaims('invalid', 'invalid'));
    }
}
