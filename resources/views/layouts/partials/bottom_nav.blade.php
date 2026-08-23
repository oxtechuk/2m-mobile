<div class="fixed bottom-0 left-0 right-0 h-16 bg-[#121212]/95 backdrop-blur-md border-t border-white/5 flex items-center justify-around px-2 z-40 md:hidden shadow-2xl no-print">
    
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('dashboard') ? 'text-[#D41414]' : 'text-gray-400 hover:text-white' }}">
        <i class="fa-solid fa-chart-line text-lg"></i>
        <span class="text-[9px] font-semibold mt-1">الرئيسية</span>
    </a>

    <!-- POS -->
    @if(auth()->user()->can('create-sale') || auth()->user()->can('manage-sales') || auth()->user()->hasRole('admin') || auth()->user()->role === 'admin')
    <a href="{{ route('pos.index') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('pos.*') ? 'text-[#D41414]' : 'text-gray-400 hover:text-white' }}">
        <i class="fa-solid fa-cash-register text-lg"></i>
        <span class="text-[9px] font-semibold mt-1">شاشة البيع</span>
    </a>
    @endif

    <!-- Maintenance -->
    @if(auth()->user()->can('manage-maintenance') || auth()->user()->can('manage-maintenance-own'))
    <a href="{{ route('maintenance.index') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('maintenance.*') ? 'text-[#D41414]' : 'text-gray-400 hover:text-white' }}">
        <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
        <span class="text-[9px] font-semibold mt-1">الصيانة</span>
    </a>
    @endif

    <!-- More Menu Button -->
    <button 
        @click="moreMenuOpen = true" 
        class="flex flex-col items-center justify-center w-16 h-full text-gray-400 hover:text-white transition"
    >
        <i class="fa-solid fa-ellipsis text-lg"></i>
        <span class="text-[9px] font-semibold mt-1">المزيد</span>
    </button>

</div>
