<x-app-layout>
@section('page-title', 'Edit Transaksi')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('bendahara.transactions.index') }}" class="text-slate-500 hover:text-slate-800 text-xs sm:text-sm inline-flex items-center gap-1.5 font-medium transition-colors">
            <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Daftar Transaksi
        </a>
        <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg border border-slate-200">ID: #{{ $transaction->id }}</span>
    </div>

    <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Edit Transaksi Kas</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Perubahan nominal atau data akan tercatat pada log audit.</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl">edit_note</span>
            </div>
        </div>

        <form method="POST" action="{{ route('bendahara.transactions.update', $transaction->id) }}" class="space-y-5" x-data="editTransactionForm()" x-init="init()">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Tipe Transaksi</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" x-model="type" class="peer hidden">
                        <div class="peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 peer-checked:text-emerald-700 border border-slate-200 rounded-xl p-3.5 text-center text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">arrow_downward</span>Pemasukan
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" x-model="type" class="peer hidden">
                        <div class="peer-checked:border-rose-500 peer-checked:bg-rose-50/60 peer-checked:text-rose-700 border border-slate-200 rounded-xl p-3.5 text-center text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">arrow_upward</span>Pengeluaran
                        </div>
                    </label>
                </div>
                @error('type')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kategori</label>
                <select name="category" x-model="category" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl">
                    <template x-for="opt in categoryOptions" :key="opt">
                        <option :value="opt" x-text="opt" :selected="category === opt"></option>
                    </template>
                </select>
                @error('category')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div x-show="type === 'income' && category === 'Uang Kas'" x-transition x-cloak>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Siswa Pembayar</label>
                <select name="student_id" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id', $currentStudentId) == $student->id ? 'selected' : '' }}>
                            {{ $student->nama }} (NIS: {{ $student->nis ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('student_id')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jumlah Nominal (Rp)</label>
                <input type="number" name="amount" value="{{ old('amount', (int)$transaction->total_nominal) }}" required min="0" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="50000">
                @error('amount')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Deskripsi / Keterangan</label>
                <input type="text" name="description" value="{{ old('description', $transaction->keterangan) }}" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Iuran bulanan / Pembelian ATK">
                @error('description')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Transaksi</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10">calendar_month</span>
                    <input type="text" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $transaction->tanggal_transaksi->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required class="datepicker-tx input-clean w-full pl-9 pr-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Pilih tanggal transaksi...">
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Tanggal transaksi maksimal hari ini (tidak dapat memilih tanggal mendatang).</p>
                @error('transaction_date')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div class="p-4 bg-amber-50/50 rounded-xl border border-amber-200/80">
                <label class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">history_edu</span> Alasan Perubahan Data <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="reason" value="{{ old('reason') }}" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-amber-200 rounded-xl focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="Contoh: Koreksi nominal atau perbaikan tanggal transaksi...">
                @error('reason')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                <p class="text-[11px] text-amber-700/80 mt-1">Alasan perubahan wajib diisi untuk menjaga transparansi pembukuan kas.</p>
            </div>
            
            <div class="pt-2 flex items-center gap-3">
                <a href="{{ route('bendahara.transactions.index') }}" class="w-1/3 py-2.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-800 text-center rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-2/3 btn-navy py-2.5 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center justify-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-lg">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editTransactionForm() {
    return {
        type: '{{ old('type', $transaction->jenis_transaksi === 'pemasukan' ? 'income' : 'expense') }}',
        category: '{{ old('category', $transaction->kategori->nama_kategori ?? 'Uang Kas') }}',
        incomeCategories: @json($incomeCategories),
        expenseCategories: @json($expenseCategories),
        get categoryOptions() {
            return this.type === 'income' ? this.incomeCategories : this.expenseCategories;
        },
        init() {
            this.$watch('type', (val) => {
                if (val === 'income') {
                    this.category = this.incomeCategories[0] || 'Uang Kas';
                } else {
                    this.category = this.expenseCategories[0] || 'Operasional Kelas';
                }
            });
        }
    }
}
</script>
</x-app-layout>
