<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ChangePasswordTest extends TestCase
{
     /**
     * change password Successfully.
     *
     * @return void
     */
    /** @test */
    public function changePassword()
    {
        $oldPassword = 'password';
        $newPassword = 'newone';

        $user = \factory(\App\User::class)->create(['password' => \Hash::make($oldPassword)]);

        $this->actingAs($user);

        $response = $this->call('POST', '/changePassword', array(
            '_token' => csrf_token(),
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'repeat_new_password' => $newPassword,
        ));
        $response->assertStatus(302);
    }

}
