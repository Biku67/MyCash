<x-app-layout>
@section('page-title', 'Kelola Bendahara')

<div class="space-y-6">
    <!-- Header Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Manajemen Bendahara</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola akun bendahara dan penugasan kelas yang dikelola.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.bendahara.create') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center gap-2 shadow-sm transition-all">
                <span class="material-symbols-outlined text-base">person_add</span>
                <span>Tambah Bendahara</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Bendahara</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 font-heading">{{ $totalBendahara }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">groups</span>
            </div>
        </div>

        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Kelas</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 font-heading">{{ $totalKelas }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">school</span>
            </div>
        </div>
    </div>

    <!-- DataTables Card -->
    <div class="card overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
            <table id="bendaharaTable" class="w-full table-clean table-responsive-cards text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr>
                        <th class="w-10 text-center">No</th>
                        <th class="min-w-[180px]">Nama Bendahara</th>
                        <th class="min-w-[140px]">Kelas</th>
                        <th class="min-w-[120px]">Kontak & NIS</th>
                        <th class="w-28 text-center">Siswa</th>
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
    $('#bendaharaTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: "{{ route('admin.bendahara.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-xs text-gray-400 font-mono' },
            { data: 'bendahara_info', name: 'nama' },
            { data: 'kelas_badge', name: 'kode_kelas' },
            { data: 'contact_info', name: 'nis' },
            { data: 'students_count', name: 'students_count', orderable: false, searchable: false, className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-right' }
        ],
        createdRow: function(row, data, dataIndex) {
            const labels = ['No', 'Nama Bendahara', 'Kelas', 'Kontak & NIS', 'Siswa', 'Aksi'];
            $('td', row).each(function(i) {
                if (labels[i]) $(this).attr('data-label', labels[i]);
            });
        },
        dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari bendahara...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ bendahara",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Tidak ada bendahara yang cocok",
            paginate: {
                previous: "←",
                next: "→"
            }
        }
    });
});

function confirmDeleteBendahara(e, name) {
    e.preventDefault();
    const form = e.target;
    Swal.fire({
        title: 'Hapus Bendahara?',
        text: 'Akun ' + name + ' dan data akses bendahara ini akan dihapus permanen dari sistem.',
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
