# Panduan Deployment & Troubleshooting VM PDN — SIMAK Hukum (Kabupaten Buol)

Dokumen ini mencatat konteks infrastruktur, identifikasi masalah, koreksi atas analisis sebelumnya, serta panduan solusi konkret untuk pengelolaan aplikasi **SIMAK Hukum** yang di-deploy pada VM Windows Server di Pusat Data Nasional (PDN).

---

## 1. Spesifikasi Lingkungan (Environment)

| Parameter | Keterangan |
|---|---|
| **Platform** | PDN (`igcp2.layanan.go.id`) — Tenant: `kabupaten-buol` |
| **VM ID** | `vm-b0cbd2aa-d6e0-4bcc-956d-220aab242c9f` |
| **Sistem Operasi** | Windows Server (64-bit) |
| **Alokasi Sumber Daya** | 2 Core vCPU, 4 GB RAM, 50 GB HDD |
| **IP Internal** | `10.91.188.45` |
| **Port RDP** | `15629` (Custom NAT, bukan default `3389`) |
| **Web Server Stack** | XAMPP (Apache) di `C:\xampp\` |
| **Lokasi Aplikasi** | `C:\simak-hukum\` (Branch `main`) |
| **Database** | SQLite di `C:\simak-hukum\database\database.sqlite` (Data formulir utama disimpan di browser local storage) |
| **CLI Runtime** | Git Bash & Windows PowerShell |

---

## 2. Gejala Masalah & Insiden Awal

Saat aplikasi mulai digunakan untuk proses pembuatan dan preview dokumen Surat Keputusan (SK):
1. **VM Sering Freeze / RDP Disconnect**: Utilisasi RAM mencapai >95%, memicu `OutOfMemoryException` di PowerShell.
2. **Request Timeout**: Laravel log mencatat `Maximum execution time of 120 seconds exceeded`.
3. **Proses Zombie**: Terdapat proses `WINWORD.EXE` yang menggantung di Task Manager.
4. **Warning CLI PHP**: Muncul `PHP Warning: Unable to load dynamic library 'dotnet'` pada setiap eksekusi perintah `php` / `php artisan`.
5. **Kesalahan Pembacaan Error**: Muncul catatan log `Failed opening required PEAR`.

---

## 3. Analisis Akar Masalah & Koreksi Miskonsepsi

Berikut perbandingan analisis awal (sebelumnya) dengan temuan teknis sebenarnya:

### A. Miskonsepsi: Word "Terbuka / Perlu Ditutup"
- **Analisis Awal:** Mengira aplikasi Microsoft Word dibuka manual oleh pengguna lalu lupa ditutup.
- **Fakta Sebenarnya:** Proses `WINWORD.EXE` dipanggil otomatis secara terprogram oleh backend Laravel (`App\Services\SkExportService`) melalui `cscript.exe` (VBScript COM) dan `powershell.exe` untuk konversi DOCX ke PDF serta penggabungan lampiran.
- **Akar Masalah Hang:** Office COM Automation pada Windows Server berjalan di background (non-interaktif / session service). Word mengalami deadlock (hang) karena:
  1. Menunggu konfirmasi dialog invisible (First-Run Wizard, dialog aktivasi / lisensi, atau safe-mode recovery prompt).
  2. Folder profil DCOM sistem (`systemprofile\Desktop`) belum dibuat di Windows Server.

### B. Miskonsepsi: Solusi Timeout Cukup dengan Menaikkan `max_execution_time = 300`
- **Analisis Awal:** Menaikkan limit waktu eksekusi di `php.ini` dari 120 detik ke 300 detik.
- **Fakta Sebenarnya:** Menaikkan timeout tidak menyelesaikan deadlock Word COM. Request hanya akan menggantung lebih lama (5 menit) sebelum tetap berakhir dengan timeout, sementara proses `WINWORD.EXE` tetap menjadi zombie di RAM.

### C. Miskonsepsi: "Failed opening required PEAR — kemungkinan vendor belum lengkap"
- **Analisis Awal:** Menganggap ada pustaka PEAR yang belum terpasang.
- **Fakta Sebenarnya:** File `composer.json` projek sama sekali tidak memiliki dependensi PEAR. Format pesan fatal error bawaan PHP di Windows/XAMPP adalah:
  ```text
  Fatal error: require(): Failed opening required 'path/to/file.php' (include_path='.;C:\xampp\php\PEAR')
  ```
  String `C:\xampp\php\PEAR` hanyalah informasi `include_path` default XAMPP, bukan nama file yang hilang. Error ini dipicu oleh path SQLite lokal pengembang (`C:\Fachry\...`) yang terbawa di file `.env`.

### D. Warning `Unable to load dynamic library 'dotnet'`
- **Fakta:** Pada PHP 8.x, ekstensi COM bernama `com_dotnet`. Ekstensi `dotnet` versi lama sudah usang dan file `.dll`-nya tidak ada di direktori `ext/` XAMPP. Selain itu, aplikasi memanggil Word lewat CLI subprocess Windows (`cscript.exe` / `powershell.exe`), bukan ekstensi internal PHP, sehingga `extension=dotnet` tidak dibutuhkan.

### E. Konsumsi RAM Kritis pada VM 4GB
- **Fakta:** Windows Server membutuhkan 2.2 – 2.8 GB RAM untuk sistem dasar.
- Layanan yang memboroskan memori tanpa fungsi esensial:
  - `mysqld` (MySQL) memakan ~70 MB padahal aplikasi menggunakan SQLite/local storage. *(Catatan: Di XAMPP, nama service adalah `mysql`, bukan `mysqld`).*
  - `WSearch` (Windows Search Indexer) memakan 150–200 MB.
  - `MsMpEng` (Windows Defender Realtime) memakan 250–350 MB dan beban CPU tinggi saat kompilasi file dokumen temporer.
  - Paging File (Virtual Memory) belum dioptimasi untuk menampung lonjakan alokasi memori.

---

## 4. Langkah Perbaikan & Solusi Teruji (Runbook)

### Langkah 1: Bersihkan Sisa Proses Zombie
Jalankan di PowerShell (Run as Administrator):
```powershell
Stop-Process -Name "WINWORD", "cscript", "wscript" -Force -ErrorAction SilentlyContinue
```

### Langkah 2: Nonaktifkan Service yang Memboroskan RAM
```powershell
# Hentikan proses mysqld jika berjalan
Stop-Process -Name "mysqld" -Force -ErrorAction SilentlyContinue

