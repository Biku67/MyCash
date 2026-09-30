# Aturan Interaksi & Pengeditan Kode MyCash

Seluruh proses pengembangan dan penyuntingan kode di repositori ini WAJIB mematuhi dua prinsip utama berikut:

## 1. Wajib Konfirmasi Sebelum Melakukan Perubahan Besar
- Sebelum mengeksekusi langkah atau perubahan yang berpotensi memiliki dampak besar, asisten **WAJIB selalu menanyakan dan meminta konfirmasi/persetujuan pengguna terlebih dahulu**.
- Kategori perubahan besar meliputi:
  - Perombakan total struktur layout/tata letak halaman atau komponen.
  - Perubahan arsitektur sistem, skema database (migration/dropping), atau alur routing inti.
  - Penghapusan atau penggantian komponen besar yang sudah ada.
  - Perubahan dependensi, build configuration, atau alur autentikasi/otorisasi.

## 2. Preservasi Komentar dan Perubahan Non-Dampak dari Pengguna
- Ketika pengguna menambahkan modifikasi kode yang bersifat non-dampak fungsional (seperti komentar penjelasan, anotasi pribadi, catatan to-do, blok komentar Blade/HTML/PHP/JS, atau penataan format):
  - **DILARANG KERAS** menghapus, menimpa, atau menghilangkan komentar/catatan yang telah dibuat pengguna tersebut saat melakukan revisi atau perbaikan kode di area tersebut.
  - Seluruh komentar dan anotasi pengguna harus selalu dipertahankan utuh (*preserved intact*).
  - Lakukan revisi secara selektif dan presisi pada baris kode yang ditargetkan tanpa mengotori atau menyapu bersih catatan yang telah ditulis pengguna.
