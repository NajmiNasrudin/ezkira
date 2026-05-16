# EZKIRA — Panduan Pasang di Shared Hosting
### Untuk Pemula — Arahan Lengkap Langkah demi Langkah

---

## Apa yang Anda Perlukan Sebelum Mula

Sebelum mula, pastikan anda ada semua ini:

1. **Fail projek EZKIRA** — folder yang mengandungi semua kod aplikasi
2. **Akaun hosting** — anda dah beli hosting dan ada akses ke cPanel
3. **Domain** — contoh `www.bisnesanda.com` yang dah disambung ke hosting
4. **phpMyAdmin access** — biasanya sudah ada dalam cPanel
5. **FTP atau File Manager** — untuk upload fail (kita akan guna File Manager cPanel, tiada software tambahan diperlukan)

> **Apa itu cPanel?**
> cPanel adalah papan kawalan hosting anda. Dari sini anda boleh urus fail, database, email dan sebagainya. Biasanya boleh akses di `https://namadomain.com/cpanel` atau `https://namadomain.com:2083`.

---

# BAHAGIAN 1 — SEDIAKAN FAIL DI KOMPUTER ANDA

---

## Langkah 1 — Zip Semua Fail Projek

Kita perlu zip (mampatkan) semua fail projek supaya senang diupload.

**Apa itu ZIP?**
ZIP adalah cara untuk gabungkan banyak fail menjadi satu fail sahaja, supaya lebih mudah dihantar atau diupload.

### Cara buat ZIP (Windows):

1. Buka **File Explorer** (tekan `Windows + E`)
2. Pergi ke folder di mana projek EZKIRA anda disimpan
   - Contoh: `C:\Users\NamaAnda\Desktop\ezkira`
3. **Masuk ke dalam** folder tersebut — anda patut nampak fail-fail seperti `index.php`, `.htaccess`, folder `app`, `config`, `views` dan sebagainya
4. Tekan `Ctrl + A` untuk **pilih semua fail dan folder**
5. Klik kanan pada mana-mana fail yang terpilih
6. Pilih **"Send to"** → klik **"Compressed (zipped) folder"**
7. Satu fail baru bernama `ezkira.zip` (atau nama lain) akan muncul
8. Namakan semula fail zip tersebut kepada **`ezkira.zip`**

> ⚠️ **PENTING — Jangan Silap Zip!**
> Anda perlu zip **isi dalam** folder, bukan folder itu sendiri.
>
> ✅ **BETUL** — Bila anda extract zip, terus nampak `index.php`
> ❌ **SALAH** — Bila extract, nampak folder `ezkira` dulu, baru nampak `index.php` di dalam

### Cara tengok fail tersembunyi (.htaccess) di Windows:

Fail `.htaccess` adalah fail tersembunyi. Untuk pastikan ia ada:
1. Dalam File Explorer, klik tab **"View"** (atas)
2. Tandakan kotak **"Hidden items"**
3. Sekarang anda patut nampak fail `.htaccess` dalam folder projek

---

# BAHAGIAN 2 — LOG MASUK DAN UPLOAD KE HOSTING

---

## Langkah 2 — Log Masuk ke cPanel

1. Buka **Google Chrome** atau mana-mana browser
2. Taip alamat cPanel anda di bar URL:
   - Cuba: `https://namadomain.com/cpanel`
   - Atau: `https://namadomain.com:2083`
   - Atau: Semak email "Welcome" dari hosting provider anda — ada link cPanel di sana
3. Anda akan nampak halaman log masuk cPanel
4. Masukkan **Username** dan **Password** yang diberikan oleh hosting provider
5. Klik butang **"Log in"**
6. Anda sekarang berada di **dashboard cPanel** — halaman dengan banyak ikon

> **Tak tahu username/password cPanel?**
> Semak email dari hosting provider anda. Cari email dengan tajuk seperti "Welcome to your hosting account" atau "Your hosting details". Semua maklumat ada di sana.

---

## Langkah 3 — Upload Fail ke Hosting

Kita akan guna **File Manager** yang ada dalam cPanel — tidak perlu install software lain.

### 3.1 — Buka File Manager

1. Di dashboard cPanel, **scroll ke bawah** atau guna kotak carian
2. Cari ikon berlabel **"File Manager"** (biasanya ada ikon folder)
3. Klik **"File Manager"**
4. File Manager akan buka — anda akan nampak senarai folder dan fail di server hosting anda

