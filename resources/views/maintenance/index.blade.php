<x-app-layout>
    <div class="glass-panel p-4 md:p-6 space-y-4">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-white/5 pb-4">
            <div>
                <h2 class="text-base md:text-lg font-bold text-white"><i class="fa-solid fa-screwdriver-wrench ml-2 text-[#D41414]"></i>طلبات الصيانة</h2>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">إدارة أجهزة العملاء المستلمة وتوزيع المهام على الفنيين ومتابعة حالة الإصلاح.</p>
            </div>
            <a href="{{ route('maintenance.create') }}" class="px-3.5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white text-xs font-bold rounded-lg transition">
                <i class="fa-solid fa-plus ml-1"></i> تسجيل طلب جديد
            </a>
        </div>

        @php
            $requests = \App\Models\MaintenanceRequest::with(['customer', 'technician'])->get();
        @endphp

        <!-- 1. Desktop Table View (Visible only on md screens and larger) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="text-gray-500 border-b border-white/5 pb-2">
                        <th class="pb-2">رقم التذكرة</th>
                        <th class="pb-2">العميل</th>
                        <th class="pb-2">الجهاز</th>
                        <th class="pb-2">العطل</th>
                        <th class="pb-2">الفني</th>
                        <th class="pb-2">حالة الإصلاح</th>
                        <th class="pb-2">الخيارات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($requests as $req)
                        <tr class="hover:bg-white/5">
                            <td class="py-3 font-mono text-white">{{ $req->ticket_number }}</td>
                            <td class="py-3">
                                <span class="font-semibold text-white block">{{ $req->customer->name }}</span>
                                <span class="text-[10px] text-gray-500">{{ $req->customer->phone }}</span>
                            </td>
                            <td class="py-3">{{ $req->device_type }} - {{ $req->device_model }}</td>
                            <td class="py-3 text-gray-400 truncate max-w-xs">{{ $req->problem_description }}</td>
                            <td class="py-3 text-gray-300">{{ $req->technician->name ?? 'غير معين' }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 text-[9px] rounded bg-amber-500/10 border border-amber-500/20 text-amber-400">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td class="py-3">
                                <a href="{{ route('maintenance.show', $req->id) }}" class="p-1.5 bg-white/5 hover:bg-white/10 rounded text-gray-300">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">لا توجد طلبات صيانة مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. Mobile Cards List View (Visible only on small screens) -->
        <div class="block md:hidden space-y-3">
            @forelse($requests as $req)
                <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-3">
                    
                    <!-- Top section: Ticket and Status -->
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-mono font-bold text-white">{{ $req->ticket_number }}</span>
                        
                        @php
                            $statusColors = [
                                'received' => 'bg-blue-500/10 border-blue-500/20 text-blue-400',
                                'diagnosed' => 'bg-purple-500/10 border-purple-500/20 text-purple-400',
                                'in_progress' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                                'completed' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                                'delivered' => 'bg-gray-500/10 border-gray-500/20 text-gray-400',
                                'cancelled' => 'bg-rose-500/10 border-rose-500/20 text-rose-400',
                            ];
                            $colorClass = $statusColors[$req->status] ?? 'bg-white/5 text-gray-400';
                        @endphp
                        
                        <span class="px-2 py-0.5 text-[9px] rounded-full border {{ $colorClass }}">
                            {{ $req->status }}
                        </span>
                    </div>

                    <!-- Middle: Device specs and Customer info -->
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-white">{{ $req->device_type }} - {{ $req->device_model }}</h4>
                        <p class="text-[10px] text-gray-400">
                            <i class="fa-solid fa-user ml-1 text-[9px]"></i> {{ $req->customer->name }} ({{ $req->customer->phone }})
                        </p>
                        <p class="text-[10px] text-gray-500 line-clamp-2 mt-1">
                            <i class="fa-solid fa-triangle-exclamation ml-1 text-[9px]"></i> العطل: {{ $req->problem_description }}
                        </p>
                    </div>

                    <!-- Bottom: Technician assigned and details button -->
                    <div class="flex justify-between items-center pt-2.5 border-t border-white/5">
                        <span class="text-[10px] text-gray-400">
                            <i class="fa-solid fa-user-gear ml-1"></i> {{ $req->technician->name ?? 'غير معين' }}
                        </span>
                        
                        <a href="{{ route('maintenance.show', $req->id) }}" class="px-3 py-1 bg-white/5 border border-white/10 text-white rounded-lg text-[10px] font-bold hover:bg-white/10 transition flex items-center gap-1">
                            عرض التفاصيل <i class="fa-solid fa-chevron-left text-[8px]"></i>
                        </a>
                    </div>

                </div>
            @empty
                <div class="py-12 text-center text-xs text-gray-500">لا توجد طلبات صيانة مسجلة حالياً.</div>
            @endforelse
        </div>

    </div>
</x-app-layout>
