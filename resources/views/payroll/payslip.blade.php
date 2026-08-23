<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>قسيمة راتب - {{ $payroll->user->name ?? 'موظف' }} ({{ $payroll->month_name }} {{ $payroll->year }})</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Cairo', system-ui, sans-serif;
            background-color: #f3f4f6;
            color: #111827;
            padding: 20px;
        }
        .payslip-card {
            max-width: 750px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 32px;
            border: 1px solid #e5e7eb;
        }
        .font-mono { font-family: 'Share Tech Mono', monospace; }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px dashed #d1d5db;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-box {
            width: 48px;
            height: 48px;
            background: #D41414;
            color: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 900;
        }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        
        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
        }
        .info-label { color: #6b7280; font-size: 11px; font-weight: bold; margin-bottom: 2px; }
        .info-val { font-weight: bold; color: #111827; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        th { background: #f3f4f6; padding: 10px; text-align: right; font-weight: bold; color: #374151; border-bottom: 1px solid #e5e7eb; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-top: 20px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .net-salary-banner {
            background: #fef2f2;
            border: 2px solid #fecaca;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
        }
        .net-amount {
            font-size: 24px;
            font-weight: 900;
            color: #D41414;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
        }
        .sig-line {
            border-bottom: 1px dotted #9ca3af;
            height: 40px;
            margin-bottom: 8px;
        }

        .no-print {
            max-width: 750px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #D41414;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back {
            background: #fff;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 13px;
            text-decoration: none;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .payslip-card { box-shadow: none; border: none; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
            @page { size: A4 portrait; margin: 15mm; }
        }
    </style>
</head>
<body>

    <!-- Action Bar (No Print) -->
    <div class="no-print">
        <a href="{{ route('payroll.index', ['year' => $payroll->year, 'month' => $payroll->month]) }}" class="btn-back">
            <i class="fa-solid fa-arrow-right"></i> العودة للكشف
        </a>
        <button type="button" onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> طباعة إيصال الراتب (Ctrl+P)
        </button>
    </div>

    <!-- Main Payslip Document -->
    <div class="payslip-card">
        
        <!-- Header -->
        <div class="header">
            <div class="brand">
                <div class="logo-box">2M</div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 900;">{{ setting('store_name', '2M Mobile') }}</h2>
                    <p style="font-size: 12px; color: #6b7280;">قسيمة إشعار واستلام راتب موظف</p>
                </div>
            </div>
            <div style="text-align: left;">
                <span style="display: block; font-size: 11px; color: #6b7280;">الفترة المالية:</span>
                <span style="font-size: 16px; font-weight: 900; color: #D41414;">شهر {{ $payroll->month_name }} {{ $payroll->year }}</span>
                <span style="display: block; font-size: 10px; color: #9ca3af; margin-top: 2px;">رقم القسيمة: #PAY-{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <!-- Employee Info Grid -->
        <div class="grid-3">
            <div class="info-box">
                <div class="info-label">اسم الموظف:</div>
                <div class="info-val">{{ $payroll->user->name ?? '—' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">الفرع التابع له:</div>
                <div class="info-val">{{ $payroll->branch->name ?? ($payroll->user->branch->name ?? 'الفرع الرئيسي') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">المسمى الوظيفي:</div>
                <div class="info-val">{{ match($payroll->user->role ?? '') { 'admin' => 'مدير عام', 'branch_manager' => 'مدير فرع', 'cashier' => 'كاشير', 'technician' => 'فني صيانة', 'customer_service' => 'خدمة عملاء', default => 'موظف' } }}</div>
            </div>
        </div>

        <div class="grid-3" style="margin-top: 10px;">
            <div class="info-box">
                <div class="info-label">رقم الهاتف:</div>
                <div class="info-val font-mono">{{ $payroll->user->phone ?? '—' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">الرقم القومي:</div>
                <div class="info-val font-mono">{{ $payroll->user->national_id ?? '—' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">تاريخ الصرف:</div>
                <div class="info-val font-mono">{{ $payroll->paid_at ? $payroll->paid_at->format('Y-m-d') : ($payroll->status === 'paid' ? date('Y-m-d') : 'قيد الصرف') }}</div>
            </div>
        </div>

        <!-- 2 Columns: Earnings (المستحقات) vs Deductions (المستقطعات) -->
        <div class="grid-2" style="margin-top: 20px;">
            
            <!-- Earnings Column -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px;">
                <h4 style="color: #15803d; font-size: 13px; font-weight: bold; border-bottom: 1px solid #bbf7d0; padding-bottom: 6px; margin-bottom: 8px;">
                    <i class="fa-solid fa-plus-circle"></i> المستحقات والبدلات (Earnings)
                </h4>
                <table>
                    <tbody>
                        <tr>
                            <td>الراتب الأساسي</td>
                            <td class="text-left font-mono font-bold">{{ number_format($payroll->basic_salary, 2) }} ج.م</td>
                        </tr>
                        @if($payroll->total_allowances > 0)
                        <tr>
                            <td>بدلات إضافية (سكن / انتقال)</td>
                            <td class="text-left font-mono font-bold text-emerald-600">+{{ number_format($payroll->total_allowances, 2) }} ج.م</td>
                        </tr>
                        @endif
                        @if($payroll->total_bonuses > 0)
                        <tr>
                            <td>مكافآت وحوافز أداء</td>
                            <td class="text-left font-mono font-bold text-emerald-600">+{{ number_format($payroll->total_bonuses, 2) }} ج.م</td>
                        </tr>
                        @endif
                        @if($payroll->total_commissions > 0)
                        <tr>
                            <td>عمولات مبيعات وصيانة معتمدة</td>
                            <td class="text-left font-mono font-bold text-purple-600">+{{ number_format($payroll->total_commissions, 2) }} ج.م</td>
                        </tr>
                        @endif
                        <tr style="border-top: 1px solid #bbf7d0; font-weight: bold;">
                            <td style="color: #15803d;">إجمالي المستحقات:</td>
                            <td class="text-left font-mono text-emerald-700" style="font-size: 14px;">
                                {{ number_format($payroll->basic_salary + $payroll->total_allowances + $payroll->total_bonuses + $payroll->total_commissions, 2) }} ج.م
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Deductions Column -->
            <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 14px;">
                <h4 style="color: #be123c; font-size: 13px; font-weight: bold; border-bottom: 1px solid #fecdd3; padding-bottom: 6px; margin-bottom: 8px;">
                    <i class="fa-solid fa-minus-circle"></i> المستقطعات والخصومات (Deductions)
                </h4>
                <table>
                    <tbody>
                        <tr>
                            <td>سلف مسحوبة خلال الشهر</td>
                            <td class="text-left font-mono font-bold text-amber-600">-{{ number_format($payroll->total_advances, 2) }} ج.م</td>
                        </tr>
                        <tr>
                            <td>جزاءات وغياب وتأخيرات</td>
                            <td class="text-left font-mono font-bold text-rose-600">-{{ number_format($payroll->total_deductions, 2) }} ج.م</td>
                        </tr>
                        <tr style="border-top: 1px solid #fecdd3; font-weight: bold;">
                            <td style="color: #be123c;">إجمالي المستقطعات:</td>
                            <td class="text-left font-mono text-rose-700" style="font-size: 14px;">
                                -{{ number_format($payroll->total_advances + $payroll->total_deductions, 2) }} ج.م
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Net Payable Salary Banner -->
        <div class="net-salary-banner">
            <div>
                <span style="font-size: 12px; color: #7f1d1d; font-weight: bold; display: block;">صافي الراتب المستحق والمستلم:</span>
                <span style="font-size: 11px; color: #991b1b;">تم احتساب المبلغ وتسوية كافة السلف والخصومات</span>
            </div>
            <div class="net-amount font-mono">
                {{ number_format($payroll->net_salary, 2) }} <span style="font-size: 14px;">ج.م</span>
            </div>
        </div>

        <!-- Signatures Area -->
        <div class="signatures">
            <div>
                <div class="sig-line"></div>
                <p><strong>توقيع واستلام الموظف</strong></p>
                <p style="color: #9ca3af; font-size: 10px;">أقر أنا الموظف باستلام كامل مستحقاتي عن هذا الشهر</p>
            </div>
            <div>
                <div class="sig-line"></div>
                <p><strong>اعتماد المحاسب / مدير الفرع</strong></p>
                <p style="color: #9ca3af; font-size: 10px;">{{ setting('store_name', '2M Mobile') }}</p>
            </div>
        </div>

    </div>

</body>
</html>