### 3.2 — Aktifkan Papar Fail Tersembunyi

Ini penting supaya fail `.htaccess` nampak:

1. Di File Manager, cari butang **"Settings"** (biasanya atas sebelah kanan)
2. Klik **"Settings"**
3. Satu kotak kecil akan muncul
4. **Tandakan** kotak **"Show Hidden Files (dotfiles)"**
5. Klik **"Save"**

### 3.3 — Pergi ke Folder public_html

1. Di panel kiri File Manager, anda nampak senarai folder
2. Klik folder **`public_html`** — ini adalah folder utama website anda
3. Panel kanan akan tunjuk isi dalam `public_html`

> **Apa itu public_html?**
> Ini adalah folder yang "menghadap internet". Bila seseorang taip domain anda di browser, server akan tunjuk kandungan dari folder ini. Semua fail website mesti diletakkan di sini.

### 3.4 — Upload Fail ZIP

1. Pastikan anda berada dalam folder `public_html` (semak path di bahagian atas — patut nampak `/public_html`)
2. Klik butang **"Upload"** di toolbar atas
3. Tab/halaman baru akan buka
4. Klik butang **"Select File"** atau drag fail ke kawasan tersebut
5. Navigasi ke komputer anda → pilih fail **`ezkira.zip`** yang tadi kita buat
6. Upload akan bermula — tunggu bar hijau capai **100%**
7. Bila dah siap, **tutup tab upload** tersebut
8. Kembali ke tab File Manager → klik butang **Refresh** (ikon pusing) atau tekan F5

### 3.5 — Extract (Buka) Fail ZIP

1. Dalam File Manager, anda patut nampak fail `ezkira.zip`
2. Klik **sekali** pada fail `ezkira.zip` untuk pilihnya (jangan double-click)
3. Klik butang **"Extract"** di toolbar atas
   - Atau: Klik kanan pada `ezkira.zip` → pilih **"Extract"**
4. Satu kotak dialog akan muncul — ia tanya "Extract to where?"
5. Pastikan path adalah `/public_html` → klik **"Extract File(s)"**
6. Proses extract akan berlaku — tunggu sehingga selesai
7. Klik **"Close"**

### 3.6 — Semak Fail Sudah Betul

1. Klik **Refresh**
2. Dalam `public_html`, anda patut nampak:
   - Fail `index.php`
   - Fail `.htaccess`
   - Folder `app`, `config`, `views`, `assets`, dll.
3. Kalau anda nampak folder `ezkira` dalam `public_html` (bukannya terus `index.php`), bermakna zip anda tadi ada extra folder — anda perlu:
   - Masuk ke dalam folder `ezkira` tersebut
   - Pilih semua → **Move** ke `/public_html`
   - Padam folder `ezkira` yang kosong tadi

---

# BAHAGIAN 3 — BUAT DAN SEDIAKAN DATABASE

---

## Langkah 4 — Buat Database MySQL

**Apa itu Database?**
Database adalah tempat simpan semua data aplikasi — maklumat pengguna, rekod jualan, perbelanjaan, dan sebagainya. Fikirkan ia seperti fail Excel yang sangat besar dan selamat.

### 4.1 — Buka MySQL Databases

1. Klik butang **"cPanel"** atau ikon rumah untuk balik ke dashboard cPanel
2. Scroll ke bahagian **"Databases"**
3. Klik **"MySQL Databases"**

### 4.2 — Buat Database Baru

1. Anda akan nampak ruangan **"Create New Database"**
2. Dalam kotak teks, taip: `ezkira`
3. Klik butang **"Create Database"**
4. Anda akan nampak mesej: *"Added the database..."*
5. **CATAT** nama penuh database — ia akan jadi sesuatu seperti: `namaakaun_ezkira`
   - Contoh: Jika username hosting anda `mybiz123`, nama database jadi `mybiz123_ezkira`
6. Klik **"Go Back"**

### 4.3 — Buat MySQL User Baru

MySQL user adalah "kunci" untuk masuk ke database. Aplikasi kita perlukan kunci ini.

1. Scroll ke bawah ke bahagian **"MySQL Users"**
2. Dalam kotak **"Username"**, taip: `ezkirauser`
   - Nama penuh user jadi: `namaakaun_ezkirauser`
3. Untuk password, klik butang **"Password Generator"**
4. Satu kotak akan muncul dengan password rawak yang kuat
5. Klik **"Copy Password"** — kemudian **tampal (paste) dalam Notepad** untuk simpan
6. Tandakan kotak **"I have copied this password..."**
7. Klik **"Use Password"**
8. Klik butang **"Create User"**
9. Klik **"Go Back"**

