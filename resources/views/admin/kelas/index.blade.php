<x-app-layout>
@section('page-title', 'Kelola Kelas')

<div class="space-y-6">
    <!-- Header Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Manajemen Master Kelas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola data kelas, wali kelas yang bertugas, dan nominal iuran kas.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.kelas.create') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center gap-2 shadow-sm transition-all">
                <span class="material-symbols-outlined text-base">add_circle</span>
                <span>Tambah Kelas</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Kelas</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 font-heading">{{ $totalKelas }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">school</span>
            </div>
        </div>

        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Siswa Terdata</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 font-heading">{{ $totalSiswa }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">groups</span>
            </div>
        </div>

        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Wali Kelas Terdaftar</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 font-heading">{{ $totalWaliKelas }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">supervisor_account</span>
            </div>
        </div>
    </div>

    <!-- DataTables Card -->
    <div id="tour-admin-kelas-table" class="card overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
            <table id="kelasTable" class="w-full table-clean table-responsive-cards text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr>
                        <th class="w-10 text-center">No</th>
                        <th class="min-w-[160px]">Kelas & Kode</th>
                        <th class="min-w-[170px]">Wali Kelas</th>
                        <th class="min-w-[130px]">Bendahara</th>
                        <th class="w-24 text-center">Siswa</th>
                        <th class="min-w-[140px]">Iuran Kas</th>
                        <th class="text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#kelasTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: "{{ route('admin.kelas.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-xs text-gray-400 font-mono' },
            { data: 'kelas_info', name: 'nama_kelas' },
            { data: 'wali_kelas_name', name: 'waliKelas.nama' },
            { data: 'bendahara_count', name: 'bendahara_count', orderable: false, searchable: false },
            { data: 'siswa_count', name: 'siswa_count', orderable: false, searchable: false, className: 'text-center' },
            { data: 'fee_parameters', name: 'nominal_standar' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-right' }
        ],
        createdRow: function(row, data, dataIndex) {
            const labels = ['No', 'Kelas & Kode', 'Wali Kelas', 'Bendahara', 'Siswa', 'Iuran Kas', 'Aksi'];
            $('td', row).each(function(i) {
                if (labels[i]) $(this).attr('data-label', labels[i]);
            });
        },
        dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari kelas...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ kelas",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Tidak ada data kelas yang cocok",
            paginate: {
                previous: "←",
                next: "→"
            }
        }
    });
});

function confirmDeleteKelas(e, name) {
    e.preventDefault();
    const form = e.target;
    Swal.fire({
        title: 'Hapus Kelas?',
        text: 'Apakah Anda yakin ingin menghapus data kelas ' + name + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endpush
</x-app-layout>
