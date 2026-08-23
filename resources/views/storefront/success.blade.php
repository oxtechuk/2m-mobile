@extends('layouts.storefront')

@section('title', 'تم تسجيل طلبك بنجاح | متجر تمورو')

@section('content')
<div class="bg-white py-12">
    <div class="max-w-4xl mx-auto px-4 text-center">
        
        <!-- Big Success Checkmark -->
        <div class="w-20 h-20 rounded-full bg-green-50 text-green-500 border border-green-100 flex items-center justify-center text-3xl mx-auto mb-6 shadow-sm">
            ✓
        </div>

        <h1 class="text-3xl font-black text-[#102A63]">تم تسجيل طلبك بنجاح!</h1>
        <p class="text-slate-500 font-semibold mt-2 text-sm max-w-md mx-auto leading-relaxed">
            شكرًا لشرائك من متجر تمورو. رقم طلبك هو <span class="text-brand-blue font-extrabold">{{ $order->order_number }}</span>. جاري تجهيز الطلب ومراجعته حالياً.
        </p>

        <!-- Order details block -->
        <div class="bg-slate-50 border border-slate-200 rounded-3xl p-6 md:p-8 text-right grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
            <div>
                <h3 class="text-xs font-bold text-slate-400 mb-3">بيانات الشحن والتوصيل</h3>
                <ul class="text-xs font-semibold text-slate-600 space-y-2.5">
                    <li><b class="text-slate-800">الاسم:</b> {{ $order->customer_name }}</li>
                    <li><b class="text-slate-800">رقم الهاتف:</b> {{ $order->customer_phone }}</li>
                    <li><b class="text-slate-800">المحافظة:</b> {{ $order->shipping_governorate }}</li>
                    <li><b class="text-slate-800">العنوان بالتفصيل:</b> {{ $order->shipping_address }}</li>
                </ul>
            </div>
            <div class="border-t md:border-t-0 md:border-r border-slate-200 pr-0 md:pr-6">
                <h3 class="text-xs font-bold text-slate-400 mb-3">تفاصيل الدفع والحساب</h3>
                <ul class="text-xs font-semibold text-slate-600 space-y-2.5">
                    <li>
                        <b class="text-slate-800">طريقة الدفع:</b> 
                        @if($order->payment_method === 'cod')
                            الدفع عند الاستلام
                        @elseif($order->payment_method === 'instapay')
                            تحويل انستاباي (InstaPay)
                        @else
                            تحويل محفظة إلكترونية
                        @endif
                    </li>
                    <li>
                        <b class="text-slate-800">حالة الدفع:</b> 
                        @if($order->payment_method === 'cod')
                            <span class="text-slate-500">معلق (يتم الدفع للمندوب)</span>
                        @else
                            <span class="text-green-600">بانتظار التحقق من إثبات التحويل</span>
                        @endif
                    </li>
                    <li><b class="text-slate-800">رسوم التوصيل:</b> {{ number_format($order->shipping_fee, 2) }} ج.م</li>
                    <li><b class="text-slate-800">الإجمالي الكلي للطلب:</b> <span class="text-brand-coral font-black">{{ number_format($order->total, 2) }} ج.م</span></li>
                </ul>
            </div>
        </div>

        <!-- Digital downloads segment -->
        @if($downloads->isNotEmpty())
            <div class="bg-blue-50/50 border border-blue-100 rounded-3xl p-6 md:p-8 mt-8 text-right">
                <h3 class="text-sm font-bold text-[#102A63] mb-3 flex items-center gap-1.5">
                    <span class="w-1.5 h-4 bg-brand-blue rounded-full"></span>
                    تحميل الشيتات والملفات الرقمية
                </h3>
                <p class="text-xs text-slate-500 font-semibold leading-relaxed mb-6">
                    طلبك يحتوي على ملفات رقمية قابلة للطباعة. بمجرد تأكيد الإدارة لاستلام قيمة التحويل (خلال دقائق)، ستتمكن من تحميل الملفات من حسابك الشخصي أو مباشرة عبر الروابط التالية:
                </p>

                <div class="flex flex-col gap-3">
                    @foreach($downloads as $download)
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">📄</span>
                                <div class="flex flex-col leading-tight">
                                    <h4 class="text-xs font-bold text-slate-700">{{ $download->product->name }}</h4>
                                    <span class="text-[10px] text-slate-400 font-semibold mt-1">صلاحية التحميل: 5 مرات كحد أقصى • PDF</span>
                                </div>
                            </div>
                            
                            @if($order->payment_method === 'cod')
                                <span class="text-[10px] text-slate-400 font-bold bg-slate-100 py-1.5 px-4 rounded-xl">
                                    متاح التحميل بعد تسليم الطلب المادي
                                </span>
                            @else
                                <a href="{{ route('download', $download->token) }}" class="bg-[#102A63] hover:bg-slate-800 text-white font-bold text-xs py-2 px-6 rounded-xl transition-colors whitespace-nowrap">
                                    تحميل الملف الآن
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- WhatsApp confirmation helper -->
        <div class="mt-12 flex flex-col items-center gap-4 bg-slate-50/50 p-6 rounded-3xl border border-slate-200">
            <span class="text-2xl">💬</span>
            <div class="flex flex-col gap-1">
                <h3 class="text-xs font-bold text-slate-800">هل تود تسريع عملية التأكيد وتفعيل الملفات؟</h3>
                <p class="text-[10px] text-slate-400 font-semibold max-w-md">
                    يمكنك إرسال رسالة مباشرة إلى رقم الواتساب الخاص بالدعم الفني مع رقم طلبك <b>({{ $order->order_number }})</b> وصورة التحويل لتأكيد الطلب وشحنه فوراً.
                </p>
            </div>
            <a href="https://wa.me/201012345678?text={{ urlencode('مرحباً، أود تأكيد الطلب رقم ' . $order->order_number . ' وإرسال إثبات الدفع.') }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-bold text-xs py-2.5 px-6 rounded-full flex items-center gap-1.5 transition-colors shadow-sm">
                تأكيد عبر الواتساب الآن
            </a>
        </div>

        <a href="{{ route('home') }}" class="inline-block mt-8 font-extrabold text-xs text-brand-blue hover:underline">العودة للرئيسية وتصفح المزيد</a>

    </div>
</div>
@endsection