### 4.4 — Sambungkan User dengan Database

Langkah ini bagi kebenaran kepada user untuk akses database.

1. Scroll ke bawah ke bahagian **"Add User To Database"**
2. Dropdown **"User"**: Pilih user yang baru dibuat (`namaakaun_ezkirauser`)
3. Dropdown **"Database"**: Pilih database yang baru dibuat (`namaakaun_ezkira`)
4. Klik **"Add"**
5. Halaman baru akan muncul bertajuk **"Manage User Privileges"**
6. Klik kotak **"ALL PRIVILEGES"** (kotak paling atas) — ini akan tandakan semua kotak di bawahnya
7. Klik **"Make Changes"**
8. Klik **"Go Back"**

> 📋 **Simpan maklumat ini — anda akan perlukan dalam Langkah 6:**
>
> ```
> Nama Database : namaakaun_ezkira
> Username DB   : namaakaun_ezkirauser
> Password DB   : [password yang anda copy tadi]
> ```

---

## Langkah 5 — Import Database (Buat Jadual & Data Awal)

Sekarang kita perlu "isi" database dengan struktur jadual yang diperlukan oleh aplikasi.

### 5.1 — Buka phpMyAdmin

1. Balik ke **dashboard cPanel**
2. Dalam bahagian **"Databases"**, klik **"phpMyAdmin"**
3. phpMyAdmin akan buka dalam tab baru — ini adalah antara muka untuk urus database

### 5.2 — Pilih Database Anda

1. Di panel **kiri** phpMyAdmin, anda akan nampak senarai database
2. Klik pada nama database anda: `namaakaun_ezkira`
3. Panel kanan akan tunjuk "No tables found" — ini normal, database masih kosong

### 5.3 — Import fail schema.sql

Fail ini akan buat semua jadual yang diperlukan.

1. Klik tab **"Import"** di bahagian atas (menu horizontal)
2. Anda akan nampak halaman import
3. Klik butang **"Choose File"** atau **"Browse"**
4. Di komputer anda, navigasi ke folder projek EZKIRA
5. Masuk ke folder **`database`**
6. Pilih fail **`schema.sql`**
7. Klik **"Open"**
8. Scroll ke bawah halaman import
9. Klik butang **"Go"** (atau **"Import"**)
10. Tunggu beberapa saat
11. Anda akan nampak mesej dalam kotak hijau: **"Import has been successfully finished"**

### 5.4 — Import fail seed.sql

Fail ini akan buat akaun admin pertama untuk anda.

1. Klik tab **"Import"** sekali lagi
2. Klik **"Choose File"**
3. Pilih fail **`seed.sql`** (masih dalam folder `database`)
4. Klik **"Go"**
5. Tunggu mesej hijau kejayaan

### 5.5 — Verify Jadual Telah Dibuat

1. Klik nama database anda di panel kiri
2. Anda patut nampak senarai jadual seperti ini:
   - `activity_logs`
   - `expenses`
   - `revenue_targets`
   - `revenues`
   - `sessions`
   - `settings`
   - `users`
3. Jika semua jadual ada — **berjaya!** ✅

---

# BAHAGIAN 4 — KONFIGURASI APLIKASI

---

## Langkah 6 — Edit Fail config.php

Fail ini adalah "tetapan utama" aplikasi. Kita perlu beritahu aplikasi: di mana database, apa nama domain, dan sebagainya.

### 6.1 — Buka Fail config.php untuk Diedit

1. Balik ke **cPanel** → **File Manager**
2. Dalam `public_html`, klik folder **`config`**
3. Anda akan nampak fail **`config.php`**
4. Klik **sekali** pada `config.php` untuk pilihnya
5. Klik butang **"Edit"** di toolbar atas
   - Atau: Klik kanan → **"Edit"**
6. Mungkin ada popup tanya tentang encoding → klik sahaja **"Edit"**
7. Editor teks akan buka dengan kod PHP

### 6.2 — Tukar Nilai-Nilai Penting

Cari baris-baris berikut dan tukar nilainya. **Jangan ubah apa-apa yang lain.**

---

**① Tukar APP_URL kepada domain sebenar anda:**

Cari baris ini:
```php
define('APP_URL', 'http://localhost:8001');
```
Tukar kepada domain anda (dengan https jika ada SSL):
```php
define('APP_URL', 'https://namadomain.com');
```

