<x-app-layout>
@section('page-title', 'Riwayat Log Transaksi')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="mb-2">
            <a href="{{ route('bendahara.transactions.index') }}" class="text-slate-500 hover:text-slate-800 text-xs sm:text-sm inline-flex items-center gap-1.5 font-medium transition-colors">
                <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Transaksi Kas
            </a>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Riwayat Log Transaksi</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Catatan lengkap setiap transaksi yang dibuat, disunting, atau dihapus.</p>
    </div>
    <div class="w-full sm:w-auto">
        <a href="{{ route('bendahara.transactions.index') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-2 w-full sm:w-auto shadow-sm rounded-xl font-semibold">
            <span class="material-symbols-outlined text-base sm:text-lg">receipt_long</span>
            <span>Kelola Transaksi</span>
        </a>
    </div>
</div>

<!-- DataTables Card for Logs -->
<div class="card overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm">
    <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
        <table id="logsTable" class="w-full table-clean table-responsive-cards text-left border-collapse text-xs sm:text-sm">
            <thead>
                <tr>
                    <th class="w-10 text-center">No</th>
                    <th class="w-36">Waktu</th>
                    <th class="w-32">Pengguna</th>
                    <th class="w-24 text-center">Aksi</th>
                    <th>Detail & Alasan Perubahan</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#logsTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: "{{ route('bendahara.transactions.logs') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-xs text-gray-400 font-mono' },
            { data: 'created_at', name: 'created_at', className: 'whitespace-nowrap text-xs text-gray-600' },
            { data: 'user_name', name: 'user_name', orderable: false, className: 'whitespace-nowrap text-xs font-semibold text-navy' },
            { data: 'action_badge', name: 'action_badge', orderable: false, searchable: false, className: 'whitespace-nowrap text-center' },
            { data: 'details', name: 'details', orderable: false, searchable: false, className: 'text-xs text-gray-800' }
        ],
        createdRow: function(row, data, dataIndex) {
            const labels = ['No', 'Waktu', 'Pengguna', 'Aksi', 'Detail & Alasan Perubahan'];
            $('td', row).each(function(i) {
                if (labels[i]) $(this).attr('data-label', labels[i]);
            });
        },
        language: {
            search: "",
            searchPlaceholder: "Cari riwayat log...",
            lengthMenu: "Tampilkan _MENU_ log",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ log",
            infoEmpty: "Menampilkan 0 log",
            infoFiltered: "(disaring dari _MAX_ total)",
            paginate: {
                first: "«",
                last: "»",
                next: "›",
                previous: "‹"
            },
            emptyTable: "Belum ada riwayat perubahan transaksi yang tercatat",
            zeroRecords: "Tidak ada data log yang cocok"
        },
        dom: '<"flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-3 gap-2.5"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-3 gap-2.5"ip>',
        pageLength: 10,
        order: [[1, 'desc']],
        drawCallback: function() {
            $('.dataTables_paginate').addClass('flex justify-center sm:justify-end gap-1 mt-2 sm:mt-0');
        }
    });
});
</script>
@endpush
</x-app-layout>
