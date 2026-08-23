<x-app-layout>
    <div class="glass-panel p-6 space-y-4">
        <div class="flex items-center space-x-3 space-x-reverse border-b border-white/5 pb-4">
            <div class="w-10 h-10 rounded-lg bg-[#D41414]/10 border border-[#D41414]/20 flex items-center justify-center text-[#D41414]">
                <i class="fa-solid fa-folder-open text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">تسجيل مصروف جديد</h2>
                <p class="text-xs text-gray-500 mt-1">هذه الصفحة قيد التطوير والتنفيذ البرمجي ضمن نظام 2M Mobile.</p>
            </div>
        </div>

        <div class="p-8 text-center bg-[#0a0a0a] rounded-xl border border-white/5 space-y-3">
            <i class="fa-solid fa-code text-4xl text-gray-700 animate-pulse"></i>
            <p class="text-sm text-gray-400">واجهة المستخدم البرمجية لـ "تسجيل مصروف جديد" ستكون متاحة هنا قريباً.</p>
            <div class="pt-2">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-xs font-bold rounded-lg transition inline-block">
                    <i class="fa-solid fa-arrow-right ml-1"></i> العودة للرئيسية
                </a>
            </div>
        </div>
    </div>
</x-app-layout>