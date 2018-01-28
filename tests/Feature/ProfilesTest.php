<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
    */

    public function a_user_has_a_profile_page()
    {
        $profile_user = create('App\User');

        $this->get(route('user.profile',['id'=>$profile_user->id]))
            ->assertSee($profile_user->name);
    }    
}