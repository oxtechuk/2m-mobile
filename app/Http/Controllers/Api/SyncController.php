<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    protected SyncService $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Heartbeat endpoint to check online status and cloud latency
     */
    public function ping()
    {
        return response()->json([
            'status' => 'online',
            'server_time' => now()->toIso8601String(),
            'app' => '2M Mobile Cloud Sync Server',
            'version' => '2.0-offline-ready'
        ]);
    }

    /**
     * Central API Endpoint: Receive incoming sync push items from local cashier instances
     */
    public function push(Request $request)
    {
        $syncKey = $request->header('X-Sync-Key');
        $expectedKey = setting('central_sync_key', config('services.sync.central_url', env('CENTRAL_SYNC_KEY')));

        if ($expectedKey && $syncKey !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'مفتـاح التزامن غيـر صحيح (Unauthorized Sync Key)'
            ], 401);
        }

        $branchId = (int) $request->input('branch_id', 1);
        $items = $request->input('items', []);

        if (empty($items)) {
            return response()->json([
                'success' => true,
                'processed_uuids' => [],
                'message' => 'لا توجد عناصر للمزامنة'
            ]);
        }

        $result = $this->syncService->processIncomingSyncPush($items, $branchId);

        return response()->json($result);
    }

    /**
     * Central API Endpoint: Deliver delta catalog updates (Products, Categories, Prices) to local instances
     */
    public function pull(Request $request)
    {
        $since = $request->input('since');
        
        $queryProducts = DB::table('products');
        $queryCategories = DB::table('categories');
        $queryCustomers = DB::table('customers');

        if ($since) {
            $queryProducts->where('updated_at', '>=', $since);
            $queryCategories->where('updated_at', '>=', $since);
            $queryCustomers->where('updated_at', '>=', $since);
        }

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'products' => $queryProducts->get(),
            'categories' => $queryCategories->get(),
            'customers' => $queryCustomers->get(),
        ]);
    }

    /**
     * Web UI Endpoint: Trigger local push sync manually from browser
     */
    public function triggerLocalPush()
    {
        $res = $this->syncService->pushLocalChanges();
        return response()->json($res);
    }
}
