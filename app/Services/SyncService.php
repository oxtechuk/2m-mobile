<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CashShift;
use App\Models\Expense;

class SyncService
{
    /**
     * Queue an offline change locally for synchronization
     */
    public function queueOfflineChange(string $entityType, string $entityUuid, string $action, array $payload): bool
    {
        try {
            DB::table('sync_queues')->insert([
                'uuid' => (string) Str::uuid(),
                'entity_type' => $entityType,
                'entity_uuid' => $entityUuid,
                'action' => $action,
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to queue offline change: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Push pending local sync queue items to Central Cloud API (aaPanel)
     */
    public function pushLocalChanges(): array
    {
        $serverUrl = setting('central_sync_url', config('services.sync.central_url', env('CENTRAL_SYNC_URL')));
        $apiKey = setting('central_sync_key', config('services.sync.api_key', env('CENTRAL_SYNC_KEY')));

        if (empty($serverUrl)) {
            return [
                'success' => false,
                'message' => 'لم يتم إعداد رابط السيرفر السحابي CENTRAL_SYNC_URL'
            ];
        }

        $pendingItems = DB::table('sync_queues')
            ->where('status', 'pending')
            ->orderBy('id', 'asc')
            ->limit(50)
            ->get();

        if ($pendingItems->isEmpty()) {
            return [
                'success' => true,
                'synced_count' => 0,
                'message' => 'لا توجد معاملات معلقة للمزامنة'
            ];
        }

        $payloads = $pendingItems->map(function ($item) {
            return [
                'queue_uuid' => $item->uuid,
                'entity_type' => $item->entity_type,
                'entity_uuid' => $item->entity_uuid,
                'action' => $item->action,
                'data' => json_decode($item->payload, true)
            ];
        })->toArray();

        try {
            $response = Http::withHeaders([
                'X-Sync-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->timeout(15)->post(rtrim($serverUrl, '/') . '/api/v1/sync/push', [
                'branch_id' => setting('branch_id', 1),
                'items' => $payloads
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                $processedUuids = $resData['processed_uuids'] ?? [];

                DB::table('sync_queues')
                    ->whereIn('uuid', $processedUuids)
                    ->update([
                        'status' => 'synced',
                        'synced_at' => now(),
                        'updated_at' => now()
                    ]);

                return [
                    'success' => true,
                    'synced_count' => count($processedUuids),
                    'message' => "تمت مزامنة (" . count($processedUuids) . ") معاملة بنجاح مع السيرفر السحابي."
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'فشل الاتصال بالسيرفر السحابي: ' . $response->status()
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Push Sync Exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'تعذر الاتصال بالسيرفر السحابي: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process incoming sync push batch on the Central Server (aaPanel)
     */
    public function processIncomingSyncPush(array $items, int $branchId): array
    {
        $processedUuids = [];
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $queueUuid = $item['queue_uuid'] ?? null;
                $entityType = $item['entity_type'] ?? '';
                $entityUuid = $item['entity_uuid'] ?? '';
                $data = $item['data'] ?? [];

                if (!$queueUuid || empty($data)) {
                    continue;
                }

                switch ($entityType) {
                    case 'Sale':
                        $this->syncSaleRecord($entityUuid, $data, $branchId);
                        break;
                    case 'CashShift':
                        $this->syncCashShiftRecord($entityUuid, $data, $branchId);
                        break;
                    case 'Expense':
                        $this->syncExpenseRecord($entityUuid, $data, $branchId);
                        break;
                    case 'Customer':
                        $this->syncCustomerRecord($entityUuid, $data);
                        break;
                }

                $processedUuids[] = $queueUuid;
            }

            DB::commit();

            return [
                'success' => true,
                'processed_uuids' => $processedUuids,
                'count' => count($processedUuids)
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Central Sync Import Failed: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper to sync a sale transaction into Central MySQL DB
     */
    private function syncSaleRecord(string $uuid, array $data, int $branchId): void
    {
        $existing = DB::table('sales')->where('uuid', $uuid)->first();
        if ($existing) {
            return; // Already processed
        }

        $saleId = DB::table('sales')->insertGetId([
            'uuid' => $uuid,
            'invoice_number' => $data['invoice_number'] ?? ('INV-SYNC-' . strtoupper(Str::random(6))),
            'cash_shift_id' => $data['cash_shift_id'] ?? 1,
            'branch_id' => $branchId,
            'customer_id' => $data['customer_id'] ?? null,
            'cashier_id' => $data['cashier_id'] ?? 1,
            'subtotal' => $data['subtotal'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'discount_type' => $data['discount_type'] ?? 'fixed',
            'tax_rate' => $data['tax_rate'] ?? 14.00,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'total' => $data['total'] ?? 0,
            'paid_amount' => $data['paid_amount'] ?? 0,
            'payment_method' => $data['payment_method'] ?? 'cash',
            'status' => $data['status'] ?? 'completed',
            'notes' => ($data['notes'] ?? '') . ' (مستوردة أوفلاين)',
            'sync_status' => 'synced',
            'synced_at' => now(),
            'created_at' => $data['created_at'] ?? now(),
            'updated_at' => now(),
        ]);

        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $saleItem) {
                DB::table('sale_items')->insert([
                    'uuid' => (string) Str::uuid(),
                    'sale_id' => $saleId,
                    'product_id' => $saleItem['product_id'] ?? null,
                    'quantity' => $saleItem['quantity'] ?? 1,
                    'unit_price' => $saleItem['unit_price'] ?? 0,
                    'subtotal' => $saleItem['subtotal'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Deduct stock on central database
                if (!empty($saleItem['product_id'])) {
                    DB::table('products')
                        ->where('id', $saleItem['product_id'])
                        ->decrement('stock_quantity', $saleItem['quantity'] ?? 1);
                }
            }
        }
    }

    private function syncCashShiftRecord(string $uuid, array $data, int $branchId): void
    {
        DB::table('cash_shifts')->updateOrInsert(
            ['uuid' => $uuid],
            [
                'user_id' => $data['user_id'] ?? 1,
                'branch_id' => $branchId,
                'opening_balance' => $data['opening_balance'] ?? 0,
                'closing_balance' => $data['closing_balance'] ?? null,
                'status' => $data['status'] ?? 'closed',
                'opened_at' => $data['opened_at'] ?? now(),
                'closed_at' => $data['closed_at'] ?? now(),
                'sync_status' => 'synced',
                'synced_at' => now(),
                'created_at' => $data['created_at'] ?? now(),
                'updated_at' => now(),
            ]
        );
    }

    private function syncExpenseRecord(string $uuid, array $data, int $branchId): void
    {
        DB::table('expenses')->updateOrInsert(
            ['uuid' => $uuid],
            [
                'uuid' => $uuid,
                'branch_id' => $branchId,
                'user_id' => $data['user_id'] ?? 1,
                'title' => $data['title'] ?? 'مصروف أوفلاين',
                'amount' => $data['amount'] ?? 0,
                'notes' => $data['notes'] ?? '',
                'sync_status' => 'synced',
                'synced_at' => now(),
                'created_at' => $data['created_at'] ?? now(),
                'updated_at' => now(),
            ]
        );
    }

    private function syncCustomerRecord(string $uuid, array $data): void
    {
        DB::table('customers')->updateOrInsert(
            ['uuid' => $uuid],
            [
                'uuid' => $uuid,
                'name' => $data['name'] ?? 'عميل جديد',
                'phone' => $data['phone'] ?? null,
                'notes' => $data['notes'] ?? '',
                'sync_status' => 'synced',
                'synced_at' => now(),
                'created_at' => $data['created_at'] ?? now(),
                'updated_at' => now(),
            ]
        );
    }
}
