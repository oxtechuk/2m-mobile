<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1">
            <label for="email" class="block text-xs font-semibold text-gray-300">البريد الإلكتروني / اسم المستخدم</label>
            <div class="relative">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    placeholder="example@2m.com"
                    class="block w-full pr-10 pl-3 py-2.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-sm"
                >
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <div class="flex justify-between items-center">
                <label for="password" class="block text-xs font-semibold text-gray-300">كلمة المرور</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] text-gray-500 hover:text-white transition" href="{{ route('password.request') }}">
                        نسيت كلمة المرور؟
                    </a>
                @endif
            </div>
            <div class="relative" x-data="{ show: false }">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input 
                    id="password" 
                    :type="show ? 'text' : 'password'" 
                    name="password" 
                    required 
                    placeholder="••••••••"
                    class="block w-full pr-10 pl-10 py-2.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-sm"
                >
                <button 
                    type="button" 
                    @click="show = !show"
                    class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 hover:text-white"
                >
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input 
                id="remember_me" 
                type="checkbox" 
                name="remember" 
                class="rounded bg-[#0a0a0a] border-white/10 text-[#D41414] focus:ring-[#D41414]"
            >
            <label for="remember_me" class="mr-2 text-xs text-gray-400 select-none">تذكرني في هذا المتصفح</label>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-lg transition-all glow-primary hover:scale-[1.01] text-sm"
        >
            تسجيل الدخول <i class="fa-solid fa-right-to-bracket mr-1"></i>
        </button>
    </form>
</x-guest-layout>
