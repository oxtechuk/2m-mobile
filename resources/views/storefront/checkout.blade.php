@extends('layouts.storefront')

@section('title', 'إتمام الشراء | متجر تمورو')

@section('content')
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <h1 class="text-2xl font-black text-[#102A63] mb-8">إتمام الشراء والطلب</h1>

        <!-- Validation & Status Alerts -->
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-bold p-4 rounded-xl mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-bold p-4 rounded-xl mb-6">
                <ul class="list-disc pr-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" x-data="{ paymentMethod: 'cod' }">
            
            <!-- Checkout Form (Right side, span 7) -->
            <div class="lg:col-span-7">
                <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                    @csrf

                    <!-- 1. Customer Info -->
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-1.5">
                            <span class="w-1.5 h-4 bg-brand-blue rounded-full"></span>
                            البيانات الشخصية
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-[10px] font-bold text-slate-400">الاسم بالكامل *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', Auth::user()?->name) }}" required class="border-slate-300 rounded-lg text-xs py-2.5 px-4 text-slate-600 focus:border-brand-blue focus:ring-0">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[10px] font-bold text-slate-400">رقم الهاتف (نشط ومتاح للواتساب) *</label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required class="border-slate-300 rounded-lg text-xs py-2.5 px-4 text-slate-600 focus:border-brand-blue focus:ring-0" placeholder="مثال: 01012345678">
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 mt-4">
                            <label class="text-[10px] font-bold text-slate-400">البريد الإلكتروني *</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', Auth::user()?->email) }}" required class="border-slate-300 rounded-lg text-xs py-2.5 px-4 text-slate-600 focus:border-brand-blue focus:ring-0">
                        </div>
                    </div>

                    <!-- 2. Shipping Details -->
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-1.5">
                            <span class="w-1.5 h-4 bg-brand-turquoise rounded-full"></span>
                            تفاصيل الشحن والتوصيل
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-[10px] font-bold text-slate-400">المحافظة *</label>
                                <select name="shipping_governorate" required class="border-slate-300 rounded-lg text-xs py-2.5 px-4 text-slate-600 focus:border-brand-blue focus:ring-0">
                                    <option value="">اختر المحافظة...</option>
                                    <option value="القاهرة">القاهرة</option>
                                    <option value="الجيزة">الجيزة</option>
                                    <option value="الاسكندرية">الاسكندرية</option>
                                    <option value="القليوبية">القليوبية</option>
                                    <option value="الشرقية">الشرقية</option>
                                    <option value="الدقهلية">الدقهلية</option>
                                    <option value="الغربية">الغربية</option>
                                    <option value="البحيرة">البحيرة</option>
                                    <option value="المنوفية">المنوفية</option>
                                    <option value="دمياط">دمياط</option>
                                    <option value="بورسعيد">بورسعيد</option>
                                    <option value="الإسماعيلية">الإسماعيلية</option>
                                    <option value="السويس">السويس</option>
                                    <option value="الفيوم">الفيوم</option>
                                    <option value="بني سويف">بني سويف</option>
                                    <option value="المنيا">المنيا</option>
                                    <option value="أسيوط">أسيوط</option>
                                    <option value="سوهاج">سوهاج</option>
                                    <option value="قنا">قنا</option>
                                    <option value="الأقصر">الأقصر</option>
                                    <option value="أسوان">أسوان</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[10px] font-bold text-slate-400">المدينة / المنطقة</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" class="border-slate-300 rounded-lg text-xs py-2.5 px-4 text-slate-600 focus:border-brand-blue focus:ring-0">
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 mt-4">
                            <label class="text-[10px] font-bold text-slate-400">العنوان بالتفصيل (اسم الشارع، رقم العمارة، رقم الشقة) *</label>
                            <textarea name="shipping_address" required rows="2" class="border-slate-300 rounded-lg text-xs py-2.5 px-4 text-slate-600 focus:border-brand-blue focus:ring-0">{{ old('shipping_address') }}</textarea>
                        </div>
                    </div>

                    <!-- 3. Payment Method -->
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-1.5">
                            <span class="w-1.5 h-4 bg-brand-coral rounded-full"></span>
                            طريقة الدفع
                        </h3>
                        
                        <div class="flex flex-col gap-4">
                            <!-- Option 1: COD -->
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-white cursor-pointer select-none">
                                <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="mt-1 text-brand-blue focus:ring-0">
                                <div class="flex flex-col leading-tight">
                                    <span class="text-xs font-bold text-slate-800">الدفع عند الاستلام (COD)</span>
                                    <span class="text-[10px] text-slate-400 font-semibold mt-1">متاح فقط للمنتجات المادية (رسوم شحن إضافية 50 ج.م).</span>
                                </div>
                            </label>

                            <!-- Option 2: InstaPay -->
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-white cursor-pointer select-none">
                                <input type="radio" name="payment_method" value="instapay" x-model="paymentMethod" class="mt-1 text-brand-blue focus:ring-0">
                                <div class="flex flex-col leading-tight">
                                    <span class="text-xs font-bold text-slate-800">الدفع عبر تطبيق انستاباي (InstaPay)</span>
                                    <span class="text-[10px] text-slate-400 font-semibold mt-1">حول المبلغ الإجمالي إلى العنوان <b>{{ $instapayAddress }}</b> وارفع صورة التحويل.</span>
                                </div>
                            </label>

                            <!-- Option 3: Wallet -->
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-white cursor-pointer select-none">
                                <input type="radio" name="payment_method" value="wallet" x-model="paymentMethod" class="mt-1 text-brand-blue focus:ring-0">
                                <div class="flex flex-col leading-tight">
                                    <span class="text-xs font-bold text-slate-800">الدفع عبر محفظة إلكترونية (فودافون كاش)</span>
                                    <span class="text-[10px] text-slate-400 font-semibold mt-1">حول المبلغ الإجمالي إلى الرقم <b>{{ $walletNumber }}</b> وارفع صورة التحويل.</span>
                                </div>
                            </label>
                        </div>

                        <!-- 4. Screenshot Upload (Conditional) -->
                        <div x-show="paymentMethod === 'instapay' || paymentMethod === 'wallet'" class="mt-6 p-6 rounded-2xl bg-blue-50/50 border border-blue-100 flex flex-col gap-3" x-transition>
                            <h4 class="text-xs font-extrabold text-[#102A63]">برجاء إرفاق لقطة شاشة لإثبات الدفع (Screenshot) *</h4>
                            <p class="text-[10px] text-slate-500 font-semibold leading-relaxed">
                                من أجل تسريع مراجعة وتأكيد طلبك وتجهيز الشحنة أو إرسال ملفات الشيتات الرقمية للتحميل، يرجى إرفاق إيصال التحويل الناجح أدناه أو إرساله مباشرة لفريق الدعم على رقم الواتساب بعد الطلب.
                            </p>
                            <input type="file" name="payment_screenshot" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-brand-blue hover:file:bg-blue-100 mt-2">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-slate-400">ملاحظات إضافية على الطلب</label>
                        <textarea name="notes" rows="2" placeholder="أي ملاحظات تود كتابتها للمندوب أو الإدارة..." class="border-slate-300 rounded-lg text-xs py-2.5 px-4 text-slate-600 focus:border-brand-blue focus:ring-0">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit Order Button -->
                    <button type="submit" class="w-full bg-[#102A63] hover:bg-slate-800 text-white font-bold text-sm py-3.5 px-8 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-md">
                        تاكيد وإرسال الطلب
                    </button>
                </form>
            </div>

            <!-- Order Summary (Left side, span 5) -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 mb-6 flex items-center gap-1.5">
                        <span class="w-1.5 h-4 bg-brand-blue rounded-full"></span>
                        ملخص السلة والمنتجات
                    </h3>

                    <!-- Cart Items List -->
                    <div class="flex flex-col gap-4 max-h-[300px] overflow-y-auto pr-2">
                        @foreach($cart as $item)
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-200/60 last:border-0">
                                <div class="w-12 h-12 bg-white rounded-xl border border-slate-200 overflow-hidden p-1 flex-shrink-0 flex items-center justify-center">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" class="w-full h-full object-contain">
                                    @else
                                        📄
                                    @endif
                                </div>
                                <div class="flex-grow leading-tight">
                                    <h4 class="text-xs font-bold text-slate-700 limit-lines-1">{{ $item['name'] }}</h4>
                                    <span class="text-[10px] text-slate-400 font-semibold mt-1">الكمية: {{ $item['quantity'] }} • {{ $item['type'] === 'digital' ? 'رقمي PDF' : 'مادي' }}</span>
                                </div>
                                <span class="text-xs font-bold text-slate-800 flex-shrink-0">{{ number_format($item['price'] * $item['quantity'], 2) }} ج.م</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Receipt Calculations -->
                    <div class="mt-6 pt-6 border-t border-slate-200 flex flex-col gap-3.5 text-xs">
                        <div class="flex justify-between items-center text-slate-500 font-bold">
                            <span>المجموع الفرعي:</span>
                            <span>{{ number_format($subtotal, 2) }} ج.م</span>
                        </div>

                        <div class="flex justify-between items-center text-slate-500 font-bold">
                            <span>رسوم التوصيل والشحن:</span>
                            @if($shippingFee == 0)
                                <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded-full border border-green-100 font-black">شحن مجاني</span>
                            @else
                                <span>{{ number_format($shippingFee, 2) }} ج.م</span>
                            @endif
                        </div>

                        <!-- Free shipping progress bar -->
                        @if($physicalCount > 0 && $subtotal < 550)
                            <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-2xl flex flex-col gap-2 mt-2">
                                <div class="flex justify-between items-center text-[10px] font-extrabold text-brand-blue">
                                    <span>متبقي {{ number_format(550 - $subtotal, 2) }} جنيه للحصول على شحن مجاني!</span>
                                    <span>{{ round(($subtotal / 550) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                    <div class="bg-brand-blue h-full rounded-full transition-all duration-500" style="width: {{ ($subtotal / 550) * 100 }}%"></div>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center text-slate-800 font-black text-sm pt-4 border-t border-slate-200">
                            <span>الإجمالي الكلي للطلب:</span>
                            <span class="text-brand-coral">{{ number_format($total, 2) }} ج.م</span>
                        </div>
                    </div>
                </div>

                <!-- Safe checkout warning -->
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200 flex items-start gap-3">
                    <span class="text-2xl mt-0.5">🔒</span>
                    <div class="flex flex-col gap-1 leading-snug">
                        <h4 class="text-xs font-bold text-slate-800">حماية البيانات والأمان</h4>
                        <p class="text-[10px] text-slate-400 font-semibold leading-normal">
                            بيانات الاتصال والعنوان الخاصة بك تستخدم فقط لتوصيل الشحنة. إذا كان طلبك يحتوي على شيتات رقمية، فستصلك فوراً روابط التحميل المشفرة على بريدك الإلكتروني وحسابك الشخصي.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