---

**② Tukar APP_ENV kepada production:**

Cari baris ini:
```php
define('APP_ENV', 'development');
```
Tukar kepada:
```php
define('APP_ENV', 'production');
```

---

**③ Tukar APP_DEBUG kepada false:**

Cari baris ini:
```php
define('APP_DEBUG', true);
```
Tukar kepada:
```php
define('APP_DEBUG', false);
```

---

**④ Isi maklumat Database:**

Cari bahagian ini:
```php
define('DB_NAME', 'fikira_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```
Tukar dengan maklumat dari Langkah 4:
```php
define('DB_NAME', 'namaakaun_ezkira');       // ← nama database anda
define('DB_USER', 'namaakaun_ezkirauser');    // ← username database anda
define('DB_PASS', 'passwordanda');            // ← password yang anda simpan tadi
```

---

**⑤ Tetapkan BASE_URI:**

Cari baris ini:
```php
define('BASE_URI', '');
```

- Jika website anda di **root domain** (contoh: `https://namadomain.com`) → **biarkan kosong**:
```php
define('BASE_URI', '');
```

- Jika website anda dalam **subfolder** (contoh: `https://namadomain.com/ezkira`) → isikan nama subfolder:
```php
define('BASE_URI', '/ezkira');
```

---

### 6.3 — Simpan Perubahan

1. Klik butang **"Save Changes"** (atas kanan editor)
2. Tutup editor

---

## Langkah 7 — Tetapkan Kebenaran Fail (Permissions)

**Apa itu permissions?**
Permissions mengawal siapa yang boleh baca, tulis, atau jalankan sesuatu fail/folder. Nombor seperti `755` atau `444` adalah kod permissions tersebut.

- `755` = Owner boleh buat semua, orang lain boleh baca & jalankan sahaja
- `444` = Semua orang hanya boleh **baca** sahaja (tidak boleh edit atau padam)

### 7.1 — Cara Tukar Permissions

1. Dalam **File Manager**, navigasi ke fail/folder berkenaan
2. Klik sekali pada fail/folder tersebut untuk pilihnya
3. Klik butang **"Permissions"** di toolbar atas
   - Atau: Klik kanan → **"Change Permissions"**
4. Kotak dialog akan muncul
5. Dalam kotak **"Permission"** (bawah sekali), taip nombor yang dikehendaki
6. Klik **"Change Permissions"**

### 7.2 — Senarai Permissions yang Perlu Ditetapkan

Lakukan satu-persatu untuk setiap fail/folder berikut:

| Fail / Folder | Permissions | Cara Navigasi |
|---------------|-------------|---------------|
| `config/config.php` | **444** | `public_html` → `config` → `config.php` |
| `uploads/` | **755** | `public_html` → `uploads` |
| `uploads/profiles/` | **755** | `public_html` → `uploads` → `profiles` |
| `uploads/receipts/` | **755** | `public_html` → `uploads` → `receipts` |
| `storage/` | **755** | `public_html` → `storage` |
| `storage/logs/` | **755** | `public_html` → `storage` → `logs` |
| `storage/sessions/` | **755** | `public_html` → `storage` → `sessions` |

### 7.3 — Buat Folder yang Mungkin Tiada

Jika folder `storage/logs`, `storage/sessions`, `uploads/profiles`, atau `uploads/receipts` tidak wujud:

1. Navigasi ke folder parent (contoh: pergi ke `uploads`)
2. Klik butang **"+ Folder"** atau **"New Folder"** di toolbar
3. Taip nama folder baru
4. Klik **"Create New Folder"**
5. Set permission folder baru kepada **755**

---

## Langkah 8 — Tetapkan Versi PHP

**Kenapa ini penting?**
Aplikasi EZKIRA dibina menggunakan ciri-ciri PHP 8.1 ke atas. Jika hosting guna PHP versi lama, aplikasi tidak akan berfungsi.

### 8.1 — Tukar PHP Version

1. Balik ke **dashboard cPanel**
2. Cari **"PHP Selector"** atau **"MultiPHP Manager"** atau **"Select PHP Version"**
   - Lokasinya berbeza mengikut hosting provider
3. Jika nampak **MultiPHP Manager**:
   - Cari domain anda dalam senarai
   - Dropdown di sebelah kanan → pilih **PHP 8.1** atau **PHP 8.2**
   - Klik **"Apply"**
