<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class AvatarTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
    */
    public function unauthenticated_users_cannot_upload_a_profile_avatar()
    {
        $user1 = create('App\User');

        $this->withExceptionHandling()
            ->postJson(route('user.avatar.store',$user1->id),[
                    'avatar' => 'new avatar',
                ])  
            ->assertStatus(401);
    }

    /** @test
    */
    public function a_valid_avatar_must_be_provided()
    {
        $this->signIn($user1 = create('App\User'))
            ->withExceptionHandling()
            ->postJson(route('user.avatar.store',$user1->id),[
                    'avatar' => 'new avatar',
                ])  
            ->assertStatus(422);
    }


    /** @test
    */
    public function authticated_user_can_upload_an_avatar_image()
    {

        Storage::fake('public');
        //dd(Storage::disk('avatars'));
        $user1 = create('App\User');
        $fake_file = UploadedFile::fake()->image('avatar.jpg');
        $this->signIn($user1)
            //->withExceptionHandling()
             ->post(route('user.avatar.store',$user1->id),[
                    'avatar' => $fake_file,
                ]);

        // Assert the file was stored...
        Storage::disk('public')->assertExists('avatars/'.$fake_file->hashName());

        $this->assertEquals('avatars/'.$fake_file->hashName(),auth()->user()->avatar_location);
    }
}
