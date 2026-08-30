<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\InventoryMovement;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')->latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $branches = Branch::all();
        return view('products.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'required|string|max:50|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'nullable|exists:branches,id',
            'opening_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'has_serials' => 'nullable|boolean',
        ]);

        $validated['has_serials'] = $request->has('has_serials');

        $product = Product::create($validated);

        // Record Initial / Opening Stock Quantity
        $branchId = $request->input('branch_id') ?: (auth()->user()->branch_id ?? 1);
        $quantity = (int) $request->input('opening_stock', 0);

        Inventory::updateOrCreate(
            ['product_id' => $product->id, 'branch_id' => $branchId],
            [
                'quantity' => $quantity,
                'last_restock_at' => $quantity > 0 ? now() : null
            ]
        );

        if ($quantity > 0) {
            InventoryMovement::create([
                'product_id' => $product->id,
                'branch_id' => $branchId,
                'type' => 'adjustment',
                'quantity' => $quantity,
                'notes' => 'رصيد افتتاحي عند إضافة المنتج',
                'created_by' => auth()->id() ?? 1,
            ]);
        }

        flash('تمت إضافة المنتج الجديد وتسجيل رصيد المخزن بنجاح.')->success();

        return redirect()->route('products.index');
    }

    public function show($id)
    {
        $product = Product::with(['category', 'inventories.branch'])->findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function edit($id)
    {
        $branchId = auth()->user()->branch_id ?? 1;
        $product = Product::with(['inventories' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->findOrFail($id);
        $categories = Category::all();
        $branches = Branch::all();
        $currentStock = $product->inventories->first()?->quantity ?? 0;
        return view('products.edit', compact('product', 'categories', 'branches', 'currentStock'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'nullable|exists:branches,id',
            'current_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'has_serials' => 'nullable|boolean',
        ]);

        $validated['has_serials'] = $request->has('has_serials');

        $product->update($validated);

        // If stock is updated from edit page
        if ($request->has('current_stock')) {
            $branchId = $request->input('branch_id') ?: (auth()->user()->branch_id ?? 1);
            $newQty = (int) $request->input('current_stock');
            $inv = Inventory::firstOrCreate(
                ['product_id' => $product->id, 'branch_id' => $branchId],
                ['quantity' => 0]
            );
            $diff = $newQty - $inv->quantity;
            if ($diff != 0) {
                $inv->quantity = $newQty;
                $inv->last_restock_at = now();
                $inv->save();

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'branch_id' => $branchId,
                    'type' => 'adjustment',
                    'quantity' => $diff,
                    'notes' => 'تعديل رصيد المخزن من صفحة تعديل المنتج',
                    'created_by' => auth()->id() ?? 1,
                ]);
            }
        }

        flash('تم تحديث بيانات المنتج ورصيد المخزن بنجاح.')->success();

        return redirect()->route('products.index');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        flash('تم حذف المنتج بنجاح.')->warning();

        return redirect()->route('products.index');
    }

    public function barcode($id)
    {
        $branchId = auth()->user()->branch_id ?? 1;
        $product = Product::with(['category', 'inventories' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->findOrFail($id);

        $inv = $product->inventories->first();
        $product->stock_quantity = $inv ? $inv->quantity : 0;

        $allProducts = Product::with(['category', 'inventories' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->latest()
            ->get()
            ->map(function ($p) {
                $inv = $p->inventories->first();
                $p->stock_quantity = $inv ? $inv->quantity : 0;
                return $p;
            });

        return view('products.barcode', [
            'initialProduct' => $product,
            'allProducts' => $allProducts
        ]);
    }

    public function barcodeStudio(Request $request)
    {
        $branchId = auth()->user()->branch_id ?? 1;
        $allProducts = Product::with(['category', 'inventories' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->latest()
            ->get()
            ->map(function ($p) {
                $inv = $p->inventories->first();
                $p->stock_quantity = $inv ? $inv->quantity : 0;
                return $p;
            });

        return view('products.barcode', [
            'initialProduct' => null,
            'allProducts' => $allProducts
        ]);
    }

    public function generateBarcode(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Generate unique standard barcode
        do {
            $barcode = '200' . str_pad($product->id, 5, '0', STR_PAD_LEFT) . rand(1000, 9999);
        } while (Product::where('barcode', $barcode)->where('id', '!=', $product->id)->exists());

        $product->barcode = $barcode;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'تم توليد وحفظ الباركود بنجاح.',
            'barcode' => $barcode,
            'product' => $product
        ]);
    }

    /**
     * Export all products to UTF-8 BOM CSV for Excel compatibility.
     */
    /**
     * Export all products sorted by Category Name for easy management in Excel.
     */
    public function export()
    {
        $products = Product::with(['category', 'inventories'])
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*')
            ->orderBy('categories.name', 'asc')
            ->orderBy('products.name', 'asc')
            ->get();

        $fileName = '2M_Products_by_Category_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Arabic support in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Structured Header Row
            fputcsv($file, [
                'القسم / الصنف',
                'اسم المنتج',
                'الباركود',
                'سعر الشراء',
                'سعر البيع',
                'سعر الجملة',
                'الكمية الحالية',
                'الحد الأدنى',
                'الرمز (SKU)',
                'الوحدة',
                'تتبع السيريال (1/0)'
            ]);

            foreach ($products as $prod) {
                $totalStock = $prod->inventories->sum('quantity');
                fputcsv($file, [
                    $prod->category->name ?? 'عام',
                    $prod->name,
                    $prod->barcode ? '="' . $prod->barcode . '"' : '',
                    $prod->cost_price,
                    $prod->selling_price,
                    $prod->wholesale_price ?? 0,
                    $totalStock,
                    $prod->minimum_stock ?? 0,
                    $prod->sku,
                    $prod->unit ?? 'قطعة',
                    $prod->has_serials ? '1' : '0',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download optimized sample CSV template for bulk product import.
     */
    public function importTemplate()
    {
        $fileName = '2M_Bulk_Products_Template.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, [
                'القسم / الصنف',
                'اسم المنتج',
                'الباركود',
                'سعر الشراء',
                'سعر البيع',
                'سعر الجملة',
                'الكمية الحالية',
                'الحد الأدنى',
                'الرمز (SKU)',
                'الوحدة',
                'تتبع السيريال (1/0)'
            ]);

            // Sample rows arranged by Categories with string-formatted barcodes for Excel compatibility
            fputcsv($file, ['هواتف ذكية', 'آيفون 15 برو ماكس 256 جيجا', '="6912345678901"', '52000', '58000', '56500', '5', '1', 'IPH15-PM-256', 'قطعة', '1']);
            fputcsv($file, ['هواتف ذكية', 'سامسونج جالكسي S24 ألترا 512 جيجا', '="6912345678902"', '48000', '54000', '52500', '4', '1', 'SAM-S24U-512', 'قطعة', '1']);
            fputcsv($file, ['شواحن وكابلات', 'شاحن أنكر نانو سريع 30 واط تايب سي', '="6912345678903"', '450', '650', '580', '25', '5', 'ANK-30W-TYPEC', 'قطعة', '0']);
            fputcsv($file, ['شواحن وكابلات', 'كابل شحن سريع أيفون قماش 1.2 متر', '="6912345678904"', '120', '220', '180', '40', '10', 'CBL-IPH-BRD', 'قطعة', '0']);
            fputcsv($file, ['إكسسوارات وسماعات', 'سماعة إيربودز برو بلوتوث لاسلكية', '="6912345678905"', '1200', '1800', '1600', '15', '3', 'AIRPODS-PRO-2', 'قطعة', '0']);
            fputcsv($file, ['إكسسوارات وسماعات', 'جراب حماية ضد الصدمات شفاف', '="6912345678906"', '50', '150', '110', '50', '10', 'CASE-SHOCK-CLR', 'قطعة', '0']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper to format and parse raw barcode inputs from CSV/Excel.
     * Prevents scientific notation (e.g. 8.80609E+12) and removes Excel formula quotes.
     */
    private function formatImportedBarcode(?string $rawBarcode): string
    {
        if ($rawBarcode === null) {
            return '';
        }

        $barcode = trim((string) $rawBarcode);

        // Strip Excel string formula prefix/suffix like ="6912345678901"
        if (str_starts_with($barcode, '="') && str_ends_with($barcode, '"')) {
            $barcode = substr($barcode, 2, -1);
        }

        $barcode = trim($barcode, " \t\n\r\0\x0B'\"=");

        if ($barcode === '') {
            return '';
        }

        // Convert scientific notation (e.g., 8.80609E+12 or 8.80609e+12 or 8.80609E12) to integer string
        if (is_numeric($barcode) && (str_contains(strtoupper($barcode), 'E+') || str_contains(strtoupper($barcode), 'E-') || (str_contains(strtoupper($barcode), 'E') && !str_contains($barcode, ' ')))) {
            $barcode = sprintf('%.0f', (float) $barcode);
        }

        return $barcode;
    }

    /**
     * Fast Bulk Import for Products & Initial Stock from CSV / Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ]);

        $filePath = $request->file('file')->getRealPath();
        
        $file = fopen($filePath, 'r');
        if (!$file) {
            flash('عفواً، تعذر قراءة ملف الإكسيل المرفوع.')->error();
            return back();
        }

        // Strip UTF-8 BOM
        $bom = fread($file, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        $createdCount = 0;
        $updatedCount = 0;
        $failedCount = 0;
        $rowNumber = 0;
        $branchId = auth()->user()->branch_id ?? 1;
        $seenBarcodes = [];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            while (($row = fgetcsv($file, 3000, ",")) !== false) {
                $rowNumber++;

                // Semicolon fallback
                if (count($row) === 1 && str_contains($row[0], ';')) {
                    $row = explode(';', $row[0]);
                }

                // Skip header row
                if ($rowNumber === 1) {
                    $firstCell = mb_strtolower(trim($row[0] ?? ''));
                    if (str_contains($firstCell, 'قسم') || str_contains($firstCell, 'صنف') || str_contains($firstCell, 'sku') || str_contains($firstCell, 'رمز')) {
                        continue;
                    }
                }

                // Check structure: (Category, Name, Barcode, Cost, Selling, Wholesale, Qty, MinStock, SKU, Unit, HasSerials)
                // OR Old structure: (SKU, Name, Category, Barcode, Cost, Selling, Wholesale, MinStock, Unit, HasSerials)
                $isNewFormat = (isset($row[1]) && !empty($row[1]));

                if ($isNewFormat && !empty($row[0]) && !empty($row[1])) {
                    $categoryName = trim($row[0] ?? 'عام');
                    $name = trim($row[1] ?? '');
                    $rawBarcode = $row[2] ?? '';
                    $costPrice = floatval($row[3] ?? 0);
                    $sellingPrice = floatval($row[4] ?? 0);
                    $wholesalePrice = floatval($row[5] ?? 0);
                    $initialQty = intval($row[6] ?? 0);
                    $minStock = intval($row[7] ?? 0);
                    $sku = trim($row[8] ?? '');
                    $unit = trim($row[9] ?? 'قطعة');
                    $hasSerials = isset($row[10]) ? (trim($row[10]) === '1' || strtolower(trim($row[10])) === 'true') : false;
                } else {
                    // Fallback
                    $sku = trim($row[0] ?? '');
                    $name = trim($row[1] ?? '');
                    $categoryName = trim($row[2] ?? 'عام');
                    $rawBarcode = $row[3] ?? '';
                    $costPrice = floatval($row[4] ?? 0);
                    $sellingPrice = floatval($row[5] ?? 0);
                    $wholesalePrice = floatval($row[6] ?? 0);
                    $initialQty = 0;
                    $minStock = intval($row[7] ?? 0);
                    $unit = trim($row[8] ?? 'قطعة');
                    $hasSerials = isset($row[9]) ? (trim($row[9]) === '1' || strtolower(trim($row[9])) === 'true') : false;
                }

                if (empty($name)) {
                    $failedCount++;
                    continue;
                }

                // Sanitize barcode
                $barcode = $this->formatImportedBarcode($rawBarcode);

                // Auto-generate SKU if empty
                if (empty($sku)) {
                    $sku = 'PRD-' . strtoupper(\Illuminate\Support\Str::random(6));
                    while (Product::where('sku', $sku)->exists()) {
                        $sku = 'PRD-' . strtoupper(\Illuminate\Support\Str::random(6));
                    }
                }

                // Category Find or Create
                $category = Category::firstOrCreate(['name' => $categoryName ?: 'عام']);

                // Find existing product by SKU or Barcode
                $product = Product::where('sku', $sku)->first();
                if (!$product && !empty($barcode)) {
                    $product = Product::where('barcode', $barcode)->first();
                }

                // Validate Barcode uniqueness to prevent DB 1062 Duplicate Entry errors
                if (!empty($barcode)) {
                    $isDuplicateInDb = Product::where('barcode', $barcode)
                        ->when($product, function ($q) use ($product) {
                            return $q->where('id', '!=', $product->id);
                        })->exists();

                    $isDuplicateInBatch = in_array($barcode, $seenBarcodes);

                    if ($isDuplicateInDb || $isDuplicateInBatch) {
                        if ($product && !empty($product->barcode)) {
                            $barcode = $product->barcode;
                        } else {
                            $barcode = '200' . str_pad(rand(100, 99999), 5, '0', STR_PAD_LEFT) . rand(100, 999);
                            while (Product::where('barcode', $barcode)->exists() || in_array($barcode, $seenBarcodes)) {
                                $barcode = '200' . str_pad(rand(100, 99999), 5, '0', STR_PAD_LEFT) . rand(100, 999);
                            }
                        }
                    }
                    $seenBarcodes[] = $barcode;
                } else {
                    if (!$product) {
                        $barcode = '200' . str_pad(rand(100, 99999), 5, '0', STR_PAD_LEFT) . rand(100, 999);
                        while (Product::where('barcode', $barcode)->exists() || in_array($barcode, $seenBarcodes)) {
                            $barcode = '200' . str_pad(rand(100, 99999), 5, '0', STR_PAD_LEFT) . rand(100, 999);
                        }
                        $seenBarcodes[] = $barcode;
                    }
                }

                if ($product) {
                    $product->update([
                        'name' => $name,
                        'barcode' => !empty($barcode) ? $barcode : $product->barcode,
                        'category_id' => $category->id,
                        'cost_price' => $costPrice ?: $product->cost_price,
                        'selling_price' => $sellingPrice ?: $product->selling_price,
                        'wholesale_price' => $wholesalePrice ?: $product->wholesale_price,
                        'minimum_stock' => $minStock,
                        'unit' => $unit ?: 'قطعة',
                        'has_serials' => $hasSerials,
                    ]);
                    $updatedCount++;
                } else {
                    $product = Product::create([
                        'sku' => $sku,
                        'name' => $name,
                        'barcode' => $barcode,
                        'category_id' => $category->id,
                        'cost_price' => $costPrice,
                        'selling_price' => $sellingPrice,
                        'wholesale_price' => $wholesalePrice,
                        'minimum_stock' => $minStock,
                        'unit' => $unit ?: 'قطعة',
                        'has_serials' => $hasSerials,
                        'is_active' => true,
                    ]);
                    $createdCount++;
                }

                // If initial quantity is specified, populate branch inventory
                if ($initialQty > 0) {
                    $inv = Inventory::firstOrCreate(
                        ['branch_id' => $branchId, 'product_id' => $product->id],
                        ['quantity' => 0]
                    );
                    $inv->quantity = $initialQty;
                    $inv->save();
                }
            }

            fclose($file);
            \Illuminate\Support\Facades\DB::commit();

            flash("✅ تم استيراد ومعالجة المنتجات بنجاح! تم إنشاء ({$createdCount}) منتج جديد، وتحديث ({$updatedCount}) منتج.")->success();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if (isset($file) && is_resource($file)) fclose($file);
            flash('حدث خطأ أثناء معالجة ملف الإكسيل: ' . $e->getMessage())->error();
        }

        return redirect()->route('products.index');
    }

    /**
     * Test printer connection and hardware test page
     */
    public function testPrinter(Request $request)
    {
        $printers = [];
        $matchedPrinter = null;
        $testResult = -1;
        $errorMessage = null;

        // Check if running on Windows OS with shell_exec enabled
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $canExec = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));

        if (!$isWindows || !$canExec) {
            return response()->json([
                'success' => false,
                'cloud_mode' => true,
                'message' => 'الخادم يعمل على استضافة سحابية (Cloud/Linux Server). الطباعة تتم مباشرة من متصفح العميل أو عبر تطبيق الوسيط المحلي.'
            ], 200);
        }

        try {
            $psCommand = 'powershell -NoProfile -ExecutionPolicy Bypass -Command "' .
                '$printers = @(Get-WmiObject -Class Win32_Printer | Select-Object Name, PortName, WorkOffline, PrinterStatus, Default); ' .
                '$target = $printers | Where-Object { ($_.Name -like \'*Xprinter*\' -or $_.Name -like \'*XP-*\') -and $_.WorkOffline -eq $false } | Select-Object -First 1; ' .
                'if (-not $target) { $target = $printers | Where-Object { $_.Name -like \'*Xprinter*\' -or $_.Name -like \'*XP-*\' } | Select-Object -First 1; } ' .
                'if (-not $target -and $printers.Count -gt 0) { $target = $printers[0]; } ' .
                '$testCode = -1; ' .
                'if ($target) { ' .
                    'try { ' .
                        '$wmi = Get-WmiObject -Class Win32_Printer | Where-Object { $_.Name -eq $target.Name } | Select-Object -First 1; ' .
                        'if ($wmi) { $res = $wmi.PrintTestPage(); $testCode = $res.ReturnValue; } ' .
                    '} catch {} ' .
                '} ' .
                'ConvertTo-Json -Compress -InputObject @{ ' .
                    'printers = $printers; ' .
                    'target = $target; ' .
                    'testCode = $testCode ' .
                '}"';

            $output = @shell_exec($psCommand);
            $result = json_decode($output, true);

            if ($result && isset($result['target']) && $result['target']) {
                $matchedPrinter = $result['target'];
                $testResult = $result['testCode'] ?? -1;
                $printers = $result['printers'] ?? [];

                return response()->json([
                    'success' => true,
                    'printer' => $matchedPrinter['Name'] ?? 'Xprinter XP-233B #2',
                    'port' => $matchedPrinter['PortName'] ?? 'USB003',
                    'status' => 'متصل (Online)',
                    'test_code' => $testResult,
                    'all_printers' => $printers,
                    'message' => 'تم الاتصال بنجاح بالطابعة المتصلة: ' . ($matchedPrinter['Name'] ?? 'Xprinter') . ' على المنفذ النشط (' . ($matchedPrinter['PortName'] ?? 'USB003') . ').'
                ]);
            }
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        return response()->json([
            'success' => false,
            'message' => 'تعذر الاتصال بالطابعة عبر السيرفر. سيتم الاعتماد على الطباعة المباشرة من المتصفح.',
            'error' => $errorMessage
        ], 200);
    }

    /**
     * Direct TSPL hardware printing for Xprinter thermal labels
     */
    public function directPrint(Request $request)
    {
        $items = $request->input('items', []);
        $config = $request->input('config', []);
        $printerName = $request->input('printer_name', '');

        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'قائمة الملصقات فارغة'], 422);
        }

        $storeName = $config['storeName'] ?? setting('store_name', '2M Mobile');
        $showStoreName = $config['showStoreName'] ?? true;
        $showProductName = $config['showProductName'] ?? true;
        $showPrice = $config['showPrice'] ?? true;

        $tspl = "";

        foreach ($items as $item) {
            $name = $item['name'] ?? 'Product';
            $code = (string)(!empty($item['barcode']) ? $item['barcode'] : (!empty($item['sku']) ? $item['sku'] : $item['id']));
            $price = number_format((float)($item['selling_price'] ?? 0), 2) . ' LE';
            $type = $item['code_type'] ?? 'qr';
            $copies = max(1, (int)($item['print_qty'] ?? 1));

            // 38mm x 25mm label (304 x 200 dots @ 203 DPI)
            $tspl .= "SIZE 38 mm, 25 mm\r\n";
            $tspl .= "GAP 2 mm, 0 mm\r\n";
            $tspl .= "DIRECTION 1\r\n";
            $tspl .= "DENSITY 12\r\n";
            $tspl .= "SPEED 4\r\n";
            $tspl .= "CLS\r\n";

            // Store Name
            if ($showStoreName && !empty($storeName)) {
                $tspl .= "TEXT 152,8,\"2\",0,1,1,\"" . addslashes(substr($storeName, 0, 20)) . "\"\r\n";
            }

            // Product Name
            if ($showProductName) {
                $tspl .= "TEXT 152,30,\"1\",0,1,1,\"" . addslashes(substr($name, 0, 25)) . "\"\r\n";
            }

            // Clean code for Barcode / QR (remove any non-ASCII characters that can crash Code128)
            $cleanBarcode = preg_replace('/[^A-Za-z0-9\-_.]/', '', $code);
            if (empty($cleanBarcode)) {
                $cleanBarcode = '2M-' . abs(crc32($name . ($item['id'] ?? '1')));
            }
            $cleanBarcode = substr($cleanBarcode, 0, 16);

            // Dynamic calculation for barcode width
            $len = strlen($cleanBarcode);
            $narrow = ($len <= 9) ? 2 : 1;
            $wide = $narrow;
            // Center barcode horizontally (approx based on width)
            $estimatedWidth = ($len * 11 + 35) * $narrow;
            $startX = max(12, (int)((304 - $estimatedWidth) / 2));

            // QR Code or Linear Barcode (Lines)
            if ($type === 'qr') {
                $tspl .= "QRCODE 105,38,M,4,A,0,\"" . addslashes($code) . "\"\r\n";
            } else {
                // High contrast Code 128 Barcode with proper quiet zone
                $tspl .= "BARCODE {$startX},42,\"128\",64,1,0,{$narrow},{$wide},\"" . addslashes($cleanBarcode) . "\"\r\n";
            }

            // Price (Bold, Centered)
            if ($showPrice) {
                $tspl .= "TEXT 152,156,\"2\",0,1,1,\"" . addslashes($price) . "\"\r\n";
            }

            $tspl .= "PRINT 1,{$copies}\r\n";
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $psScript = storage_path('app/print_raw.ps1');
        $canExec = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));

        if ($isWindows && $canExec && file_exists($psScript)) {
            $tempFile = storage_path('app/temp_labels_' . time() . '.prn');
            file_put_contents($tempFile, $tspl);

            $command = 'powershell -NoProfile -NonInteractive -ExecutionPolicy Bypass -File "' . $psScript . '" -PrinterName "' . $printerName . '" -RawFile "' . $tempFile . '"';
            $output = @shell_exec($command);
            @unlink($tempFile);

            $res = json_decode($output, true);
            if ($res && isset($res['success']) && $res['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تمت الطباعة الفورية بنجاح على طابعة Xprinter دون أي هدر في الورق!'
                ]);
            }
        }

        // Return TSPL raw data for local print bridge fallback or client-side print
        return response()->json([
            'success' => false,
            'fallback_browser' => true,
            'tspl_data' => base64_encode($tspl),
            'message' => 'الخادم يعمل بنظام Cloud/Linux. سيتم استخدام نافذة الطباعة المجهزة للملصقات الحرارية.'
        ], 200);
    }
}


