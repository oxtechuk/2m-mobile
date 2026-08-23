@php
    $activeGroup = null;
    if (request()->routeIs('pos.*') || request()->routeIs('sales.*') || request()->routeIs('returns.*') || request()->routeIs('customers.*')) {
        $activeGroup = 'sales';
    } elseif (request()->routeIs('products.*') || request()->routeIs('inventory.*') || request()->routeIs('categories.*')) {
        $activeGroup = 'inventory';
    } elseif (request()->routeIs('maintenance.*')) {
        $activeGroup = 'maintenance';
    } elseif (request()->routeIs('wallets.*') || request()->routeIs('expenses.*') || request()->routeIs('transfers.*') || request()->routeIs('transactions.*')) {
        $activeGroup = 'finance';
    } elseif (request()->routeIs('users.*') || request()->routeIs('payroll.*')) {
        $activeGroup = 'hr';
    } elseif (request()->routeIs('reports.*') || request()->routeIs('settings.*')) {
        $activeGroup = 'settings';
    }
@endphp

<aside 
    class="hidden md:flex bg-[#121212] border-l border-white/5 flex-col transition-all duration-300 z-30 shrink-0 select-none"
    :class="sidebarOpen ? 'w-64' : 'w-16'"
    x-data="{ 
        openGroup: '{{ $activeGroup }}',
        toggleGroup(grp) {
            if (!this.sidebarOpen) {
                this.sidebarOpen = true;
                this.openGroup = grp;
            } else {
                this.openGroup = (this.openGroup === grp) ? null : grp;
            }
        }
    }"
