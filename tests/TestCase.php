<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function actingAs($user = null, $tenantUuid = null, $driver = null): User
    {
        if (is_null($user)) {
            $user = User::factory()->create();
        }
        
        Passport::actingAs(
            $user,
            ['create-servers']
        );

        return $user;
    }

}
