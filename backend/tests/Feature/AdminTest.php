<?php

namespace Tests\Feature;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Administrator']);
        $this->adminToken = $this->admin->createToken('test')->plainTextToken;
    }

    protected function adminHeaders(): array
    {
        return ['Authorization' => "Bearer $this->adminToken"];
    }

    public function test_admin_can_view_dashboard_stats(): void
    {
        Suggestion::factory()->count(5)->create();

        $response = $this->withHeaders($this->adminHeaders())
            ->getJson('/api/v1/admin/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_ideas', 'in_review', 'implemented', 'goal', 'growth_pct',
            ]);
    }

    public function test_admin_can_list_suggestions(): void
    {
        Suggestion::factory()->count(3)->create();

        $response = $this->withHeaders($this->adminHeaders())
            ->getJson('/api/v1/admin/suggestions');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_admin_can_filter_suggestions_by_status(): void
    {
        Suggestion::factory()->create(['status' => 'Pending']);
        Suggestion::factory()->create(['status' => 'Approved']);

        $response = $this->withHeaders($this->adminHeaders())
            ->getJson('/api/v1/admin/suggestions?status=Pending');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_admin_can_update_suggestion_status(): void
    {
        $suggestion = Suggestion::factory()->create(['status' => 'Pending']);

        $response = $this->withHeaders($this->adminHeaders())
            ->patchJson("/api/v1/admin/suggestions/{$suggestion->id}/status", [
                'status' => 'Approved',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Suggestion status updated.']);

        $this->assertDatabaseHas('suggestions', [
            'id' => $suggestion->id,
            'status' => 'Approved',
        ]);
    }

    public function test_admin_can_update_suggestion_with_remarks(): void
    {
        $suggestion = Suggestion::factory()->create(['status' => 'Pending']);

        $response = $this->withHeaders($this->adminHeaders())
            ->patchJson("/api/v1/admin/suggestions/{$suggestion->id}/status", [
                'status' => 'Rejected',
                'admin_remarks' => 'Needs more detail',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('suggestions', [
            'id' => $suggestion->id,
            'status' => 'Rejected',
            'admin_remarks' => 'Needs more detail',
        ]);
    }

    public function test_admin_can_update_invalid_status(): void
    {
        $suggestion = Suggestion::factory()->create(['status' => 'Pending']);

        $response = $this->withHeaders($this->adminHeaders())
            ->patchJson("/api/v1/admin/suggestions/{$suggestion->id}/status", [
                'status' => 'NonExistent',
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_list_employees(): void
    {
        User::factory()->count(3)->create(['role' => 'Employee']);

        $response = $this->withHeaders($this->adminHeaders())
            ->getJson('/api/v1/admin/employees');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_employee(): void
    {
        $response = $this->withHeaders($this->adminHeaders())
            ->postJson('/api/v1/admin/employees', [
                'name' => 'New Employee',
                'email' => 'newemployee@example.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'newemployee@example.com',
            'role' => 'Employee',
        ]);
    }

    public function test_admin_can_view_activity(): void
    {
        Suggestion::factory()->count(3)->create();

        $response = $this->withHeaders($this->adminHeaders())
            ->getJson('/api/v1/admin/dashboard/activity');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_categories(): void
    {
        Suggestion::factory()->create(['category' => 'Technology']);
        Suggestion::factory()->create(['category' => 'Workplace']);

        $response = $this->withHeaders($this->adminHeaders())
            ->getJson('/api/v1/admin/dashboard/categories');

        $response->assertStatus(200);
    }

    public function test_employee_cannot_access_admin_routes(): void
    {
        $employee = User::factory()->create(['role' => 'Employee']);
        $employeeToken = $employee->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $employeeToken")
            ->getJson('/api/v1/admin/dashboard/stats');

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_admin_routes(): void
    {
        $superAdmin = User::factory()->create(['role' => 'Super Administrator']);
        $superToken = $superAdmin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $superToken")
            ->getJson('/api/v1/admin/dashboard/stats');

        $response->assertStatus(200);
    }
}
