<x-app-layout>
@section('page-title', 'Catat Transaksi')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('bendahara.transactions.index') }}" class="text-slate-500 hover:text-slate-800 text-xs sm:text-sm inline-flex items-center gap-1.5 font-medium transition-colors">
            <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Daftar Transaksi
        </a>
    </div>

    <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Catat Transaksi Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Tambahkan data pemasukan atau pengeluaran kas kelas.</p>
        </div>

        <form method="POST" action="{{ route('bendahara.transactions.store') }}" class="space-y-5" x-data="transactionForm()" x-init="init()">
            @csrf
            <div id="tour-bendahara-tx-type">
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

            <div id="tour-bendahara-tx-category">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kategori</label>
                <select name="category" id="categorySelect" x-model="category" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl ui selection dropdown">
                    <template x-for="opt in categoryOptions" :key="opt">
                        <option :value="opt" x-text="opt" :selected="category === opt"></option>
                    </template>
                </select>
                @error('category')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-4">
                <div id="tour-bendahara-form-student" x-show="type === 'income' && category === 'Uang Kas'" x-transition x-cloak class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Siswa Pembayar</label>
                        <select name="student_id" id="student_id" class="ui search selection dropdown w-full">
                            <option value="">-- Cari / Pilih Siswa --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->nama }} (NIS: {{ $student->nis ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                    <!-- Automatic Allocation Callout -->
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 flex items-start gap-2.5 leading-relaxed">
                        <span class="material-symbols-outlined text-slate-400 text-base flex-shrink-0 mt-0.5">info</span>
                        <div>
                            <span class="font-semibold text-slate-800">Catatan:</span> Nominal setoran kas ini akan dialokasikan secara otomatis (FIFO) ke periode kas yang belum lunas.
                        </div>
                    </div>
                </div>

                <div id="tour-bendahara-tx-amount">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jumlah Nominal (Rp)</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required min="0" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Contoh: 20000">
                    @error('amount')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div id="tour-bendahara-tx-description">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Deskripsi / Keterangan</label>
                <input type="text" name="description" x-model="description" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" :placeholder="type === 'income' ? 'Pembayaran Uang Kas' : 'Pembelian spidol dan penghapus papan'">
                @error('description')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            
            <div id="tour-bendahara-tx-date">
                <label for="transaction_date" class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Transaksi</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10">calendar_month</span>
                    <input 
                        type="text" 
                        name="transaction_date" 
                        id="transaction_date" 
                        value="{{ old('transaction_date', now()->format('Y-m-d')) }}" 
                        max="{{ date('Y-m-d') }}" 
                        required
                        class="datepicker-tx input-clean w-full pl-9 pr-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" 
                        placeholder="Pilih tanggal transaksi..."
                    >
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Tanggal maksimal hari ini (tidak dapat memilih tanggal yang belum lewat).</p>
                @error('transaction_date')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="pt-2">
                <button type="submit" class="w-full btn-navy py-3 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center justify-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-lg">save</span> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function transactionForm() {
    return {
        type: '{{ old('type', 'income') }}',
        category: '{{ old('category', 'Uang Kas') }}',
        description: '{{ old('description', 'Pembayaran Uang Kas') }}',
        incomeCategories: @json($incomeCategories),
        expenseCategories: @json($expenseCategories),
        get categoryOptions() {
            return this.type === 'income' ? this.incomeCategories : this.expenseCategories;
        },
        init() {
            this.$watch('type', (val) => {
                if (val === 'income') {
                    this.category = this.incomeCategories[0] || 'Uang Kas';
                    this.description = 'Pembayaran Uang Kas';
                } else {
                    this.category = this.expenseCategories[0] || 'Operasional Kelas';
                    this.description = '';
                }
                this.$nextTick(() => {
                    const $cat = $('#categorySelect');
                    if ($cat.length && typeof $cat.dropdown === 'function') {
                        $cat.dropdown('refresh');
                        $cat.dropdown('set selected', this.category);
                    }
                });
            });
        }
    }
}
</script>
</x-app-layout>
