<x-app-layout>
@section('page-title', 'Data Siswa')

<div class="space-y-6">
    <!-- Header Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Data Siswa Kelas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola data siswa dan akun kelas 
                <span class="font-semibold text-slate-700">{{ $kelas ? $kelas->nama_kelas : 'Belum Ditugaskan' }}</span>.
            </p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2.5">
            @if($kelas)
                <a href="{{ route('wali-kelas.students.create') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center gap-2 shadow-sm transition-all">
                    <span class="material-symbols-outlined text-base">person_add</span>
                    <span>Tambah Siswa</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Siswa Terdaftar</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 font-heading">{{ $totalSiswa }} Siswa</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">groups</span>
            </div>
        </div>

        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Kelas yang Diampu</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 font-heading">{{ $kelas ? $kelas->nama_kelas : '-' }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">school</span>
            </div>
        </div>
    </div>

    <!-- DataTables Card -->
    <div id="tour-wali-students-table" class="card overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
            <table id="waliStudentsTable" class="w-full table-clean table-responsive-cards text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr>
                        <th class="w-10 text-center">No</th>
                        <th class="min-w-[180px]">Nama Siswa & Akun</th>
                        <th class="w-32">NIS</th>
                        <th class="w-40">Kontak WhatsApp</th>
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
    $('#waliStudentsTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: "{{ route('wali-kelas.students.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-xs text-gray-400 font-mono' },
            { data: 'student_info', name: 'nama' },
            { data: 'nis_badge', name: 'nis' },
            { data: 'contact', name: 'no_hp' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-right' }
        ],
        createdRow: function(row, data, dataIndex) {
            const labels = ['No', 'Nama Siswa & Akun', 'NIS', 'Kontak WhatsApp', 'Aksi'];
            $('td', row).each(function(i) {
                if (labels[i]) $(this).attr('data-label', labels[i]);
            });
        },
        dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari nama atau NIS siswa...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ siswa",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Tidak ditemukan siswa yang cocok",
            paginate: {
                previous: "←",
                next: "→"
            }
        }
    });
});

function confirmResetPasswordStudent(event, name, nis) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        title: 'Reset Password Siswa?',
        html: `Password login untuk siswa <strong>${name}</strong> akan direset ke nomor NIS default: <br><span class="font-mono text-base font-bold text-navy mt-1.5 inline-block bg-slate-100 px-3 py-1 rounded-lg border border-slate-200">${nis}</span>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1B4F72',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Ya, Reset Password',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}

function confirmDeleteStudent(event, name) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        title: 'Hapus Siswa?',
        html: `Apakah Anda yakin ingin menghapus siswa <strong>${name}</strong>?<br><span class="text-xs text-gray-500 mt-2 block">Catatan: Siswa hanya dapat dihapus apabila belum memiliki riwayat transaksi kas.</span>`,
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