4. Jika nampak **PHP Selector**:
   - Pilih **PHP 8.1** atau **PHP 8.2**
   - Klik **"Set as current"**

### 8.2 — Aktifkan PHP Extensions

Masih dalam PHP Selector, cari tab **"Extensions"**. Pastikan extensions berikut **ditandakan (✓)**:

- `pdo_mysql` — untuk sambungan database
- `fileinfo` — untuk semak jenis fail upload
- `mbstring` — untuk sokongan bahasa
- `session` — untuk sistem log masuk
- `json` — untuk data processing
- `openssl` — untuk keselamatan

Klik **"Save"** jika ada perubahan.

---

## Langkah 9 — Aktifkan SSL (Paparkan https://)

**Apa itu SSL?**
SSL memastikan data yang dihantar antara browser dan server anda adalah selamat dan disulitkan. Website dengan SSL akan papar `https://` dan ikon mangga kunci di browser.

### 9.1 — Aktifkan SSL percuma (Let's Encrypt)

1. Dalam **cPanel**, cari **"SSL/TLS"** atau **"Let's Encrypt SSL"** atau **"AutoSSL"**
2. Klik padanya
3. Jika ada butang **"Run AutoSSL"** → klik sahaja, SSL akan dipasang automatik
4. Jika ada **"Issue Certificate"**:
   - Pilih domain anda
   - Klik **"Issue"**
   - Tunggu beberapa minit (kadang-kadang sehingga 30 minit)
5. Semak — bila buka domain anda dalam browser, patut nampak `https://` dan ikon kunci

> **Tak nampak pilihan SSL?**
> Hubungi support hosting anda dan minta mereka aktifkan SSL/Let's Encrypt untuk domain anda. Ini percuma untuk kebanyakan hosting.

---

# BAHAGIAN 5 — TEST DAN GO LIVE

---

## Langkah 10 — Test Aplikasi

### 10.1 — Buka Website

1. Buka tab baru dalam browser
2. Taip domain anda: `https://namadomain.com`
3. Tekan Enter

**Apa yang patut berlaku:**
- Browser akan redirect automatik ke halaman Login
- Halaman login EZKIRA akan muncul dengan logo dan form log masuk

**Jika nampak error** — jangan panik! Scroll ke bawah ke bahagian "Masalah Biasa" dalam panduan ini.

### 10.2 — Log Masuk Pertama Kali

1. Dalam halaman Login, masukkan:
   - **Email**: `admin@ezkira.com`
   - **Password**: `Admin@1234`
2. Klik butang **"Log In"**
3. Anda patut dibawa ke halaman **Dashboard**

### 10.3 — Tukar Password SEGERA

⚠️ Password lalai `Admin@1234` perlu ditukar segera selepas log masuk pertama!

1. Klik nama anda di bahagian atas kanan (atau ikon avatar)
2. Klik **"My Profile"**
3. Klik tab **"Change Password"**
4. Isikan:
   - **Current Password**: `Admin@1234`
   - **New Password**: Password baru anda (minimum 8 huruf, gabungkan huruf besar, kecil, nombor dan simbol. Contoh: `MyBiz@2025!`)
   - **Confirm New Password**: Taip semula password baru
5. Klik **"Save"**

### 10.4 — Kemaskini Maklumat Syarikat

1. Dalam **My Profile** → tab **"Personal Information"**
2. Tukar maklumat berikut:
   - **Company Name**: Nama syarikat anda
   - **PIC Name**: Nama anda (Person In Charge)
   - **Email**: Email syarikat
   - **WhatsApp**: Nombor WhatsApp
3. Klik **"Save Changes"**

### 10.5 — Checklist Test Akhir

Uji semua fungsi ini satu-persatu:

- [ ] Halaman login muncul dengan betul
- [ ] Boleh log masuk
- [ ] Dashboard papar tanpa error
- [ ] Klik tab **Revenue** — halaman load
- [ ] Tambah jualan baru (Revenue) — klik "+ Add Sale"
- [ ] Klik tab **Expenses** — halaman load
- [ ] Tambah perbelanjaan baru — klik "+ Add Expense"
- [ ] Cuba upload resit (PDF atau gambar)
- [ ] Klik **Export P&L** — fail CSV akan muat turun
- [ ] Toggle Dark Mode (ikon bulan atas kanan) — warna bertukar
- [ ] Tukar bahasa BM ↔ EN — teks bertukar
- [ ] Profile → Upload logo syarikat

---

# BAHAGIAN 6 — MASALAH BIASA & CARA SELESAIKAN

