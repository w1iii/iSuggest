<?php

namespace Tests\Feature;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuggestionTest extends TestCase
{
    use RefreshDatabase;

    private User $employee;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employee = User::factory()->create(['role' => 'Employee']);
        $this->token = $this->employee->createToken('test')->plainTextToken;
    }

    protected function authHeaders(): array
    {
        return ['Authorization' => "Bearer $this->token"];
    }

    public function test_employee_can_create_suggestion(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/suggestions', [
                'title' => 'Test Suggestion',
                'description' => 'This is a test suggestion description that is long enough',
                'category' => 'Technology',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('suggestions', [
            'title' => 'Test Suggestion',
            'user_id' => $this->employee->id,
        ]);
    }

    public function test_create_suggestion_validates_category(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/suggestions', [
                'title' => 'Test',
                'description' => 'Description text',
                'category' => 'InvalidCategory',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);
    }

    public function test_employee_can_list_own_suggestions(): void
    {
        Suggestion::factory()->count(3)->create(['user_id' => $this->employee->id]);
        Suggestion::factory()->create(); // another user's suggestion

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/suggestions');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_employee_can_update_own_suggestion(): void
    {
        $suggestion = Suggestion::factory()->create([
            'user_id' => $this->employee->id,
            'title' => 'Original Title',
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->putJson("/api/v1/suggestions/{$suggestion->id}", [
                'title' => 'Updated Title',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('suggestions', [
            'id' => $suggestion->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_employee_cannot_update_others_suggestion(): void
    {
        $otherUser = User::factory()->create(['role' => 'Employee']);
        $suggestion = Suggestion::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($this->authHeaders())
            ->putJson("/api/v1/suggestions/{$suggestion->id}", [
                'title' => 'Hacked Title',
            ]);

        $response->assertStatus(404);
    }

    public function test_employee_can_delete_own_suggestion(): void
    {
        $suggestion = Suggestion::factory()->create(['user_id' => $this->employee->id]);

        $response = $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/v1/suggestions/{$suggestion->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('suggestions', ['id' => $suggestion->id]);
    }

    public function test_employee_cannot_delete_others_suggestion(): void
    {
        $otherUser = User::factory()->create(['role' => 'Employee']);
        $suggestion = Suggestion::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/v1/suggestions/{$suggestion->id}");

        $response->assertStatus(404);
    }

    public function test_employee_can_get_user_stats(): void
    {
        Suggestion::factory()->count(2)->create([
            'user_id' => $this->employee->id,
            'status' => 'Pending',
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/suggestions/user-stats');

        $response->assertStatus(200)
            ->assertJson([
                'total' => 2,
                'pending' => 2,
            ]);
    }

    public function test_unauthenticated_user_cannot_create_suggestion(): void
    {
        $response = $this->postJson('/api/v1/suggestions', [
            'title' => 'Test',
            'description' => 'Description',
            'category' => 'Technology',
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_cannot_access_employee_suggestion_routes(): void
    {
        $admin = User::factory()->create(['role' => 'Administrator']);
        $adminToken = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $adminToken")
            ->postJson('/api/v1/suggestions', [
                'title' => 'Test',
                'description' => 'Description text',
                'category' => 'Technology',
            ]);

        $response->assertStatus(403);
    }
}
