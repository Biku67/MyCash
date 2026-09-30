# Aturan Konsistensi Palet Warna & Desain MyCash

Setiap pembuatan atau modifikasi tampilan antarmuka (UI) baik pada landing page, dashboard, komponen, maupun modal, WAJIB mematuhi palet warna resmi MyCash yang sudah terdaftar di `tailwind.config.js`:

## 1. Warna Utama (Primary - Navy)
- **Default Navy:** `#1B4F72` (`text-navy`, `bg-navy`, `border-navy`)
- **Light Navy:** `#2471A3` (`bg-navy-light`, `text-navy-light`)
- **Dark Navy:** `#154360` (`bg-navy-dark`, `hover:bg-navy-dark`)
- **Deep Obsidian Navy:** `#0B192C` / `#0A1118` (untuk section gelap bernuansa mewah)

## 2. Warna Aksen (Accent - Teal)
- **Default Teal:** `#5DCAA5` (`text-teal-accent`, `bg-teal-accent`, `border-teal-accent`)
- **Dark Teal:** `#48B08E` (`bg-teal-accent-dark`, `hover:bg-teal-accent-dark`)
- **Dim Glow Teal:** `rgba(93, 202, 165, 0.1)` (`bg-teal-accent-dim`)

## 3. Warna Teks & Permukaan Netral
- **Secondary Text:** `#64748B` (`text-on-surface-secondary`)
- **Tertiary Text:** `#94A3B8` (`text-on-surface-tertiary`)
- **Light Surface:** `#F8FAFC` / `#FFFFFF`

## 4. Batasan Mutlak terhadap Referensi Eksternal
- Ketika meniru atau mengadopsi layout, tata letak, ritme tipografi, atau komponen dari website inspirasi luar (seperti Motion, Webflow, Cipharvin, dsb), **DILARANG** mengganti palet warna MyCash dengan warna brand website referensi tersebut (misalnya warna merah, oranye, dsb).
- Selalu adaptasikan aksen referensi menjadi **Teal Accent (`#5DCAA5`)** dan warna utama menjadi **Navy (`#1B4F72` / `#0B192C`)**.