---

### ❌ Masalah: "500 Internal Server Error"

**Apa maksudnya:** Server ada masalah menjalankan kod.

**Cara selesaikan:**

1. Semak fail `.htaccess` ada dalam `public_html`:
   - Buka File Manager → pastikan Show Hidden Files aktif → cari `.htaccess`
   - Jika tiada, fail ini tidak ter-upload — upload semula

2. Semak PHP version:
   - cPanel → PHP Selector → pastikan PHP 8.1 ke atas dipilih

3. Tengok error log untuk tahu punca sebenar:
   - File Manager → `public_html` → `storage` → `logs` → buka `php-error.log`
   - Hantar isi error log ini kepada developer

---

### ❌ Masalah: "Database Connection Failed" atau "SQLSTATE error"

**Apa maksudnya:** Aplikasi tidak dapat sambung ke database.

**Cara selesaikan:**

1. Buka `config/config.php` → semak semula 3 baris ini:
```php
define('DB_NAME', '...');   // ← pastikan nama DB betul (dengan prefix hosting)
define('DB_USER', '...');   // ← pastikan username betul (dengan prefix hosting)
define('DB_PASS', '...');   // ← pastikan password betul
```

2. Verify dengan cuba log masuk ke phpMyAdmin menggunakan username dan password yang sama:
   - cPanel → phpMyAdmin → Log in as: (masukkan username & password DB)
   - Kalau tak boleh masuk, bermakna username/password salah

3. Semak user sudah di-assign ke database:
   - cPanel → MySQL Databases → scroll ke bahagian "Current Databases"
   - Pastikan user anda ada dalam senarai "Users" untuk database berkenaan

---

### ❌ Masalah: Halaman 404 atau "Page Not Found"

**Apa maksudnya:** Routing tidak berfungsi dengan betul.

**Cara selesaikan:**

1. Semak `BASE_URI` dalam `config.php`:
   - Website di root (`namadomain.com`) → `define('BASE_URI', '');`
   - Website di subfolder (`namadomain.com/ezkira`) → `define('BASE_URI', '/ezkira');`

2. Semak `.htaccess` ada dan betul:
   - File Manager → cari `.htaccess` dalam `public_html`
   - Klik kanan → View untuk tengok isinya
   - Mesti ada baris `RewriteEngine On`

3. Hubungi support hosting — minta mereka confirm **mod_rewrite** aktif untuk domain anda

---

### ❌ Masalah: Upload Gambar/Resit Tidak Berfungsi

**Cara selesaikan:**

1. Pastikan folder-folder ini wujud dalam `public_html`:
   - `uploads/profiles/`
   - `uploads/receipts/`

2. Set permission kedua-dua folder ke **755**:
   - Klik kanan folder → Change Permissions → taip 755

---

### ❌ Masalah: Halaman Putih (Blank White Page)

**Cara selesaikan:**

1. Sementara, tukar `config.php`:
```php
define('APP_DEBUG', true);
```
2. Refresh halaman — error yang sebenar akan muncul
3. Selesaikan error tersebut
4. Tukar semula ke `false` selepas selesai

---

# RINGKASAN — MAKLUMAT LOG MASUK PERTAMA

| Perkara | Nilai |
|---------|-------|
| URL Website | `https://namadomain.com` |
| Email Admin | `admin@ezkira.com` |
| Password Admin | `Admin@1234` |
| ⚠️ Tindakan | **Tukar password segera selepas log masuk!** |

---

# SENARAI SEMAK AKHIR

Tandakan semua sebelum umum website:

- [ ] Semua fail telah diupload ke `public_html`
- [ ] `schema.sql` dan `seed.sql` telah diimport
- [ ] `config.php` telah dikemaskini dengan maklumat betul
- [ ] `APP_ENV` = `production`
- [ ] `APP_DEBUG` = `false`
- [ ] `APP_URL` guna `https://`
- [ ] `config.php` permission ditukar ke `444`
- [ ] Folder `uploads/` dan `storage/` permission = `755`
- [ ] PHP 8.1 ke atas dipilih
- [ ] SSL aktif (https berfungsi)
- [ ] Boleh log masuk ke dashboard
- [ ] Password admin telah ditukar
- [ ] Maklumat syarikat telah diisi

---

**Perlukan bantuan?**
Hubungi: bizbuddyhq@gmail.com | +60122541050

*EZKIRA v1.0 · Dibuat oleh NajmiNasrudin*
