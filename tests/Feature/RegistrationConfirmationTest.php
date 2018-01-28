<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmYourEmail;
use App\User;


Class RegistrationConfirmationTest extends TestCase
{
    use DatabaseMigrations;

    // >withExceptionHandling()
            
    /** @test
    */
    public function an_email_is_sent_when_a_user_registers()
    {
        $user1 = make('App\User');
        Mail::fake();
        event(new Registered($user1));
        Mail::assertSent('App\Mail\PleaseConfirmYourEmail');

        
    }

    /** @test
    */
    public function a_user_can_verify_and_confirm_their_email_address()
    {

        $user2 = make('App\User')->toArray();
        //dd($user2);
        $user2 = array_merge($user2,[
            'password'=>'abc123',
            'password_confirmation'=>'abc123',
        ]);

        $this->post('register',$user2);

        $user2 = User::where('email',$user2['email'])->first();

        $this->assertSame(0, $user2->confirmed);

        //dd($user2->confirmation_token);

        $this->get(route('confirm.registered.email',['confirmation_token'=>$user2->confirmation_token]));

        $this->assertEquals(1, $user2->fresh()->confirmed); 
    }
}