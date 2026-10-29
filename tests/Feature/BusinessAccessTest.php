<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_owner_can_view_branch_list(): void
    {
        $branch = Branch::factory()->create();
        $owner = User::factory()->create([
            'role' => 'owner',
            'branch_id' => $branch->id,
        ]);

        $response = $this->actingAs($owner)->get('/branches');

        $response->assertOk();
        $response->assertSee($branch->name);
    }

    public function test_cashier_can_use_pos_and_only_browse_active_products(): void
    {
        $branch = Branch::factory()->create();
        $cashier = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $branch->id,
        ]);
        Product::create([
            'name' => 'Available Shake',
            'category' => 'Shake',
            'size' => '500ml',
            'flavor' => 'Vanilla',
            'price' => 100,
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Hidden Shake',
            'category' => 'Shake',
            'size' => '500ml',
            'flavor' => 'Chocolate',
            'price' => 110,
            'is_active' => false,
        ]);

        $this->actingAs($cashier)
            ->get('/pos')
            ->assertOk()
            ->assertSee('Available Shake')
            ->assertSee('POS')
            ->assertDontSee('Dashboard')
            ->assertDontSee('Branches')
            ->assertDontSee('Expenses')
            ->assertDontSee('Reports');

        $this->actingAs($cashier)
            ->get('/products')
            ->assertOk()
            ->assertSee('Available Shake')
            ->assertDontSee('Hidden Shake');
    }

    public function test_cashier_cannot_open_management_or_reporting_routes(): void
    {
        $cashier = User::factory()->create(['role' => 'staff']);

        foreach (['/dashboard', '/branches', '/ingredients', '/inventory', '/expenses', '/reports', '/users'] as $path) {
            $this->actingAs($cashier)->get($path)->assertForbidden();
        }
    }

    public function test_manager_sees_only_the_assigned_branch(): void
    {
        $assignedBranch = Branch::factory()->create(['name' => 'Assigned Branch']);
        $otherBranch = Branch::factory()->create(['name' => 'Other Branch']);
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $assignedBranch->id,
        ]);

        $this->actingAs($manager)
            ->get('/branches')
            ->assertOk()
            ->assertSee('Assigned Branch')
            ->assertDontSee('Other Branch');
    }

    public function test_manager_cannot_access_owner_financial_modules(): void
    {
        $assignedBranch = Branch::factory()->create();
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $assignedBranch->id,
        ]);

        $this->actingAs($manager)->get('/expenses')->assertForbidden();
        $this->actingAs($manager)->get('/reports')->assertForbidden();
        $this->actingAs($manager)->post('/expenses', [
            'branch_id' => $assignedBranch->id,
            'category' => 'Supplies',
            'amount' => 50,
            'occurred_at' => now()->toDateString(),
        ])->assertForbidden();
        $this->assertDatabaseMissing('expenses', [
            'category' => 'Supplies',
        ]);
    }

    public function test_manager_cannot_access_owner_only_modules(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)->get('/users')->assertForbidden();
        $this->actingAs($manager)->get('/ingredients')->assertForbidden();
        $this->actingAs($manager)->post('/products', [])->assertForbidden();
    }
}
