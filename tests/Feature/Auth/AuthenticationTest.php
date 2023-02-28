<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\PassportClientCreator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationTest extends TestCase {

    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void {
        $this->followingRedirects()
             ->get( '/login' )
             ->assertStatus( 200 );
    }

    public function test_users_can_authenticate_using_the_login_screen(): void {
        $user = User::factory()->create();

        $response = $this->post( '/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ] );

        $this->assertAuthenticated();
        $response->assertRedirect( env( 'PRIVATEAREA_URL' ) );
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void {
        $user = User::factory()->create();

        $this->post( '/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ] );

        $this->assertGuest();
    }

    public function test_creating_client(){

        $cr = new PassportClientCreator();

        $id          = env( 'REACT_APP_CLIENT_ID' );
        $secret      = env( 'REACT_APP_CLIENT_SECRET' );
        $callbackUrl = env( 'REACT_APP_CLIENT_CALLBACK' );
        $name        = 'CLIENT_TEST';

        $cr->create($id, $secret, $callbackUrl, $name);

        $client = DB::table('oauth_clients')->where('id', $id)->first();

        $this->assertNotNull($client);
    }

    public function setUp(): void {

        parent::setUp(); // TODO: Change the autogenerated stub

        $id          = env( 'REACT_APP_CLIENT_ID' );
        $secret      = env( 'REACT_APP_CLIENT_SECRET' );
        $callbackUrl = env( 'REACT_APP_CLIENT_CALLBACK' );
        $name        = 'CLIENT_TEST';

        $client_creator = new PassportClientCreator();

        $client_creator->create( $id, $secret, $callbackUrl, $name );
    }
}
