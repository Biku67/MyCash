<section>
    <header class="flex items-start justify-between gap-4 pb-5 border-b border-slate-100">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-heading flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4F72] text-xl">lock_reset</span>
                <span>Perbarui Kata Sandi</span>
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Pastikan akun Anda menggunakan kata sandi yang aman dan tidak dibagikan ke orang lain.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div x-data="{ show: false }">
            <label for="update_password_current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Kata Sandi Saat Ini <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">key</span>
                <input id="update_password_current_password" name="current_password" :type="show ? 'text' : 'password'" type="password" autocomplete="current-password"
                    class="input-clean w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm font-medium text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-[#1B4F72] focus:ring-1 focus:ring-[#1B4F72]"
                    placeholder="••••••••">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 focus:outline-none flex items-center justify-center p-1 rounded-lg hover:bg-slate-100 transition-colors"
                    aria-label="Tampilkan atau sembunyikan kata sandi">
                    <span class="material-symbols-outlined text-lg" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                </button>
            </div>
            @if($errors->updatePassword->get('current_password'))
                <p class="mt-1 text-xs text-rose-500">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- New Password -->
            <div x-data="{ show: false }">
                <label for="update_password_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">lock</span>
                    <input id="update_password_password" name="password" :type="show ? 'text' : 'password'" type="password" autocomplete="new-password"
                        class="input-clean w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm font-medium text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-[#1B4F72] focus:ring-1 focus:ring-[#1B4F72]"
                        placeholder="Minimal 8 karakter">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 focus:outline-none flex items-center justify-center p-1 rounded-lg hover:bg-slate-100 transition-colors"
                        aria-label="Tampilkan atau sembunyikan kata sandi">
                        <span class="material-symbols-outlined text-lg" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
                @if($errors->updatePassword->get('password'))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div x-data="{ show: false }">
                <label for="update_password_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">verified_user</span>
                    <input id="update_password_password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" type="password" autocomplete="new-password"
                        class="input-clean w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm font-medium text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-[#1B4F72] focus:ring-1 focus:ring-[#1B4F72]"
                        placeholder="Ulangi kata sandi baru">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 focus:outline-none flex items-center justify-center p-1 rounded-lg hover:bg-slate-100 transition-colors"
                        aria-label="Tampilkan atau sembunyikan kata sandi">
                        <span class="material-symbols-outlined text-lg" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
                @if($errors->updatePassword->get('password_confirmation'))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <div>
                @if (session('status') === 'password-updated')
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                         class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Kata sandi berhasil diperbarui.</span>
                    </div>
                @endif
            </div>

            <button type="submit" class="btn-navy px-5 py-2.5 text-xs sm:text-sm font-bold rounded-xl shadow-sm inline-flex items-center gap-2 hover:opacity-95 transition-all">
                <span class="material-symbols-outlined text-base">shield</span>
                <span>Perbarui Kata Sandi</span>
            </button>
        </div>
    </form>
</section>
