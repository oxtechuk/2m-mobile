<header class="h-16 bg-[#121212] border-b border-white/5 flex items-center justify-between px-6 z-20 shrink-0">
    <div class="flex items-center space-x-4 space-x-reverse">
        <!-- Toggle Sidebar (on main navbar area) -->
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white p-1 rounded hover:bg-white/5 md:hidden">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>

        <!-- Current Branch & Role Info -->
        <div class="flex items-center space-x-2 space-x-reverse text-sm">
            @if(auth()->user()->role === 'admin')
                @php
                    $allBranches = \App\Models\Branch::all();
                    $activeBranchId = selected_branch_id();
                    $activeBranchName = 'كل الفروع';
                    if ($activeBranchId !== 'all') {
                        $activeBranch = $allBranches->firstWhere('id', $activeBranchId);
                        $activeBranchName = $activeBranch ? $activeBranch->name : 'الفرع الرئيسي';
                    }
                @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-2.5 py-1 rounded bg-white/5 border border-white/10 hover:bg-white/10 text-white text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-store text-xs text-[#D41414]"></i>
                        <span>{{ $activeBranchName }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
                    </button>
                    <!-- Dropdown -->
                    <div 
                        x-show="open" 
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-1.5 w-48 bg-[#1e1e1e] border border-white/10 rounded-lg shadow-xl py-1 z-50"
                        style="display: none;"
                    >
                        <form method="POST" action="{{ route('switch.branch') }}">
                            @csrf
                            <input type="hidden" name="branch_id" value="all">
                            <button type="submit" class="w-full text-right px-4 py-2 text-xs text-gray-300 hover:bg-white/5 hover:text-white transition flex items-center justify-between">
                                <span>كل الفروع</span>
                                @if($activeBranchId === 'all')
                                    <i class="fa-solid fa-check text-emerald-400"></i>
                                @endif
                            </button>
                        </form>
                        
                        @foreach($allBranches as $b)
                            <form method="POST" action="{{ route('switch.branch') }}">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                <button type="submit" class="w-full text-right px-4 py-2 text-xs text-gray-300 hover:bg-white/5 hover:text-white transition flex items-center justify-between">
                                    <span>{{ $b->name }}</span>
                                    @if($activeBranchId !== 'all' && $activeBranchId == $b->id)
                                        <i class="fa-solid fa-check text-emerald-400"></i>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @else
                <span class="px-2.5 py-1 rounded bg-white/5 border border-white/10 text-gray-300">
                    <i class="fa-solid fa-store text-xs ml-1 text-[#D41414]"></i>
                    {{ auth()->user()->branch->name ?? 'بدون فرع' }}
                </span>
            @endif
            
            <!-- Cashier Shift Status Indicator -->
            @php
                $openShift = \App\Models\CashShift::where('user_id', auth()->id())->where('status', 'open')->first();
            @endphp
            @if($openShift)
                <a href="{{ route('pos.index') }}" title="إدارة وقفل الوردية" class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/20 transition flex items-center text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 ml-1.5 animate-pulse"></span>
                    <span>الوردية مفتوحة</span>
                </a>
            @else
                <a href="{{ route('pos.index') }}" title="فتح وردية جديدة للبيع" class="px-2.5 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition flex items-center text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 ml-1.5"></span>
                    <span>الوردية مغلقة (فتح)</span>
                </a>
            @endif
            
            @can('create-sale')
            <a href="{{ route('pos.index') }}" class="px-2.5 py-1 rounded bg-[#D41414]/10 border border-[#D41414]/20 hover:bg-[#D41414] hover:text-white text-[#D41414] transition text-xs flex items-center font-bold">
                <i class="fa-solid fa-cash-register ml-1"></i>
                <span class="hidden sm:inline">شاشة البيع POS</span>
            </a>
            @endcan
        </div>
    </div>

    <!-- Right Side Actions (Notifications, Search, Dropdowns) -->
    <div class="flex items-center space-x-4 space-x-reverse">
        
        <!-- Notifications Dropdown -->
        @php
            $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->get();
        @endphp
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative text-gray-400 hover:text-white p-2 rounded-full hover:bg-white/5 transition">
                <i class="fa-solid fa-bell text-lg"></i>
                @if($unreadNotifications->count() > 0)
                    <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-[#D41414] text-white text-[9px] font-bold rounded-full flex items-center justify-center glow-primary">
                        {{ $unreadNotifications->count() }}
                    </span>
                @endif
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="open" 
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute left-0 mt-2 w-80 bg-[#1e1e1e] border border-white/10 rounded-lg shadow-xl py-2 z-50 overflow-hidden"
                style="display: none;"
            >
                <div class="px-4 py-2 border-b border-white/5 flex justify-between items-center bg-[#151515]">
                    <span class="text-xs font-semibold text-white">الإشعارات الجديدة</span>
                    @if($unreadNotifications->count() > 0)
                        <button class="text-[10px] text-[#D41414] hover:underline" onclick="markAllNotificationsRead()">تحديد الكل كمقروء</button>
                    @endif
                </div>

                <div class="max-h-60 overflow-y-auto divide-y divide-white/5">
                    @forelse($unreadNotifications as $notif)
                        <div class="p-3 hover:bg-white/5 transition flex items-start space-x-2.5 space-x-reverse">
                            <span class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $notif->type === 'error' ? 'bg-rose-500' : ($notif->type === 'warning' ? 'bg-amber-500' : 'bg-blue-500') }}"></span>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-white">{{ $notif->title }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $notif->message }}</p>
                                <span class="text-[9px] text-gray-500 block mt-1">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-gray-500">
                            لا توجد إشعارات غير مقروءة.
                        </div>
                    @endforelse
                </div>
                
                <div class="border-t border-white/5 p-2 bg-[#151515] text-center">
                    <a href="{{ route('notifications.index') }}" class="text-xs text-gray-400 hover:text-white hover:underline">عرض كل الإشعارات</a>
                </div>
            </div>
        </div>

        <!-- User Settings Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-2 space-x-reverse hover:opacity-80 transition py-1.5 px-2.5 rounded-lg hover:bg-white/5">
                <span class="text-xs font-medium text-gray-300 hidden sm:block">{{ auth()->user()->name }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-500"></i>
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="open" 
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute left-0 mt-2 w-48 bg-[#1e1e1e] border border-white/10 rounded-lg shadow-xl py-1 z-50 overflow-hidden"
                style="display: none;"
            >
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-gray-300 hover:bg-white/5 hover:text-white transition">
                    <i class="fa-solid fa-user-gear ml-2 text-gray-500"></i>الملف الشخصي
                </a>
                
                <div class="h-px bg-white/5 my-1"></div>

                <!-- Log Out -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-right block px-4 py-2 text-xs text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition">
                        <i class="fa-solid fa-right-from-bracket ml-2 text-rose-500"></i>تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>

<script>
function markAllNotificationsRead() {
    fetch('{{ route("notifications.readAll") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}
</script>
