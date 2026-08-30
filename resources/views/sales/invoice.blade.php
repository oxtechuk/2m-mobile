<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $sale->invoice_number }} - 2M Mobile</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* 80mm Roll (Xprinter XP-370BM) 1-Page Layout - ZERO WASTE */
        @page {
            size: 80mm auto;
            margin: 0mm !important;
        }

        html, body {
            background-color: #ffffff;
            color: #000000;
            font-family: 'Cairo', system-ui, -apple-system, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            width: 72mm;
            max-width: 72mm;
            margin: 0 auto;
            padding: 0mm 1mm;
            height: auto !important;
            max-height: max-content !important;
            overflow: hidden !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .receipt-container {
            width: 100%;
            background: #ffffff;
            margin: 0 auto;
            padding-bottom: 2mm;
        }

        .font-mono {
            font-family: 'Share Tech Mono', monospace, 'Courier New', Courier;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 900; }

        .divider {
            border-top: 1px dashed #000000;
            margin: 2px 0;
        }
        .divider-double {
            border-top: 2px solid #000000;
            margin: 3px 0;
        }

        /* Store Header */
        .store-logo {
            max-height: 36px;
            max-width: 90px;
            margin: 0 auto 1px auto;
            display: block;
            object-fit: contain;
        }
        .store-name {
            font-size: 14px;
            font-weight: 900;
            margin-bottom: 0px;
            line-height: 1.1;
        }
        .store-subtitle {
            font-size: 8.5px;
            font-weight: 600;
            color: #222;
        }

        /* Meta Table */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin: 1.5px 0;
            table-layout: fixed;
        }
        .meta-table td {
            padding: 0.75px 0;
            vertical-align: middle;
        }
        .meta-label {
            font-weight: 700;
            color: #000;
            white-space: nowrap;
            width: 32%;
        }
        .meta-value {
            font-weight: 700;
            text-align: left;
            direction: ltr;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin: 1.5px 0;
            table-layout: fixed;
        }
        .items-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2px 1px;
            font-weight: 900;
            font-size: 8.5px;
            background: #f4f4f4;
        }
        .items-table td {
            padding: 2px 1px;
            vertical-align: top;
            border-bottom: 0.5px dotted #aaa;
            word-wrap: break-word;
        }

        /* Totals */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-top: 1.5px;
        }
        .totals-table td {
            padding: 1px 0;
        }
        .grand-total-box {
            border: 1.5px solid #000;
            background: #f4f4f4;
            padding: 3px 5px;
            margin: 3px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11.5px;
            font-weight: 900;
        }

        /* QR Code */
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 3px auto 1px auto;
        }
        .qr-section img, .qr-section canvas {
            max-width: 48px;
            max-height: 48px;
            margin: auto;
        }

        /* Footer */
        .footer-terms {
            font-size: 8px;
            text-align: center;
            margin-top: 2px;
            line-height: 1.2;
        }

        /* Screen Action Bar */
        .no-print-bar {
            max-width: 72mm;
            margin: 10px auto;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .btn-direct-print {
            background-color: #10B981;
            color: #ffffff !important;
            border: none;
            padding: 10px 14px;
            font-family: 'Cairo', sans-serif;
            font-size: 12px;
            font-weight: 800;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
            transition: all 0.15s ease;
        }
        .btn-direct-print:hover {
            background-color: #059669;
        }
        .btn-print {
            background-color: #D41414;
            color: #ffffff !important;
            border: none;
            padding: 8px 12px;
            font-family: 'Cairo', sans-serif;
            font-size: 11px;
            font-weight: 800;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-back {
            background-color: #374151;
            color: #ffffff !important;
            border: none;
            padding: 7px 10px;
            font-family: 'Cairo', sans-serif;
            font-size: 10px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                width: 72mm !important;
                max-width: 72mm !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body onload="initPrint()">

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print-bar">
        <!-- Direct Raw Thermal Hardware Print (0 Waste) -->
        <button type="button" onclick="sendDirectPrint()" id="directPrintBtn" class="btn-direct-print">
            <i class="fa-solid fa-bolt"></i>
            <span>طباعة حرارية فورية (0 هدر - متتالي)</span>
        </button>

        <!-- Standard Browser Print Dialog -->
        <button type="button" onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i>
            <span>معاينة ونافذة الطباعة (Ctrl+P)</span>
        </button>

        <a href="{{ route('pos.index') }}" class="btn-back">
            <i class="fa-solid fa-cash-register"></i>
            <span>العودة لشاشة الكاشير (POS)</span>
        </a>
    </div>

    <!-- Receipt Content (Fit on 1 Continuous Thermal Page) -->
    <div class="receipt-container">
        
        <!-- Header -->
        <div class="text-center">
            @if(setting('store_logo'))
                <img src="{{ asset('storage/' . setting('store_logo')) }}" class="store-logo">
            @endif
            <h1 class="store-name">{{ setting('store_name', '2M Mobile') }}</h1>
            <p class="store-subtitle">مبيعات وصيانة الهواتف الذكية والإكسسوارات</p>
            <p style="font-size: 8.5px; font-weight: 600;">{{ $sale->branch->name ?? 'الفرع الرئيسي' }} - هاتف: {{ $sale->branch->phone ?? setting('store_phone', '01011111111') }}</p>
        </div>

        <div class="divider"></div>

        <!-- Meta Table -->
        <table class="meta-table">
            <tr>
                <td class="meta-label">رقم الفاتورة:</td>
                <td class="meta-value font-mono font-black">#{{ $sale->invoice_number }}</td>
            </tr>
            <tr>
                <td class="meta-label">التاريخ والوقت:</td>
                <td class="meta-value font-mono" style="font-size: 9px;">{{ $sale->created_at->format('Y-m-d h:i A') }}</td>
            </tr>
            <tr>
                <td class="meta-label">الكاشير:</td>
                <td class="text-left font-bold" style="font-size: 9.5px;">{{ $sale->user->name ?? 'الكاشير' }}</td>
            </tr>
            <tr>
                <td class="meta-label">العميل:</td>
                <td class="text-left font-bold" style="font-size: 9.5px;">{{ $sale->customer->name ?? 'عميل نقدي عام' }}</td>
            </tr>
            @if($sale->customer && $sale->customer->phone)
            <tr>
                <td class="meta-label">هاتف العميل:</td>
                <td class="meta-value font-mono">{{ $sale->customer->phone }}</td>
            </tr>
            @endif
        </table>

        <div class="divider"></div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-right" style="width: 42%;">الصنف</th>
                    <th class="text-center" style="width: 12%;">الكمية</th>
                    <th class="text-center" style="width: 23%;">السعر</th>
                    <th class="text-left" style="width: 23%;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td class="text-right">
                            <span class="font-bold block" style="line-height: 1.15;">{{ $item->product->name ?? 'منتج' }}</span>
                            @php
                                $serials = \App\Models\ProductSerial::where('sale_item_id', $item->id)->pluck('serial_number');
                            @endphp
                            @if($serials->count() > 0)
                                <span class="font-mono block" style="font-size: 7.5px; color: #333;">
                                    SN: {{ $serials->implode(', ') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center font-mono font-bold">{{ $item->quantity }}</td>
                        <td class="text-center font-mono">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-left font-mono font-bold" style="white-space: nowrap;">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals -->
        <table class="totals-table">
            <tr>
                <td class="font-bold">المجموع الفرعي:</td>
                <td class="text-left font-mono font-bold" style="white-space: nowrap;">{{ number_format($sale->subtotal, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
            </tr>
            @if($sale->tax > 0)
            <tr>
                <td class="font-bold">الضريبة (14%):</td>
                <td class="text-left font-mono" style="white-space: nowrap;">{{ number_format($sale->tax, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
            </tr>
            @endif
            @if($sale->discount > 0)
            <tr>
                <td class="font-bold">الخصم:</td>
                <td class="text-left font-mono font-bold" style="white-space: nowrap;">- {{ number_format($sale->discount, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
            </tr>
            @endif
        </table>

        <!-- Grand Total Highlight Box -->
        <div class="grand-total-box">
            <span>الإجمالي النهائي:</span>
            <span class="font-mono" style="white-space: nowrap;">{{ number_format($sale->total, 2) }} {{ setting('default_currency', 'ج.م') }}</span>
        </div>

        <!-- QR Code -->
        <div class="qr-section text-center">
            <div id="invoice-qrcode"></div>
            <span class="font-mono font-bold block mt-0.5" style="font-size: 8px; letter-spacing: 0.5px;">#{{ $sale->invoice_number }}</span>
        </div>

        <div class="divider"></div>

        <!-- Footer Terms -->
        <div class="footer-terms">
            <p class="font-bold">شكراً لتعاملكم معنا! 🌟</p>
            <p>• البضاعة المباعة ترد وتستبدل خلال 14 يوماً بالفاتورة.</p>
            <p style="font-size: 8px; font-weight: 800; color: #000; margin-top: 3px;">
                برمجة وتطوير شركة <strong>Ox Tech</strong>
            </p>
            <p style="font-size: 7.5px; font-family: 'Share Tech Mono', monospace; color: #333; direction: ltr; font-weight: bold;">
                🌐 oxtech.uk
            </p>
        </div>

    </div>

    <!-- QR Code Script & Auto Print -->
    <script>
        let hasTriggeredPrint = false;

        function initPrint() {
            try {
                const qrData = "2M-INV|{{ $sale->invoice_number }}|{{ $sale->total }}|{{ $sale->created_at->toIso8601String() }}";
                const qrContainer = document.getElementById("invoice-qrcode");
                if (qrContainer && typeof QRCode !== 'undefined') {
                    qrContainer.innerHTML = '';
                    new QRCode(qrContainer, {
                        text: qrData,
                        width: 50,
                        height: 50,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }
            } catch (e) {
                console.error("QR Code error:", e);
            }
        }

        async function sendDirectPrint() {
            const btn = document.getElementById('directPrintBtn');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري إرسال الفاتورة للطابعة...</span>';
            btn.style.opacity = '0.7';
            btn.disabled = true;

            // 1. Try Local Print Bridge on cashier machine (http://127.0.0.1:9191/print)
            try {
                const bridgeCheck = await fetch("http://127.0.0.1:9191/status", { method: 'GET', signal: AbortSignal.timeout(600) });
                if (bridgeCheck.ok) {
                    // Fetch ESC data from server
                    const res = await fetch("{{ route('sales.direct-print', $sale->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ printer_name: 'Xprinter XP-370BM' })
                    });
                    const serverData = await res.json();
                    if (serverData.esc_data) {
                        const bridgeRes = await fetch("http://127.0.0.1:9191/print-raw", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ data: serverData.esc_data, printer: 'Xprinter XP-370BM' })
                        });
                        if (bridgeRes.ok) {
                            btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>تمت الطباعة المباشرة عبر الوسيط المحلي!</span>';
                            setTimeout(() => { btn.innerHTML = originalHTML; btn.style.opacity = '1'; btn.disabled = false; }, 2500);
                            return;
                        }
                    }
                }
            } catch (e) {
                // Local bridge not running, continue with standard pipeline
            }

            // 2. Try Backend direct print (Works on Localhost Windows)
            try {
                const res = await fetch("{{ route('sales.direct-print', $sale->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        printer_name: 'Xprinter XP-370BM'
                    })
                });

                const data = await res.json();
                btn.innerHTML = originalHTML;
                btn.style.opacity = '1';
                btn.disabled = false;

                if (data.success) {
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>تمت الطباعة الفورية بنجاح!</span>';
                    setTimeout(() => { btn.innerHTML = originalHTML; }, 2500);
                    return;
                }
            } catch (err) {
                console.warn('Direct backend print inaccessible on server, using browser print...', err);
            }

            // 3. Cloud Server Fallback: Open optimized browser print window directly
            btn.innerHTML = originalHTML;
            btn.style.opacity = '1';
            btn.disabled = false;
            window.print();
        }
    </script>

</body>
</html>