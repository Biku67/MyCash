<x-app-layout>
@section('page-title', 'Transaksi Kas')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading">Transaksi Kas</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Catat dan pantau mutasi pemasukan serta pengeluaran kas kelas.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
        <a href="{{ route('bendahara.transactions.logs') }}" class="py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 rounded-xl font-semibold transition-all border border-slate-200">
            <span class="material-symbols-outlined text-base text-slate-500">history</span>
            <span>Riwayat Log</span>
            @if(isset($logsCount) && $logsCount > 0)
                <span class="px-1.5 py-0.2 text-[10px] font-bold bg-navy text-white rounded-full">{{ $logsCount }}</span>
            @endif
        </a>
        <a id="tour-bendahara-btn-create-tx" href="{{ route('bendahara.transactions.create') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-1.5 rounded-xl transition-all">
            <span class="material-symbols-outlined text-base">add</span>
            <span>Catat Transaksi</span>
        </a>
    </div>
</div>

<!-- DataTables Card -->
<div class="card overflow-hidden">
    <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
        <table id="transactionsTable" class="w-full table-clean table-responsive-cards text-left border-collapse text-xs sm:text-sm">
            <thead>
                <tr>
                    <th class="w-10 text-center">No</th>
                    <th class="w-28">Tanggal</th>
                    <th class="min-w-[130px]">Kategori</th>
                    <th>Deskripsi</th>
                    <th class="w-24 text-center">Tipe</th>
                    <th class="text-right w-32">Jumlah</th>
                    <th class="text-center w-20">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Hidden Form for Delete Action -->
<form id="deleteTransactionForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="reason" id="deleteReasonInput">
</form>

@push('scripts')
<script>
$(document).ready(function() {
    $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: "{{ route('bendahara.transactions.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-xs text-gray-400 font-mono' },
            { data: 'tanggal_transaksi', name: 'tanggal_transaksi', className: 'whitespace-nowrap text-xs sm:text-sm font-medium text-gray-700' },
            { data: 'category_badge', name: 'kategori.nama_kategori', orderable: false, className: 'whitespace-nowrap' },
            { data: 'keterangan_display', name: 'keterangan', className: 'text-xs sm:text-sm text-gray-800' },
            { data: 'type_badge', name: 'jenis_transaksi', className: 'whitespace-nowrap text-center' },
            { data: 'amount_display', name: 'total_nominal', className: 'whitespace-nowrap text-right text-xs sm:text-sm font-bold' },
            { data: 'actions', name: 'actions', className: 'whitespace-nowrap text-center', orderable: false, searchable: false }
        ],
        createdRow: function(row, data, dataIndex) {
            const labels = ['No', 'Tanggal', 'Kategori', 'Deskripsi', 'Tipe', 'Jumlah', 'Aksi'];
            $('td', row).each(function(i) {
                if (labels[i]) $(this).attr('data-label', labels[i]);
            });
        },
        language: {
            search: "",
            searchPlaceholder: "Cari transaksi kas...",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ entri",
            infoEmpty: "Menampilkan 0 entri",
            infoFiltered: "(disaring dari _MAX_ total)",
            paginate: {
                first: "«",
                last: "»",
                next: "›",
                previous: "‹"
            },
            emptyTable: "Belum ada transaksi kas yang dicatat",
            zeroRecords: "Tidak ada transaksi yang cocok"
        },
        dom: '<"flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-3 gap-2.5"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-3 gap-2.5"ip>',
        pageLength: 10,
        order: [[1, 'desc']],
        drawCallback: function() {
            $('.dataTables_paginate').addClass('flex justify-center sm:justify-end gap-1 mt-2 sm:mt-0');
        }
    });
});

function confirmDeleteTransaction(deleteUrl, description, amount) {
    Swal.fire({
        title: 'Hapus Transaksi?',
        html: `<p class="text-xs sm:text-sm text-gray-600 mb-2">Anda akan menghapus transaksi:<br><strong class="text-gray-900">${description}</strong> (Rp ${amount}).</p><p class="text-[11px] text-red-500 font-medium">Tindakan ini akan membatalkan alokasi kas terkait dan terekam di riwayat log.</p>`,
        input: 'text',
        inputPlaceholder: 'Tulis alasan penghapusan (opsional)...',
        inputAttributes: {
            autocapitalize: 'off',
            class: 'swal2-input text-sm'
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: (reason) => {
            const form = document.getElementById('deleteTransactionForm');
            form.action = deleteUrl;
            document.getElementById('deleteReasonInput').value = reason || 'Dihapus oleh bendahara';
            form.submit();
        }
    });
}
</script>
@endpush
</x-app-layout>
