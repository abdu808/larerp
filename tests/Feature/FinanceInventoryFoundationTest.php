<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
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
        $this->assertSame(Expense::STATUSES['paid'], $expense->status_label);
        $this->assertSame('750.00 SAR', $account->balance_summary);
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
        $this->assertFalse($item->needs_restock);
        $this->assertSame('25.00 '.$item->unit, $item->quantity_summary);
        $this->assertSame(25.0, $movement->signed_quantity);
        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $item->id,
            'type' => 'in',
            'quantity' => 25,
        ]);
    }

    public function test_expense_amount_must_be_greater_than_zero(): void
    {
        $account = FinancialAccount::create([
            'code' => 'CASH-002',
            'name' => 'حساب نقدي',
            'type' => 'cash',
            'opening_balance' => 100,
            'current_balance' => 100,
            'is_active' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expense amount must be greater than zero.');

        Expense::create([
            'financial_account_id' => $account->id,
            'amount' => 0,
            'expense_date' => now()->toDateString(),
            'status' => 'paid',
        ]);
    }

    public function test_inventory_movement_quantity_must_be_greater_than_zero(): void
    {
        $item = InventoryItem::create([
            'sku' => 'ITEM-002',
            'name' => 'سلة غذائية',
            'category' => 'مواد غذائية',
            'unit' => 'سلة',
            'minimum_quantity' => 10,
            'current_quantity' => 25,
            'is_active' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Inventory movement quantity must be greater than zero.');

        InventoryMovement::create([
            'inventory_item_id' => $item->id,
            'type' => 'out',
            'quantity' => 0,
            'movement_date' => now()->toDateString(),
        ]);
    }

    public function test_out_inventory_movement_uses_positive_quantity_with_negative_signed_display(): void
    {
        $item = InventoryItem::create([
            'sku' => 'ITEM-003',
            'name' => 'بطانية',
            'category' => 'مستلزمات',
            'unit' => 'قطعة',
            'minimum_quantity' => 5,
            'current_quantity' => 5,
            'is_active' => true,
        ]);

        $movement = InventoryMovement::create([
            'inventory_item_id' => $item->id,
            'type' => 'out',
            'quantity' => 3,
            'movement_date' => now()->toDateString(),
        ]);

        $this->assertSame(InventoryMovement::TYPES['out'], $movement->type_label);
        $this->assertSame(-3.0, $movement->signed_quantity);
        $this->assertTrue($item->needs_restock);
    }
}
