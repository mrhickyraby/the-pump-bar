<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerFinancialReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_report_shows_computed_financial_totals_and_commission(): void
    {
        [$owner, $date] = $this->seedOwnerFinancialDay();

        $response = $this->actingAs($owner)->get('/reports?from='.$date->toDateString().'&to='.$date->toDateString());

        $response->assertOk();
        $response->assertSee('₱1,500.00');
        $response->assertSee('₱450.00');
        $response->assertSee('₱1,050.00');
        $response->assertSee('₱300.00');
        $response->assertSee('₱175.00');
        $response->assertSee('₱575.00');
        $response->assertSee('₱950.00');
    }

    public function test_owner_dashboard_filter_uses_same_financial_values_as_report(): void
    {
        [$owner, $date] = $this->seedOwnerFinancialDay();

        $response = $this->actingAs($owner)->get('/dashboard?period=day&date='.$date->toDateString());

        $response->assertOk();
        $response->assertSee('Showing owner financial analytics for');
        $response->assertSee('₱1,500.00');
        $response->assertSee('₱450.00');
        $response->assertSee('₱1,050.00');
        $response->assertSee('₱300.00');
        $response->assertSee('₱175.00');
        $response->assertSee('₱575.00');
    }

    /**
     * @return array{0: User, 1: Carbon}
     */
    private function seedOwnerFinancialDay(): array
    {
        $date = Carbon::parse('2026-10-22');
        $branch = Branch::factory()->create();
        $owner = User::factory()->create([
            'role' => 'owner',
            'branch_id' => $branch->id,
        ]);

        $ingredient = Ingredient::create([
            'name' => 'Protein',
            'unit' => 'grams',
            'cost_per_unit' => 30,
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Test Shake',
            'category' => 'Protein Shake',
            'size' => '500ml',
            'flavor' => 'Vanilla',
            'price' => 100,
            'description' => 'Test product',
            'is_active' => true,
        ]);

        Recipe::create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 1,
        ]);

        $sale = Sale::create([
            'branch_id' => $branch->id,
            'user_id' => $owner->id,
            'transaction_number' => 'TPB-TEST-001',
            'total' => 1500,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);
        $sale->created_at = $date->copy()->setTime(11, 0);
        $sale->save();

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 15,
            'unit_price' => 100,
            'total' => 1500,
        ]);

        Expense::create([
            'branch_id' => $branch->id,
            'user_id' => $owner->id,
            'category' => 'Operations',
            'amount' => 300,
            'description' => 'Daily operations',
            'occurred_at' => $date->copy()->startOfDay(),
        ]);

        return [$owner, $date];
    }
}
