<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceInventoryFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_financial_account_can_have_expenses(): void
    {
        $account = FinancialAccount::create([
            'code' => 'CASH-001',
            'name' => 'الصندوق الرئيسي',
            'type' => 'cash',
            'opening_balance' => 1000,
            'current_balance' => 750,
            'is_active' => true,
        ]);

        $expense = Expense::create([
            'financial_account_id' => $account->id,
            'category' => 'تشغيل',
            'payee' => 'مورد خدمات',
            'amount' => 250,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'reference_number' => 'EXP-001',
            'status' => 'paid',
            'description' => 'مصروف تشغيلي مبدئي',
        ]);

        $this->assertTrue($expense->financialAccount->is($account));
        $this->assertTrue($account->expenses()->first()->is($expense));
        $this->assertDatabaseHas('expenses', [
            'financial_account_id' => $account->id,
            'amount' => 250,
            'status' => 'paid',
        ]);
    }

    public function test_inventory_item_can_have_movements(): void
    {
        $item = InventoryItem::create([
            'sku' => 'ITEM-001',
            'name' => 'سلة غذائية',
            'category' => 'مواد غذائية',
            'unit' => 'سلة',
            'minimum_quantity' => 10,
            'current_quantity' => 25,
            'is_active' => true,
        ]);

        $movement = InventoryMovement::create([
            'inventory_item_id' => $item->id,
            'type' => 'in',
            'quantity' => 25,
            'movement_date' => now()->toDateString(),
            'reference_type' => 'opening_balance',
            'reference_number' => 'INV-OPEN-001',
            'unit_cost' => 45,
            'notes' => 'رصيد افتتاحي للمخزون',
        ]);

        $this->assertTrue($movement->inventoryItem->is($item));
        $this->assertTrue($item->movements()->first()->is($movement));
        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $item->id,
            'type' => 'in',
            'quantity' => 25,
        ]);
    }
}