# Matikan dan disable service MySQL XAMPP
Get-Service *mysql* | Stop-Service -Force -ErrorAction SilentlyContinue
Get-Service *mysql* | Set-Service -StartupType Disabled -ErrorAction SilentlyContinue

# Matikan dan disable Windows Search
Stop-Service -Name "WSearch" -Force -ErrorAction SilentlyContinue
Set-Service -Name "WSearch" -StartupType Disabled

# Matikan Realtime Monitoring Windows Defender
Set-MpPreference -DisableRealtimeMonitoring $true
```

### Langkah 3: Konfigurasi Virtual Memory (Paging File)
Agar PowerShell dan aplikasi tidak crash saat RAM fisik terpakai penuh:
```powershell
Set-CimInstance -Query "Select * from Win32_PageFileSetting" -Property @{InitialSize = 4096; MaximumSize = 8192} -ErrorAction SilentlyContinue
```

### Langkah 4: Perbaiki Konfigurasi DCOM Word di Windows Server
1. Buat folder profil sistem yang dibutuhkan Office COM:
   ```powershell
   New-Item -ItemType Directory -Path "C:\Windows\System32\config\systemprofile\Desktop" -Force
   New-Item -ItemType Directory -Path "C:\Windows\SysWOW64\config\systemprofile\Desktop" -Force
   ```
2. **Inisialisasi GUI Word (Wajib dilakukan sekali via RDP):**
   - Buka aplikasi **Microsoft Word** melalui Start Menu di sesi RDP.
   - Selesaikan wizard awal, setujui EULA/lisensi, dan tutup prompt aktivasi/welcome.
   - Tutup kembali aplikasi Word. Pastikan saat dibuka kembali tidak ada popup yang muncul.

### Langkah 5: Sesuaikan File `.env` Aplikasi di VM (`C:\simak-hukum\.env`)
Pastikan variabel berikut diatur sesuai lingkungan server:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://10.91.188.45

# Database SQLite lokal server (bukan path laptop developer)
DB_CONNECTION=sqlite
DB_DATABASE=C:\simak-hukum\database\database.sqlite

# Gunakan driver file agar sesi dan cache tidak membebani SQLite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Setelah menyimpan `.env`, jalankan pembersihan cache di terminal:
```bash
cd /c/simak-hukum
touch database/database.sqlite
php artisan optimize:clear
php artisan config:cache
```

### Langkah 6: Konfigurasi `php.ini` & Restart Apache
1. Buka `C:\xampp\php\php.ini`:
   - Nonaktifkan warning dotnet: beri tanda titik koma pada baris `;extension=dotnet`.
   - Sesuaikan batas eksekusi:
     ```ini
     max_execution_time = 300
     max_input_time = 300
     memory_limit = 512M
     ```
2. Restart Apache melalui PowerShell:
   ```powershell
   & "C:\xampp\apache\bin\httpd.exe" -k restart
   ```

---

## 5. Pemeriksaan Kesehatan (Health Check Routine)

Untuk memantau performa VM secara berkala, gunakan perintah PowerShell berikut:

```powershell
# Cek ketersediaan RAM bebas (dalam GB)
Get-CimInstance Win32_OperatingSystem | Select-Object `
    @{Name="TotalRAM_GB";Expression={[math]::round($_.TotalVisibleMemorySize/1MB,2)}}, `
    @{Name="FreeRAM_GB";Expression={[math]::round($_.FreePhysicalMemory/1MB,2)}}

# Cek apakah ada proses Word yang tertahan di background
Get-Process -Name "WINWORD" -ErrorAction SilentlyContinue | Select-Object Id, ProcessName, WorkingSet64, StartTime

# Cek status service Apache & MySQL
Get-Service -Name "Apache*", "*mysql*", "WSearch"
```

---

## 6. Rekomendasi Jangka Panjang

Sesuai dokumen arsitektur [`docs/windows-document-service.md`](windows-document-service.md), otomatisasi Microsoft Word COM di lingkungan server produksi memiliki risiko stabilitas yang tinggi. 

Rekomendasi pengembangan berikutnya:
1. **Migrasi Konversi ke LibreOffice Headless**:
   Pasang LibreOffice di VM, lalu tambahkan konfigurasi di `.env`:
   ```env
   SK_ALLOW_LIBREOFFICE_FALLBACK=true
   SK_OFFICE_BINARY="C:\Program Files\LibreOffice\program\soffice.exe"
   ```
   LibreOffice headless bekerja sepenuhnya tanpa GUI/dialog, lebih hemat memori, dan tidak memicu deadlock pada sesi background.
2. **Pemisahan Worker Service (Opsional)**:
   Jika beban pembuatan dokumen meningkat, pisahkan proses dokumen ke service worker terpisah sesuai kontrak OpenAPI di `docs/openapi/`.