>
    <!-- Logo & Brand Header -->
    <div class="h-16 flex items-center justify-center px-3 border-b border-white/5 bg-[#0a0a0a]">
        <!-- Full Brand (when expanded) -->
        <div class="flex items-center space-x-2 space-x-reverse w-full" x-show="sidebarOpen" x-transition:opacity>
            @if(setting('store_logo'))
                <img src="{{ asset('storage/' . setting('store_logo')) }}" class="w-8 h-8 rounded-lg object-cover shrink-0">
            @else
                <div class="w-8 h-8 rounded-lg bg-[#D41414] flex items-center justify-center glow-primary shrink-0">
                    <span class="text-white font-extrabold text-lg">2M</span>
                </div>
            @endif
            <span class="text-white font-bold text-lg text-glow-primary truncate">{{ setting('store_name', '2M Mobile') }}</span>
        </div>

        <!-- Mini Icon Logo (when collapsed) -->
        <div x-show="!sidebarOpen" class="flex items-center justify-center" x-transition:opacity>
            @if(setting('store_logo'))
                <img src="{{ asset('storage/' . setting('store_logo')) }}" class="w-8 h-8 rounded-lg object-cover" title="{{ setting('store_name', '2M Mobile') }}">
            @else
                <div class="w-8 h-8 rounded-lg bg-[#D41414] flex items-center justify-center glow-primary" title="{{ setting('store_name', '2M Mobile') }}">
                    <span class="text-white font-extrabold text-sm">2M</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation Menu Links (Grouped & Collapsible) -->
    <div class="flex-1 overflow-y-auto py-3 px-2 space-y-1.5 scrollbar-none text-xs">
        
        <!-- 🚀 1. Prominent POS Action Button -->
        @if(auth()->user()->can('create-sale') || auth()->user()->can('manage-sales') || auth()->user()->hasRole('admin') || auth()->user()->role === 'admin')
        <a 
            href="{{ route('pos.index') }}" 
            class="flex items-center justify-center space-x-2 space-x-reverse bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-xl transition-all glow-primary mb-2 shadow-md" 
            :class="sidebarOpen ? 'w-full px-3 py-2.5' : 'w-10 h-10 p-0 mx-auto'"
            :title="!sidebarOpen ? 'فاتورة بيع سريعة (POS)' : ''"
        >
            <i class="fa-solid fa-cash-register text-white text-sm"></i>
            <span x-show="sidebarOpen" class="text-xs text-white font-black">فاتورة بيع جديدة (POS)</span>
        </a>
        @endif

        <!-- 📊 2. Dashboard Link -->
        <a 
            href="{{ route('dashboard') }}" 
            class="flex items-center space-x-3 space-x-reverse px-3 py-2 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center px-0'"
            :title="!sidebarOpen ? 'الرئيسية ولوحة التحكم' : ''"
        >
            <i class="fa-solid fa-chart-pie w-5 text-center text-sm {{ request()->routeIs('dashboard') ? 'text-[#D41414]' : 'text-gray-400' }}"></i>
            <span x-show="sidebarOpen" class="font-bold">لوحة التحكم</span>
        </a>

        <div class="h-px bg-white/5 my-1" x-show="sidebarOpen"></div>

        <!-- 🛒 GROUP 1: المبيعات والعملاء (Sales & Invoices) -->
        @if(auth()->user()->can('create-sale') || auth()->user()->can('manage-sales') || auth()->user()->can('manage-customers'))
        <div class="space-y-1">
            <button 
                type="button"
                @click="toggleGroup('sales')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition cursor-pointer"
                :class="openGroup === 'sales' ? 'bg-white/5 text-white' : ''"
                :title="!sidebarOpen ? 'المبيعات والعملاء' : ''"
            >
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fa-solid fa-bag-shopping w-5 text-center text-sm" :class="openGroup === 'sales' ? 'text-[#D41414]' : 'text-gray-400'"></i>
                    <span x-show="sidebarOpen" class="font-bold">المبيعات والعملاء</span>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-200" :class="openGroup === 'sales' ? 'rotate-180 text-white' : ''"></i>
            </button>

            <!-- Sublinks -->
            <div x-show="sidebarOpen && openGroup === 'sales'" x-collapse class="pr-6 pl-2 space-y-1 pt-0.5">
                <a href="{{ route('pos.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('pos.*') ? 'text-[#D41414] font-bold bg-[#D41414]/10' : '' }}">
                    <i class="fa-solid fa-cash-register text-xs w-4 text-center"></i>
                    <span>شاشة البيع (POS)</span>
                </a>
                @can('manage-sales')
                <a href="{{ route('sales.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('sales.*') ? 'text-[#D41414] font-bold bg-[#D41414]/10' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar text-xs w-4 text-center"></i>
                    <span>سجل الفواتير</span>
                </a>
                @endcan
                @can('process-return')
                <a href="{{ route('returns.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('returns.*') ? 'text-[#D41414] font-bold bg-[#D41414]/10' : '' }}">
                    <i class="fa-solid fa-rotate-left text-xs w-4 text-center"></i>
                    <span>مرتجعات المبيعات</span>
                </a>
                @endcan
                <a href="{{ route('customers.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('customers.*') ? 'text-[#D41414] font-bold bg-[#D41414]/10' : '' }}">
                    <i class="fa-solid fa-users text-xs w-4 text-center"></i>
                    <span>سجل العملاء</span>
                </a>
            </div>
        </div>
        @endif

        <!-- 📦 GROUP 2: المخزون والمنتجات (Products & Stock) -->
        @if(auth()->user()->can('manage-products') || auth()->user()->can('manage-inventory'))
        <div class="space-y-1">
            <button 
                type="button"
                @click="toggleGroup('inventory')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition cursor-pointer"
                :class="openGroup === 'inventory' ? 'bg-white/5 text-white' : ''"
                :title="!sidebarOpen ? 'المخزون والمنتجات' : ''"
            >
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fa-solid fa-boxes-stacked w-5 text-center text-sm" :class="openGroup === 'inventory' ? 'text-amber-500' : 'text-gray-400'"></i>
                    <span x-show="sidebarOpen" class="font-bold">المخزون والمنتجات</span>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-200" :class="openGroup === 'inventory' ? 'rotate-180 text-white' : ''"></i>
            </button>

            <!-- Sublinks -->
            <div x-show="sidebarOpen && openGroup === 'inventory'" x-collapse class="pr-6 pl-2 space-y-1 pt-0.5">
                @can('manage-products')
                <a href="{{ route('products.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('products.index') || request()->routeIs('products.create') || request()->routeIs('products.edit') ? 'text-amber-400 font-bold bg-amber-500/10' : '' }}">
                    <i class="fa-solid fa-mobile-screen text-xs w-4 text-center"></i>
                    <span>المنتجات والأجهزة</span>
                </a>
                <a href="{{ route('products.barcode-studio') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('products.barcode*') ? 'text-amber-400 font-bold bg-amber-500/10' : '' }}">
                    <i class="fa-solid fa-barcode text-xs w-4 text-center"></i>
                    <span>استوديو الباركود</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('categories.*') ? 'text-amber-400 font-bold bg-amber-500/10' : '' }}">
                    <i class="fa-solid fa-tags text-xs w-4 text-center"></i>
                    <span>تصنيفات الأصناف</span>
                </a>
                @endcan
                @can('manage-inventory')
                <a href="{{ route('inventory.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('inventory.*') ? 'text-amber-400 font-bold bg-amber-500/10' : '' }}">
                    <i class="fa-solid fa-warehouse text-xs w-4 text-center"></i>
                    <span>جرد ومخزون الفروع</span>
                </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- 🔧 3. Maintenance Module (Direct Link) -->
        @if(auth()->user()->can('manage-maintenance') || auth()->user()->can('manage-maintenance-own'))
        <a 
            href="{{ route('maintenance.index') }}" 
            class="flex items-center space-x-3 space-x-reverse px-3 py-2 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition {{ request()->routeIs('maintenance.*') ? 'sidebar-link-active' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center px-0'"
            :title="!sidebarOpen ? 'قسم الصيانة' : ''"
        >
            <i class="fa-solid fa-screwdriver-wrench w-5 text-center text-sm {{ request()->routeIs('maintenance.*') ? 'text-teal-400' : 'text-gray-400' }}"></i>
            <span x-show="sidebarOpen" class="font-bold">قسم الصيانة والأجهزة</span>
        </a>
        @endif

        <!-- 💼 GROUP 4: الخزائن والماليات (Finance & Wallets) -->
        @if(auth()->user()->can('manage-wallets') || auth()->user()->role === 'branch_manager' || auth()->user()->hasRole('admin'))
        <div class="space-y-1">
            <button 
                type="button"
                @click="toggleGroup('finance')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition cursor-pointer"
                :class="openGroup === 'finance' ? 'bg-white/5 text-white' : ''"
                :title="!sidebarOpen ? 'الخزائن والماليات' : ''"
            >
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fa-solid fa-wallet w-5 text-center text-sm" :class="openGroup === 'finance' ? 'text-emerald-400' : 'text-gray-400'"></i>
                    <span x-show="sidebarOpen" class="font-bold">الخزائن والماليات</span>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-200" :class="openGroup === 'finance' ? 'rotate-180 text-white' : ''"></i>
            </button>

            <!-- Sublinks -->
            <div x-show="sidebarOpen && openGroup === 'finance'" x-collapse class="pr-6 pl-2 space-y-1 pt-0.5">
                <a href="{{ route('wallets.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('wallets.index') ? 'text-emerald-400 font-bold bg-emerald-500/10' : '' }}">
                    <i class="fa-solid fa-vault text-xs w-4 text-center"></i>
                    <span>الخزائن والمحافظ</span>
                </a>
                @can('manage-expenses')
                <a href="{{ route('expenses.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('expenses.*') ? 'text-emerald-400 font-bold bg-emerald-500/10' : '' }}">
                    <i class="fa-solid fa-circle-minus text-xs w-4 text-center"></i>
                    <span>سندات المصروفات</span>
                </a>
                @endcan
                @can('manage-transfers')
                <a href="{{ route('transfers.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('transfers.*') ? 'text-emerald-400 font-bold bg-emerald-500/10' : '' }}">
                    <i class="fa-solid fa-money-bill-transfer text-xs w-4 text-center"></i>
                    <span>تحويلات الأموال</span>
                </a>
                @endcan
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('transactions.*') ? 'text-emerald-400 font-bold bg-emerald-500/10' : '' }}">
                    <i class="fa-solid fa-list-check text-xs w-4 text-center"></i>
                    <span>سجل المعاملات</span>
                </a>
            </div>
        </div>
        @endif

        <!-- 👥 GROUP 5: شؤون الموظفين والرواتب (HR & Payroll) -->
        @if(auth()->user()->hasRole('admin') || auth()->user()->role === 'admin' || auth()->user()->role === 'branch_manager')
        <div class="space-y-1">
            <button 
                type="button"
                @click="toggleGroup('hr')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition cursor-pointer"
                :class="openGroup === 'hr' ? 'bg-white/5 text-white' : ''"
                :title="!sidebarOpen ? 'الموظفين والرواتب' : ''"
            >
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fa-solid fa-users-gear w-5 text-center text-sm" :class="openGroup === 'hr' ? 'text-purple-400' : 'text-gray-400'"></i>
                    <span x-show="sidebarOpen" class="font-bold">الموظفين والرواتب</span>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-200" :class="openGroup === 'hr' ? 'rotate-180 text-white' : ''"></i>
            </button>

            <!-- Sublinks -->
            <div x-show="sidebarOpen && openGroup === 'hr'" x-collapse class="pr-6 pl-2 space-y-1 pt-0.5">
                <a href="{{ route('users.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('users.*') ? 'text-purple-400 font-bold bg-purple-500/10' : '' }}">
                    <i class="fa-solid fa-user-tie text-xs w-4 text-center"></i>
                    <span>شؤون الموظفين</span>
                </a>
                <a href="{{ route('payroll.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('payroll.index') || request()->routeIs('payroll.payslip') ? 'text-purple-400 font-bold bg-purple-500/10' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar text-xs w-4 text-center"></i>
                    <span>مسير الرواتب</span>
                </a>
                <a href="{{ route('payroll.adjustments') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('payroll.adjustments*') ? 'text-purple-400 font-bold bg-purple-500/10' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar text-xs w-4 text-center"></i>
                    <span>السلف والخصومات</span>
                </a>
            </div>
        </div>
        @endif

        <!-- ⚙️ GROUP 6: التقارير والإعدادات (Reports & Settings) -->
        @if(auth()->user()->can('view-reports') || auth()->user()->can('manage-settings') || auth()->user()->hasRole('admin'))
        <div class="space-y-1">
            <button 
                type="button"
                @click="toggleGroup('settings')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition cursor-pointer"
                :class="openGroup === 'settings' ? 'bg-white/5 text-white' : ''"
                :title="!sidebarOpen ? 'التقارير والإعدادات' : ''"
            >
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fa-solid fa-sliders w-5 text-center text-sm" :class="openGroup === 'settings' ? 'text-blue-400' : 'text-gray-400'"></i>
                    <span x-show="sidebarOpen" class="font-bold">التقارير والإعدادات</span>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-200" :class="openGroup === 'settings' ? 'rotate-180 text-white' : ''"></i>
            </button>

            <!-- Sublinks -->
            <div x-show="sidebarOpen && openGroup === 'settings'" x-collapse class="pr-6 pl-2 space-y-1 pt-0.5">
                @if(auth()->user()->can('view-reports') || auth()->user()->can('view-branch-reports'))
                <a href="{{ route('reports.sales') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('reports.*') ? 'text-blue-400 font-bold bg-blue-500/10' : '' }}">
                    <i class="fa-solid fa-chart-line text-xs w-4 text-center"></i>
                    <span>التقارير التحليلية</span>
                </a>
                @endif
                @can('manage-settings')
                <a href="{{ route('branches.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('branches.*') ? 'text-blue-400 font-bold bg-blue-500/10' : '' }}">
                    <i class="fa-solid fa-building text-xs w-4 text-center"></i>
                    <span>إدارة الفروع</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 py-1.5 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition {{ request()->routeIs('settings.*') ? 'text-blue-400 font-bold bg-blue-500/10' : '' }}">
                    <i class="fa-solid fa-gear text-xs w-4 text-center"></i>
                    <span>إعدادات النظام</span>
                </a>
                @endcan
            </div>
        </div>
        @endif

    </div>

    <!-- User Profile & Bottom Toggle Area -->
    <div class="border-t border-white/5 bg-[#0a0a0a] flex flex-col shrink-0">
        
        <!-- User Profile Area -->
        <div class="p-2.5 flex items-center transition" :class="sidebarOpen ? 'space-x-3 space-x-reverse justify-start' : 'justify-center'">
            <div class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center text-white border border-white/10 overflow-hidden shrink-0" :title="!sidebarOpen ? '{{ auth()->user()->name }}' : ''">
                @if(auth()->user()->avatar)
                    <img src="{{ asset(auth()->user()->avatar) }}" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-user-tie text-sm text-gray-400"></i>
                @endif
            </div>
            <div class="min-w-0 flex-1" x-show="sidebarOpen" x-transition:opacity>
                <p class="text-xs font-bold text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ auth()->user()->role === 'admin' ? 'المدير العام' : (auth()->user()->branch->name ?? 'غير مرتبط بفرع') }}</p>
            </div>
        </div>

        <!-- Toggle Button Placed at the Bottom (Always Visible to Open/Close Sidebar) -->
        <div class="p-1.5 border-t border-white/5 bg-[#050505]/60 flex items-center justify-center">
            <button 
                type="button"
                @click="sidebarOpen = !sidebarOpen" 
                class="w-full py-2 px-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition flex items-center justify-center gap-2 group cursor-pointer"
                :title="sidebarOpen ? 'تصغير القائمة الجانبية' : 'توسيع القائمة الجانبية'"
            >
                <i 
                    class="fa-solid text-sm transition-transform duration-300 text-gray-400 group-hover:text-white" 
                    :class="sidebarOpen ? 'fa-angles-right group-hover:-translate-x-0.5' : 'fa-angles-left group-hover:translate-x-0.5'"
                ></i>
                <span x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 group-hover:text-white" x-transition:opacity>تصغير القائمة</span>
            </button>
        </div>

    </div>
</aside>
