<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Brt;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrtManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $this->token = $response->json('token');
    }

    public function test_user_can_create_brt()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->post('/api/brts', [
            'reserved_amount' => 50.00
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'user_id',
                    'brt_code',
                    'reserved_amount',
                    'status',
                    'created_at',
                    'updated_at'
                ]);

        $this->assertDatabaseHas('brts', [
            'user_id' => $this->user->id,
            'reserved_amount' => 50.00
        ]);
    }

    public function test_user_can_view_own_brts()
    {
        // Create some BRTs for the user
        Brt::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/api/brts');

        $response->assertStatus(200)
                ->assertJsonCount(3);
    }

    public function test_user_cannot_view_others_brts()
    {
        $otherUser = User::factory()->create();
        Brt::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/api/brts');

        $response->assertStatus(200)
                ->assertJsonCount(0);
    }
}