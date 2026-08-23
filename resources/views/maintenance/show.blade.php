<x-app-layout>
    <div class="space-y-6 max-w-5xl mx-auto">
        
        <!-- Header Ribbon -->
        <div class="glass-panel p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">تذكرة صيانة رقم</span>
                <h2 class="text-xl font-mono font-black text-white flex items-center gap-2">
                    {{ $request->ticket_number }}
                    <span class="px-2 py-0.5 text-[9px] rounded bg-[#D41414]/10 border border-[#D41414]/20 text-[#D41414]">
                        {{ $request->priority }}
                    </span>
                </h2>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs text-gray-400">الحالة الحالية:</span>
                <span class="px-3 py-1 text-xs font-bold rounded-full border bg-amber-500/10 border-amber-500/20 text-amber-400">
                    {{ $request->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2 Cols: Device details, Diagnosis and Actions -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Info cards row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Customer Details -->
                    <div class="glass-panel p-4 space-y-2">
                        <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">
                            <i class="fa-solid fa-user ml-1 text-gray-400"></i> بيانات العميل
                        </h3>
                        <p class="text-xs font-semibold text-white">{{ $request->customer->name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $request->customer->phone }}</p>
                        @if($request->customer->secondary_phone)
                            <p class="text-xs text-gray-500 font-mono">هاتف إضافي: {{ $request->customer->secondary_phone }}</p>
                        @endif
                    </div>

                    <!-- Device Details -->
                    <div class="glass-panel p-4 space-y-2">
                        <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">
                            <i class="fa-solid fa-mobile-button ml-1 text-gray-400"></i> بيانات الجهاز
                        </h3>
                        <p class="text-xs font-semibold text-white">{{ $request->device_type }} - {{ $request->device_model }}</p>
                        <p class="text-xs text-gray-400 font-mono">السيريال/IMEI: {{ $request->device_serial ?? 'غير متوفر' }}</p>
                        <p class="text-xs text-gray-500"><i class="fa-solid fa-calendar-day ml-1"></i> التسليم المتوقع: {{ $request->estimated_delivery ?? 'غير محدد' }}</p>
                    </div>
                </div>

                <!-- Pre-repair checklist display -->
                <div class="glass-panel p-4 space-y-3">
                    <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">حالة الفحص الأولي قبل الاستلام</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @php
                            $checklistLabels = [
                                'power' => 'باور يعمل',
                                'touch' => 'لمس شاشة سليم',
                                'screen' => 'شاشة غير مكسورة',
                                'camera_front' => 'كاميرا أمامية',
                                'camera_back' => 'كاميرا خلفية',
                                'wifi' => 'واي فاي سليم',
                                'charging' => 'شحن سليم',
                                'scratches' => 'يوجد خدوش بالظهر'
                            ];
                            $storedChecklist = $request->pre_repair_checklist ?? [];
                        @endphp
                        @foreach($checklistLabels as $key => $label)
                            @php
                                $isChecked = isset($storedChecklist[$key]) && $storedChecklist[$key] === 'ok';
                            @endphp
                            <div class="flex items-center gap-1.5 p-2 bg-[#0a0a0a] border border-white/5 rounded-lg">
                                @if($isChecked)
                                    <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                                    <span class="text-[10px] text-gray-300">{{ $label }}</span>
                                @else
                                    <i class="fa-solid fa-circle-xmark text-gray-600 text-xs"></i>
                                    <span class="text-[10px] text-gray-500 line-through">{{ $label }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Problem Description -->
                <div class="glass-panel p-4 space-y-2">
                    <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">شكوى العميل والعطل المذكور</h3>
                    <p class="text-xs text-gray-300 leading-relaxed bg-[#0a0a0a] p-3 rounded-lg border border-white/5">
                        {{ $request->problem_description }}
                    </p>
                </div>

                <!-- Spare Parts Section -->
                <div class="glass-panel p-4 space-y-4">
                    <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2"><i class="fa-solid fa-gears text-[#D41414] ml-1"></i> قطع الغيار المستهلكة في الإصلاح</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead>
                                <tr class="text-gray-500 border-b border-white/5 pb-1">
                                    <th class="pb-1">اسم قطعة الغيار</th>
                                    <th class="pb-1">الكمية</th>
                                    <th class="pb-1">سعر القطعة</th>
                                    <th class="pb-1">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($request->spareParts as $part)
                                    <tr>
                                        <td class="py-2 text-white">{{ $part->product->name }}</td>
                                        <td class="py-2 font-mono">{{ $part->quantity }}</td>
                                        <td class="py-2 font-mono">{{ number_format($part->unit_price, 2) }} ج.م</td>
                                        <td class="py-2 font-bold font-mono text-emerald-400">{{ number_format($part->total_price, 2) }} ج.م</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">لم يتم استخدام قطع غيار في عملية الإصلاح بعد.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Diagnosis updates (For Technician) -->
                @can('manage-maintenance')
                <div class="glass-panel p-4 space-y-4">
                    <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">تحديث التشخيص الفني والأسعار</h3>
                    <form method="POST" action="{{ route('maintenance.update', $request->id) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <!-- hidden bindings for required fields -->
                        <input type="hidden" name="customer_id" value="{{ $request->customer_id }}">
                        <input type="hidden" name="device_type" value="{{ $request->device_type }}">
                        <input type="hidden" name="device_model" value="{{ $request->device_model }}">
                        <input type="hidden" name="priority" value="{{ $request->priority }}">

                        <div class="space-y-1">
                            <label for="diagnosis" class="block text-xs font-semibold text-gray-300">التشخيص الفني الدقيق للفحص</label>
                            <textarea 
                                name="diagnosis" 
                                id="diagnosis" 
                                rows="3" 
                                placeholder="اكتب تقرير فحص الجهاز وأسباب العطل..."
                                class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                            >{{ old('diagnosis', $request->diagnosis) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="final_cost" class="block text-xs font-semibold text-gray-300">التكلفة النهائية للإصلاح</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="final_cost" 
                                    id="final_cost" 
                                    value="{{ old('final_cost', $request->final_cost ?? $request->estimated_cost) }}"
                                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="advance_payment" class="block text-xs font-semibold text-gray-300">العربون المدفوع مسبقاً</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="advance_payment" 
                                    id="advance_payment" 
                                    value="{{ old('advance_payment', $request->advance_payment) }}"
                                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]"
                                >
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white text-xs font-bold rounded-lg transition">
                                حفظ التقرير والتكلفة
                            </button>
                        </div>
                    </form>
                </div>
                @endcan

            </div>

            <!-- Right 1 Col: Status logger, assignment timeline -->
            <div class="space-y-6">
                
                <!-- Assigned Technician Card -->
                <div class="glass-panel p-4 space-y-4">
                    <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">فني الصيانة المسؤول</h3>
                    
                    @if($request->technician)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-300 border border-white/10">
                                <i class="fa-solid fa-user-gear"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white">{{ $request->technician->name }}</p>
                                <p class="text-[10px] text-gray-500">فني الفرع</p>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-rose-500/5 border border-rose-500/10 rounded-lg text-center text-rose-400 text-xs">
                            لم يتم تعيين فني صيانة للطلب بعد.
                        </div>
                    @endif

                    @can('manage-maintenance')
                    <form method="POST" action="{{ route('maintenance.assign', $request->id) }}" class="space-y-2 pt-2 border-t border-white/5">
                        @csrf
                        <select name="technician_id" required class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                            <option value="">إسناد لفني آخر...</option>
                            @foreach($technicians as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-[10px] font-bold rounded-lg transition">
                            تحديث المسؤول
                        </button>
                    </form>
                    @endcan
                </div>

                <!-- Update Status Form Card -->
                <div class="glass-panel p-4 space-y-4">
                    <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">تحديث حالة الإصلاح</h3>
                    
                    <form method="POST" action="{{ route('maintenance.status', $request->id) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-1">
                            <label for="status" class="block text-[10px] text-gray-400">حالة الإصلاح الجديدة</label>
                            <select 
                                name="status" 
                                id="status" 
                                required
                                class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                            >
                                <option value="received" {{ $request->status == 'received' ? 'selected' : '' }}>تم الاستلام (Received)</option>
                                <option value="diagnosed" {{ $request->status == 'diagnosed' ? 'selected' : '' }}>تم الفحص (Diagnosed)</option>
                                <option value="waiting_parts" {{ $request->status == 'waiting_parts' ? 'selected' : '' }}>بانتظار قطع الغيار</option>
                                <option value="in_progress" {{ $request->status == 'in_progress' ? 'selected' : '' }}>قيد الإصلاح (In Progress)</option>
                                <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>جاهز للتسليم (Completed)</option>
                                <option value="delivered" {{ $request->status == 'delivered' ? 'selected' : '' }}>تم التسليم للعميل (Delivered)</option>
                                <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>ملغي (Cancelled)</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label for="notes" class="block text-[10px] text-gray-400">ملاحظات التغيير</label>
                            <input 
                                type="text" 
                                name="notes" 
                                id="notes" 
                                placeholder="مثال: تم فحص وتحديد العطل الرئيسي بالشاشة"
                                class="block w-full px-3 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                            >
                        </div>

                        <button type="submit" class="w-full py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-lg text-xs transition glow-primary">
                            حفظ حالة التذكرة
                        </button>
                    </form>
                </div>

                <!-- Status Log Timeline -->
                <div class="glass-panel p-4 space-y-4">
                    <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">تاريخ حركات الجهاز (الجدول الزمني)</h3>
                    
                    <div class="space-y-3 relative border-r border-white/5 pr-4 mr-2">
                        @foreach($request->statusLogs as $log)
                            <div class="relative">
                                <!-- Dot indicator -->
                                <span class="absolute -right-[21px] top-1.5 w-2 h-2 rounded-full bg-[#D41414] border border-[#050505]"></span>
                                <div>
                                    <p class="text-[11px] font-bold text-white">{{ $log->new_status }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $log->notes }}</p>
                                    <span class="text-[9px] text-gray-500 block mt-1"><i class="fa-regular fa-clock"></i> {{ $log->created_at->diffForHumans() }} (بواسطة {{ $log->changedBy->name ?? 'غير معروف' }})</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>