# ⚡ Sistem Informasi Pemesanan Instalasi Listrik PT. Eksavindo Mitra Mandiri Berbasis Web

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![Laravel-Ready](https://img.shields.io/badge/Architecture-Laravel%20%26%20MVC-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-3NF%20Compliant-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Midtrans](https://img.shields.io/badge/Midtrans-GoPay%20QRIS%20Gateway-002B49?style=for-the-badge)](https://midtrans.com)
[![PUIL 2020](https://img.shields.io/badge/Standard-PUIL%202020%20%26%20SNI-f59e0b?style=for-the-badge)](#)
[![Status](https://img.shields.io/badge/Status-Completed%20%26%20Tested-success?style=for-the-badge)](#)

> **Tugas Akhir / Laporan Proyek Sistem Informasi**  
> **Program Studi:** S1 Ilmu Komputer, Fakultas Ilmu Komputer  
> **Perguruan Tinggi:** Universitas Muhammadiyah Metro  
> **Penyusun:** Handika Saputra (NPM: 24430011)  
> **Tahun Akademik:** 2026 / 2027  
> **Instansi Mitra:** PT. Eksavindo Mitra Mandiri (Kota Metro, Lampung)  

---

## 📑 Daftar Isi
1. [Ringkasan Proyek](#-ringkasan-proyek)
2. [Fitur Utama Sistem](#-fitur-utama-sistem)
3. [Perancangan Sistem & Diagram](#-perancangan-sistem--diagram)
   - [1. Flowchart Aliran Sistem Informasi (ASI) Swimlane](#1-flowchart-aliran-sistem-informasi-asi-swimlane-3-jalur)
   - [2. Activity Flowchart Siklus Pemesanan & Pembayaran QRIS End-to-End](#2-activity-flowchart-siklus-pemesanan--pembayaran-qris-end-to-end)
   - [3. Data Flow Diagram (DFD) Level 0 / Diagram Konteks](#3-data-flow-diagram-dfd-level-0--diagram-konteks)
   - [4. Data Flow Diagram (DFD) Level 1 (13 Proses)](#4-data-flow-diagram-dfd-level-1-13-proses)
   - [5. Use Case Diagram UML](#5-use-case-diagram-uml)
   - [6. Entity Relationship Diagram (ERD) Normalisasi 3NF](#6-entity-relationship-diagram-erd-normalisasi-3nf)
   - [7. Rancangan Relasi Antar Tabel](#7-rancangan-relasi-antar-tabel)
4. [Tangkapan Layar Antarmuka Aplikasi (Screenshots)](#-tangkapan-layar-antarmuka-aplikasi-screenshots)
   - [1. Halaman Masuk / Login Pengguna](#1-halaman-masuk--login-pengguna)
   - [2. Katalog Layanan dengan AJAX Live Search](#2-katalog-layanan-dengan-ajax-live-search)
   - [3. Detail Layanan Instalasi](#3-detail-layanan-instalasi)
   - [4. Keranjang Belanja Pemesanan](#4-keranjang-belanja-pemesanan)
   - [5. Form Checkout Multi-Alamat](#5-form-checkout-multi-alamat)
   - [6. Pembayaran GoPay QRIS Standar Nasional (Zero Manual Input)](#6-pembayaran-gopay-qris-standar-nasional-zero-manual-input)
   - [7. Kuitansi / Invoice Digital Resmi](#7-kuitansi--invoice-digital-resmi)
   - [8. Dashboard Administrator](#8-dashboard-administrator)
   - [9. Panel Manajemen Pesanan & Penjadwalan Teknisi (Administrator)](#9-panel-manajemen-pesanan--penjadwalan-teknisi-administrator)
   - [10. Panel Kelola Layanan & Produk Instalasi (Administrator)](#10-panel-kelola-layanan--produk-instalasi-administrator)
5. [Spesifikasi Teknologi](#-spesifikasi-teknologi)
6. [Struktur Direktori Proyek](#-struktur-direktori-proyek)
7. [Panduan Instalasi & Menjalankan Proyek](#-panduan-instalasi--menjalankan-proyek)
8. [Akun Pengujian Demo](#-akun-pengujian-demo)

---

## 📌 Ringkasan Proyek
**PT. Eksavindo Mitra Mandiri** merupakan badan usaha penyedia layanan instalasi, perawatan kelistrikan, serta proteksi petir di Kota Metro, Lampung. Dalam proses bisnis konvensional, pemesanan jasa listrik masih dilakukan manual melalui sambungan telepon atau kunjungan kantor. Hal ini menimbulkan berbagai kendala operasional, seperti pencatatan pesanan yang tercecer, estimasi spesifikasi teknis dan biaya paket yang kurang transparan bagi konsumen, verifikasi transfer bank yang memakan waktu, serta jadwal armada teknisi yang sering tumpang tindih.

Proyek ini menghadirkan **Sistem Informasi Pemesanan Instalasi Listrik Berbasis Web** yang mengintegrasikan:
1. **Katalog Digital Terstandar PUIL 2020 & SNI** (spesifikasi daya VA, fase 1-phase/3-phase, kelengkapan material kabel, serta kuota tim).
2. **Online Payment Gateway Midtrans (GoPay QRIS & Virtual Account)** dengan sistem nominal terkunci otomatis (*zero manual input*).
3. **Automated Dispatch & Scheduling Engine** yang secara otomatis memvalidasi status pesanan menjadi *Lunas/Diproses*, menugaskan armada teknisi, menetapkan tanggal pemasangan H+1, dan menerbitkan kuitansi digital sah bertanda verifikasi.

---

## ✨ Fitur Utama Sistem

### 👤 Modul Konsumen (Pelanggan)
- **Registrasi & Autentikasi Pengguna:** Pembuatan akun otomatis dengan format ID kustom berurutan (`USR00001`, `USR00002`, dst.).
- **Katalog Layanan Interaktif & AJAX Live Search:** Pencarian realtime tanpa reload berdasarkan nama paket, kategori layanan, daya, atau fase.
- **Detail Paket & Spesifikasi PUIL:** Informasi transparan mengenai daya VA, jenis instalasi (Rumah Tinggal, Komersil 3 Phase, Grounding Petir, Perawatan Panel), harga normal, dan potongan diskon promo aktif.
- **Keranjang Pemesanan Dinamis:** Penyesuaian jumlah unit paket dengan rekalkulasi subtotal secara instan.
- **Checkout Multi-Alamat:** Konsumen dapat memilih alamat tersimpan atau menambahkan alamat lokasi pemasangan baru secara langsung.
- **Metode Pembayaran Ganda:**
  - **Online Payment Gateway (GoPay QRIS):** QR Code Standar Pembayaran Nasional GPN tercetak otomatis sesuai total tagihan; verifikasi instan tanpa input manual.
  - **Transfer Rekening Bank Manual:** Dilengkapi opsi unggah foto bukti transfer bagi pelanggan konvensional.
- **Riwayat Pesanan & Pelacakan Armada:** Pemantauan status pengerjaan (*Menunggu Penjadwalan* $\rightarrow$ *Teknisi Ditugaskan* $\rightarrow$ *Menuju Lokasi* $\rightarrow$ *Selesai*).
- **Cetak Kuitansi / Invoice Digital:** Format kuitansi resmi siap cetak (dilengkapi stempel LUNAS / VERIFIED untuk arsip transaksi).

### 🛠️ Modul Administrator
- **Dashboard Statistik Operasional:** Monitoring metrik total pesanan, jumlah verifikasi tertunda, pekerjaan selesai, dan akumulasi omset terverifikasi.
- **Manajemen Pesanan & Penjadwalan Cerdas:** Verifikasi transaksi, pengubahan status pengerjaan, dan tombol satu klik *"Jadwalkan Otomatis (Besok)"*.
- **Live Search Pesanan AJAX:** Filter instan tabel pesanan berdasarkan ID pesanan, nama pemesan, nomor telepon, alamat, atau paket layanan.
- **CRUD Master Paket Layanan / Produk:** Pengaturan spesifikasi daya VA, fase, harga paket, deskripsi kabel SNI, dan kuota teknisi.
- **CRUD Kategori Layanan:** Pengelompokan jenis pekerjaan instalasi kelistrikan.
- **CRUD Rekening Bank Resmi:** Pengaturan nomor rekening bank transfer PT. Eksavindo.
- **CRUD Diskon Promo:** Pengaturan diskon nominal aktif yang otomatis memotong tagihan konsumen saat checkout.

---

## 📐 Perancangan Sistem & Diagram

### 1. Flowchart Aliran Sistem Informasi (ASI) Swimlane (3 Jalur)

<p align="center">
  <img src="diagram-perancangan/1_flowchart_asi_swimlane.jpg" alt="Flowchart Aliran Sistem Informasi ASI Swimlane" width="850">
</p>

```mermaid
flowchart TD
    subgraph KONSUMEN ["Konsumen (Customer)"]
        K1([Mulai]) --> K2[Buka Web & Login / Registrasi]
        K2 --> K3[Pilih Paket Instalasi Listrik]
        K3 --> K4[Masukkan ke Keranjang]
        K4 --> K5[Isi Alamat Pemasangan di Checkout]
        K5 --> K6[Pilih Metode Pembayaran]
        K6 --> K7{Metode Pembayaran?}
        K7 -- "GoPay QRIS / Gateway" --> K8[Scan QRIS Midtrans Tanpa Input Manual]
        K7 -- "Transfer Manual" --> K9[Transfer ke Rekening Bank & Unggah Bukti]
        K8 --> K10[Konfirmasi Selesai Bayar]
        K9 --> K11[Menunggu Verifikasi Admin]
        K10 --> K12[Menerima Jadwal Teknisi Otomatis]
        K13[Menerima Kedatangan Tim Teknisi] --> K14[Menerima Hasil Instalasi & Kuitansi Lunas]
        K14 --> K15([Selesai])
    end

    subgraph SYSTEM ["Sistem Web & Payment Gateway"]
        K8 -.-> S1[Midtrans Memvalidasi Pembayaran QRIS]
        S1 --> S2[Sistem Otomatis Set Status: LUNAS]
        S2 --> S3[Auto-Dispatch: Jadwalkan H+1 & Tugaskan Teknisi]
        S3 --> K12
        K9 -.-> S4[Simpan Data Pesanan & File Struk Transfer]
        S4 --> A1
        A3 --> S5[Update Status Pesanan & Jadwal Teknisi]
        S5 --> K12
        S3 --> S6[Terbitkan Kuitansi Digital Resmi]
        S5 --> S6
        S6 --> K14
    end

    subgraph ADMIN ["Administrator & Teknisi"]
        A1[Menerima Notifikasi Pesanan Baru] --> A2{Perlu Verifikasi Manual?}
        A2 -- Ya --> A3[Verifikasi Bukti Transfer & Tentukan Jadwal]
        A2 -- Tidak (QRIS Otomatis) --> A4[Review Penjadwalan Otomatis Sistem]
        A4 --> A5[Tugaskan Armada Teknisi Berangkat]
        A3 --> A5
        A5 --> A6[Teknisi Melaksanakan Instalasi Sesuai PUIL]
        A6 --> A7[Admin Set Status: Pemasangan Selesai]
        A7 --> K13
    end
```

---

### 2. Activity Flowchart Siklus Pemesanan & Pembayaran QRIS End-to-End

<p align="center">
  <img src="diagram-perancangan/2_activity_flowchart_qris.jpg" alt="Activity Flowchart Siklus Pemesanan & Pembayaran QRIS End-to-End" width="850">
</p>

```mermaid
flowchart TD
    Start([Mulai Transaksi]) --> Login[Konsumen Login ke Sistem]
    Login --> Katalog[Buka Katalog & Cari Paket via AJAX Live Search]
    Katalog --> AddCart[Tambah Paket ke Keranjang]
    AddCart --> ViewCart[Periksa Rincian Biaya di Keranjang]
    ViewCart --> Checkout[Masuk ke Halaman Checkout]
    Checkout --> Address[Tentukan Alamat Lokasi Pemasangan]
    Address --> PaymentChoice{Pilih Metode Bayar}

    PaymentChoice -- "GoPay QRIS Gateway" --> QRISView[Tampilkan Kartu Standar QRIS GPN]
    QRISView --> QRISLock[Sistem Mengunci Nominal Pas - Tanpa Input Manual]
    QRISLock --> UserScan[Konsumen Scan QRIS lewat Mobile Banking / E-Wallet]
    UserScan --> GatewayVerify[Midtrans Gateway Verifikasi Transaksi]
    GatewayVerify --> AutoPaid[Sistem Set: Status LUNAS & Pesanan DIPROSES]
    AutoPaid --> AutoSchedule[Auto-Schedule: Penetapan Jadwal H+1 Pukul 09.00 WIB]
    AutoSchedule --> AutoDispatch[Auto-Assign: Tim Armada 1 Teknisi Tersertifikasi]

    PaymentChoice -- "Transfer Rekening Manual" --> ManualBank[Konsumen Transfer ke Bank Mandiri / BCA]
    ManualBank --> UploadReceipt[Unggah Foto Bukti Transfer]
    UploadReceipt --> AdminReview[Admin Verifikasi Mutasi Rekening]
    AdminReview --> AdminApprove[Admin Menyetujui & Menentukan Jadwal Manual]

    AutoDispatch --> InvoiceReady[Penerbitan Kuitansi Digital Resmi Berstempel LUNAS]
    AdminApprove --> InvoiceReady
    InvoiceReady --> TechExec[Teknisi Melakukan Pemasangan di Lokasi]
    TechExec --> Done([Transaksi Selesai & Bergaransi 30 Hari])
```

---

### 3. Data Flow Diagram (DFD) Level 0 / Diagram Konteks

<p align="center">
  <img src="diagram-perancangan/3_dfd_level_0_konteks.jpg" alt="Data Flow Diagram DFD Level 0 Diagram Konteks" width="850">
</p>

```mermaid
flowchart LR
    KONSUMEN((KONSUMEN))
    ADMIN((ADMINISTRATOR))
    GATEWAY((MIDTRANS GATEWAY))
    
    SYSTEM[("0.0<br/>SISTEM INFORMASI PEMESANAN<br/>INSTALASI LISTRIK<br/>PT. EKSAVINDO MITRA MANDIRI")]

    KONSUMEN -- "Data Registrasi, Akun Login,<br/>Data Alamat Pasang, Data Keranjang,<br/>Pilihan Pembayaran, Bukti Bayar Manual" --> SYSTEM
    SYSTEM -- "Info Katalog Paket Listrik, Rincian Keranjang,<br/>Tampilan GoPay QRIS Pas, Notifikasi Status,<br/>Jadwal Teknisi, Kuitansi / Invoice Digital" --> KONSUMEN

    SYSTEM -- "Request Transaksi QRIS, Nominal Pas, Order ID" --> GATEWAY
    GATEWAY -- "Callback Notifikasi Sukses, Status Settlement" --> SYSTEM

    ADMIN -- "Master Kategori, Master Produk/Paket Listrik,<br/>Master Rekening Bank, Master Diskon Promo,<br/>Konfirmasi Jadwal, Penugasan Armada" --> SYSTEM
    SYSTEM -- "Laporan Pesanan Masuk, Rekap Omset Penjualan,<br/>Data Konsumen, Log Pembayaran Gateway" --> ADMIN
```

---

### 4. Data Flow Diagram (DFD) Level 1 (13 Proses)

<p align="center">
  <img src="diagram-perancangan/4_dfd_level_1.jpg" alt="Data Flow Diagram DFD Level 1" width="850">
</p>

```mermaid
flowchart TD
    Admin((Admin))
    Konsumen((Konsumen))

    subgraph DataStore ["Tabel Database (MySQL)"]
        D1[(Tabel Kategori)]
        D2[(Tabel Produk)]
        D3[(Tabel Diskon)]
        D4[(Tabel Bank)]
        D5[(Tabel User)]
        D6[(Tabel Alamat)]
        D7[(Tabel Keranjang)]
        D8[(Tabel Pesanan)]
        D9[(Tabel Detail Pesanan)]
        D10[(Tabel Transaksi Gateway)]
    end

    Admin --> P1["1. Kelola Kategori"] --> D1
    Admin --> P2["2. Kelola Produk Layanan"] --> D2
    Admin --> P3["3. Kelola Diskon Promo"] --> D3
    Admin --> P4["4. Kelola Rekening Bank"] --> D4

    Konsumen --> P5["5. Registrasi & Akun"] --> D5
    Konsumen --> P6["6. Autentikasi Login"] <--> D5
    Konsumen --> P7["7. Kelola Alamat Pasang"] --> D6
    Konsumen --> P8["8. Kelola Keranjang Belanja"] <--> D7

    D2 --> P9["9. Tampilkan Katalog & Live Search"] --> Konsumen
    D3 --> P9

    Konsumen --> P10["10. Proses Checkout Pemesanan"]
    P10 --> D8
    P10 --> D9

    P10 --> P11["11. Pembayaran Online QRIS / Gateway"]
    P11 <--> D10
    P11 --> D8

    D8 --> P12["12. Penjadwalan & Armada Teknisi"]
    Admin --> P12
    P12 --> Konsumen

    D8 --> P13["13. Pembuatan Kuitansi & Laporan"]
    D9 --> P13
    P13 --> Konsumen
    P13 --> Admin
```

---

### 5. Use Case Diagram UML

<p align="center">
  <img src="diagram-perancangan/5_use_case_diagram.jpg" alt="Use Case Diagram UML" width="850">
</p>

```mermaid
flowchart LR
    subgraph Aktor
        K[Konsumen]
        A[Administrator]
        G[Midtrans Gateway]
    end

    subgraph UseCases ["Use Case Sistem Pemesanan Instalasi Listrik"]
        UC1([UC-01: Registrasi Akun])
        UC2([UC-02: Login Multi-Role])
        UC3([UC-03: Pencarian Layanan AJAX Live Search])
        UC4([UC-04: Kelola Keranjang Pemesanan])
        UC5([UC-05: Pengaturan Multi-Alamat])
        UC6([UC-06: Checkout Pemesanan])
        UC7([UC-07: Pembayaran Otomatis GoPay QRIS])
        UC8([UC-08: Unggah Bukti Transfer Manual])
        UC9([UC-09: Pelacakan Status & Jadwal Teknisi])
        UC10([UC-10: Cetak Kuitansi / Invoice Digital])
        UC11([UC-11: Monitoring Dashboard Operasional])
        UC12([UC-12: Verifikasi & Penjadwalan Teknisi])
        UC13([UC-13: CRUD Paket Layanan & Daya Listrik])
        UC14([UC-14: CRUD Kategori & Diskon Promo])
        UC15([UC-15: CRUD Rekening Bank Transfer])
    end

    K --> UC1
    K --> UC2
    K --> UC3
    K --> UC4
    K --> UC5
    K --> UC6
    K --> UC7
    K --> UC8
    K --> UC9
    K --> UC10

    G --> UC7

    A --> UC2
    A --> UC11
    A --> UC12
    A --> UC13
    A --> UC14
    A --> UC15
    A --> UC10
```

---

### 6. Entity Relationship Diagram (ERD) Normalisasi 3NF

<p align="center">
  <img src="diagram-perancangan/6_erd_normalisasi_3nf.jpg" alt="Entity Relationship Diagram Normalisasi 3NF" width="850">
</p>

Rancangan database telah memenuhi standardisasi normalisasi **1NF, 2NF, hingga 3NF**:
- **1NF:** Setiap atribut bersifat atomik, tanpa *repeating groups*. Item belanja pada transaksi dipisahkan ke tabel `detail_pesanan`.
- **2NF:** Seluruh atribut non-key bergantung penuh terhadap Primary Key masing-masing.
- **3NF:** Ketergantungan transitif dieliminasi. Diskon, kategori, alamat pasang, dan log transaksi gateway dipisahkan ke entitas terisolasi.

```mermaid
erDiagram
    ROLE ||--o{ USER : memiliki
    USER ||--o{ ALAMAT : memiliki
    USER ||--o{ KERANJANG : menyimpan
    USER ||--o{ FAVORIT : menyimpan
    USER ||--o{ PESANAN : membuat
    
    KATEGORI ||--o{ PRODUK : mengelompokkan
    PRODUK ||--o{ DISKON : memiliki
    PRODUK ||--o{ KERANJANG : tercatat
    PRODUK ||--o{ FAVORIT : tercatat
    PRODUK ||--o{ DETAIL_PESANAN : tercantum
    
    ALAMAT ||--o{ PESANAN : ditujukan
    BANK ||--o{ PESANAN : ditransfer
    
    PESANAN ||--|{ DETAIL_PESANAN : terdiri_dari
    PESANAN ||--o| TRANSAKSI_GATEWAY : terhubung

    ROLE {
        int id_role PK
        string nama_role
        string deskripsi
    }

    USER {
        char(8) id_user PK
        int id_role FK
        string username
        string email UK
        string password
        string nomor_telp
        date tanggal_lahir
        string foto_profil
        boolean is_active
    }

    ALAMAT {
        int id_alamat PK
        char(8) id_user FK
        string alamat
        string penerima
        string telepon_penerima
    }

    KATEGORI {
        int id_kategori PK
        string nama_kategori
    }

    PRODUK {
        int id_produk PK
        int id_kategori FK
        string nama_produk
        string label_produk
        string ukuran
        string daya
        text deskripsi_produk
        int harga
        int stok
        int terjual
    }

    DISKON {
        int id_diskon PK
        int id_produk FK
        int besar_diskon
        boolean is_active
    }

    KERANJANG {
        int id_keranjang PK
        char(8) id_user FK
        int id_produk FK
        string ukuran
        int jml_beli
    }

    FAVORIT {
        int id_favorit PK
        char(8) id_user FK
        int id_produk FK
    }

    BANK {
        int id_bank PK
        string nama_bank
        string norek
        string atas_nama
    }

    PESANAN {
        varchar(50) id_pesanan PK
        char(8) id_user FK
        int id_alamat FK
        int id_bank FK
        int total_bayar
        string kurir
        string jadwal_pemasangan
        tinyint status_keberangkatan
        enum metode_bayar
        string bukti_transfer
        tinyint status_pembayaran
        tinyint status_pemesanan
        string snap_token
    }

    DETAIL_PESANAN {
        bigint id_detail_pesanan PK
        varchar(50) id_pesanan FK
        int id_produk FK
        string ukuran_produk
        int jml_beli
        int harga_satuan
        int subtotal
    }

    TRANSAKSI_GATEWAY {
        bigint id_transaksi_gateway PK
        varchar(50) id_pesanan FK
        varchar(64) order_id UK
        int gross_amount
        string snap_token
        string payment_type
        string transaction_status
        string fraud_status
        datetime settlement_time
        json raw_response
    }
```

---

### 7. Rancangan Relasi Antar Tabel

<p align="center">
  <img src="diagram-perancangan/7_rancangan_relasi_antar_tabel.jpg" alt="Rancangan Relasi Antar Tabel Database" width="850">
</p>

| Tabel | Primary Key (Eksplisit) | Foreign Key | Kardinalitas | Penjelasan Integritas Referensial |
|---|---|---|---|---|
| `role` | `id_role` | - | 1 : N | Mendefinisikan hak akses admin dan konsumen |
| `user` | `id_user` | `id_role` | 1 : N | Akun pengguna utama (`ADM00001`, `USR00001`) |
| `alamat` | `id_alamat` | `id_user` | 1 : N | Satu konsumen dapat menyimpan banyak titik lokasi pemasangan |
| `kategori` | `id_kategori` | - | 1 : N | Klasifikasi kategori instalasi (Rumah, Gedung, Panel, Grounding) |
| `produk` | `id_produk` | `id_kategori` | 1 : N | Master data paket layanan, daya VA, fase, dan material kabel SNI |
| `diskon` | `id_diskon` | `id_produk` | 1 : 1 | Potongan harga promo yang langsung memotong tagihan |
| `keranjang` | `id_keranjang` | `id_user`, `id_produk` | N : M | Menampung sementara pilihan layanan konsumen sebelum checkout |
| `favorit` | `id_favorit` | `id_user`, `id_produk` | N : M | Fitur wishlist paket layanan kelistrikan |
| `bank` | `id_bank` | - | 1 : N | Rekening tujuan pembayaran transfer manual kantor |
| `pesanan` | `id_pesanan` | `id_user`, `id_alamat`, `id_bank` | 1 : N | Header transaksi utama pemesanan dan jadwal teknisi |
| `detail_pesanan` | `id_detail_pesanan` | `id_pesanan`, `id_produk` | 1 : N | Itemized order yang menjamin normalisasi 1NF & 2NF terpenuhi |
| `transaksi_gateway` | `id_transaksi_gateway` | `id_pesanan` | 1 : 1 | Menampung audit trail transaksi online Midtrans & Webhook |

---

## 📸 Tangkapan Layar Antarmuka Aplikasi (Screenshots)

Berikut adalah dokumentasi tangkapan layar antarmuka sistem yang telah selesai diimplementasikan:

### 1. Halaman Masuk / Login Pengguna
Menampilkan form autentikasi kredensial pengguna yang aman dengan pemisahan hak akses antara Konsumen dan Administrator.
*(Akses: `index.php?page=login`)*

<p align="center">
  <img src="screenshot/01_login_pengguna.png" alt="Halaman Login Pengguna" width="850">
</p>

---

### 2. Katalog Layanan dengan AJAX Live Search
Menampilkan seluruh paket instalasi listrik dengan fitur pencarian instan (tanpa reload halaman). Hasil pencarian menyaring nama paket, kapasitas daya VA, dan kategori secara realtime dengan teknik *debounce*.
*(Akses: `index.php?page=katalog`)*

<p align="center">
  <img src="screenshot/02_katalog_layanan_instalasi.jpg" alt="Katalog Layanan & Live Search AJAX" width="850">
</p>

---

### 3. Detail Layanan Instalasi
Menampilkan rincian teknis lengkap (fase listrik, rentang daya VA, standar PUIL 2020 & SNI, garansi resmi 30 hari) serta input kuantitas pemesanan.
*(Akses: `index.php?page=detail_produk&id=...`)*

<p align="center">
  <img src="screenshot/03_detail_paket_layanan.jpg" alt="Detail Paket Layanan Instalasi" width="850">
</p>

---

### 4. Keranjang Belanja Pemesanan
Menampilkan daftar item pesanan, kalkulasi otomatis subtotal item, potongan diskon promo aktif, rincian biaya pengiriman teknisi gratis area Metro, dan tombol navigasi menuju proses checkout.
*(Akses: `index.php?page=keranjang`)*

<p align="center">
  <img src="screenshot/04_keranjang_pemesanan.png" alt="Keranjang Pemesanan Dinamis" width="850">
</p>

---

### 5. Form Checkout Multi-Alamat
Fitur fleksibel penentuan titik lokasi pengerjaan instalasi listrik: pelanggan dapat memilih alamat tersimpan atau menambahkan alamat pemasangan baru dengan tipe bangunan (Rumah / Komersial / Industri), kode pos, kota, nomor WhatsApp, serta patokan khusus teknisi sebelum menyelesaikan pemesanan.
*(Akses: `index.php?page=checkout`)*

<p align="center">
  <img src="screenshot/05_form_checkout_multi_alamat.png" alt="Form Checkout Multi-Alamat" width="850">
</p>

---

### 6. Pembayaran GoPay QRIS Standar Nasional (Zero Manual Input)
Tampilan kartu standar nasional QRIS GPN terintegrasi Payment Gateway Midtrans. Nominal tagihan terkunci otomatis dari sistem sesuai total tagihan pesanan (`ORD-20261117-8E4E7`), sehingga pelanggan cukup scan barcode tanpa perlu input harga manual di aplikasi e-wallet.
*(Akses: `index.php?page=bayar_gateway&id=...`)*

<p align="center">
  <img src="screenshot/06_pembayaran_qris_midtrans.png" alt="Pembayaran GoPay QRIS Standar Nasional" width="850">
</p>

---

### 7. Kuitansi / Invoice Digital Resmi
Dokumen tagihan dan bukti pembayaran sah lengkap dengan stempel status **LUNAS / VERIFIED**, nomor pesanan, rincian biaya, jadwal kunjungan, penugasan armada teknisi bersertifikat PT. Eksavindo, serta tombol cetak / simpan langsung (*Print to PDF*).
*(Akses: `index.php?page=kuitansi&id=...`)*

<p align="center">
  <img src="screenshot/07_kuitansi_invoice_digital.png" alt="Kuitansi / Invoice Digital Resmi" width="850">
</p>

---

### 8. Dashboard Administrator
Panel ringkasan operasional yang menyajikan kartu metrik total pesanan, jumlah verifikasi tertunda, pemasangan selesai, akumulasi total omset terverifikasi (Rp 10.400.000), tautan cepat CRUD, serta tabel pemantauan pesanan masuk terbaru.
*(Akses: `index.php?page=admin_dashboard`)*

<p align="center">
  <img src="screenshot/08_dashboard_administrator.png" alt="Dashboard Administrator" width="850">
</p>

---

### 9. Panel Manajemen Pesanan & Penjadwalan Teknisi (Administrator)
Tabel kontrol admin untuk pemrosesan detail pesanan: verifikasi status pembayaran, penugasan nama armada teknisi PT. Eksavindo, penentuan jadwal kunjungan instalasi, serta pembaruan progres pengerjaan di lokasi hingga status selesai.
*(Akses: `index.php?page=admin_pesanan_detail&id=...`)*

<p align="center">
  <img src="screenshot/09_admin_proses_pesanan.jpg" alt="Panel Admin Kelola Status & Jadwal Teknisi" width="850">
</p>

---

### 10. Panel Kelola Layanan & Produk Instalasi (Administrator)
Form dan tabel kontrol CRUD master data paket instalasi kelistrikan: penambahan paket baru, pemfilteran realtime, pengaturan kategori, spesifikasi fase/daya VA, harga satuan paket, kuota tim, serta monitoring jumlah paket yang telah terjual.
*(Akses: `index.php?page=admin_produk`)*

<p align="center">
  <img src="screenshot/10_admin_kelola_produk_layanan.png" alt="Panel Kelola Layanan & Produk Instalasi" width="850">
</p>

---

## 💻 Spesifikasi Teknologi

| Komponen | Spesifikasi / Library | Keterangan |
|---|---|---|
| **Bahasa Pemrograman** | PHP 8.2 / 8.4+ | Menggunakan arsitektur MVC bersih & siap migrasi ke Laravel |
| **Framework / Core** | Native MVC & Laravel 11.x Ready | Struktur Model Eloquent, Controller, dan View Blade telah disiapkan |
| **Database Management System** | MySQL 8.0+ / MariaDB & TiDB Cloud (Serverless) | Engine InnoDB / TiKV, Normalisasi 3NF, Mendukung Cloud-Native Distributed SQL |
| **Frontend Styling** | Bootstrap 5.3.3 + Custom CSS | Tampilan modern, responsive pada layar smartphone maupun desktop |
| **Icon Library** | Bootstrap Icons v1.11.3 | Representasi simbol grafis interaktif |
| **Tipografi** | Google Fonts: Plus Jakarta Sans | Tampilan profesional berstandar antarmuka modern |
| **Payment Gateway** | Midtrans Sandbox Engine | Mendukung GoPay QRIS, BCA VA, BNI VA, Mandiri Bill, E-Wallet |
| **Asynchronous Engine** | Vanilla JavaScript Fetch API (AJAX) | Live search realtime dengan teknik *debounce* hemat request |
| **Web Server** | Apache (XAMPP) / PHP Built-in Server | Fleksibel dijalankan lokal maupun diunggah ke web hosting |

---

## 📁 Struktur Direktori Proyek

```text
c:\pemesanan-instalasi-listrik\
│
├── index.php                          # Front Controller & Routing Utama
├── JALANKAN_WEB.bat                   # Skrip Otomatisasi Start Server Port 8000
├── database.sql                       # DDL Skema Database Awal
├── schema_instalasi_listrik_gateway.sql # DDL Database 3NF + Payment Gateway
├── tidb_migration.sql                 # Skrip Migrasi Lengkap Siap Eksekusi ke TiDB Cloud
├── .env.example                       # Contoh Konfigurasi Environment (MySQL / TiDB)
├── README.md                          # Laporan Dokumentasi Proyek GitHub
│
├── config/
│   ├── database.php                   # Koneksi Database PDO MySQL
│   └── app.php                        # Helper Global, Format Rupiah, Helper ID Kustom
│
├── controllers/
│   ├── AuthController.php             # Autentikasi Pengguna & Registrasi ID USR00001
│   ├── OrderController.php            # Keranjang, Checkout, & Simulasi Bayar Otomatis
│   └── AdminController.php            # Verifikasi, Penjadwalan Teknisi, & CRUD Master Data
│
├── views/
│   ├── layouts/
│   │   ├── header.php                 # Navbar Navigasi & Indikator Cart
│   │   └── footer.php                 # Footer Resmi & Hak Cipta
│   ├── auth/
│   │   ├── login.php                  # Form Masuk Pengguna
│   │   └── register.php               # Form Registrasi Konsumen Baru
│   ├── konsumen/
│   │   ├── home.php                   # Halaman Utama (Landing Page)
│   │   ├── katalog.php                # Katalog Layanan Listrik + AJAX Search
│   │   ├── katalog_item.php           # Template Kartu Layanan (Komponen AJAX)
│   │   ├── detail_produk.php          # Spesifikasi Teknis PUIL 2020
│   │   ├── keranjang.php              # Daftar Keranjang Belanja
│   │   ├── checkout.php               # Konfirmasi Titik Pasang & Metode Bayar
│   │   ├── bayar_gateway.php          # Halaman Pembayaran GoPay QRIS Standar GPN
│   │   ├── pesanan_saya.php           # Pelacakan Pesanan & Status Teknisi
│   │   ├── kuitansi.php               # Kuitansi Digital Resmi Siap Cetak
│   │   └── profil.php                 # Manajemen Profil & Multi-Alamat
│   └── admin/
│       ├── dashboard.php              # Statistik Omset & Ringkasan Transaksi
│       ├── pesanan.php                # Kelola Pesanan + AJAX Live Search
│       ├── pesanan_detail.php         # Dispatch Armada & Penjadwalan Otomatis
│       ├── produk.php                 # CRUD Master Paket Instalasi
│       ├── kategori.php               # CRUD Master Kategori Layanan
│       ├── bank.php                   # CRUD Rekening Bank Transfer
│       └── diskon.php                 # CRUD Promo Diskon Aktif
│
├── laravel-listrik/                   # Struktur Arsitektur Laravel Lengkap (Model & Service)
│   ├── app/
│   │   ├── Models/                    # Role, User, Produk, Pesanan, TransaksiGateway, dll.
│   │   ├── Services/MidtransService.php # Service Generator Snap Token & Webhook SHA512
│   │   └── Http/Controllers/          # Controller Konsumen, Admin, Auth
│   └── routes/web.php                 # Routing Laravel RESTful
│
├── screenshot/                        # Tangkapan Layar Antarmuka Sistem Web
│   ├── 01_login_pengguna.png          # Antarmuka Login Pelanggan & Admin
│   ├── 02_katalog_layanan_instalasi.jpg # Katalog Layanan & Live Search AJAX
│   ├── 03_detail_paket_layanan.jpg    # Detail Spesifikasi Teknis PUIL 2020
│   ├── 04_keranjang_pemesanan.png     # Keranjang Belanja Dinamis
│   ├── 05_form_checkout_multi_alamat.png # Form Checkout Multi-Alamat
│   ├── 06_pembayaran_qris_midtrans.png # Pembayaran GoPay QRIS Standar GPN Midtrans
│   ├── 07_kuitansi_invoice_digital.png # Kuitansi / Invoice Digital Sah (Lunas/Verified)
│   ├── 08_dashboard_administrator.png # Dashboard Monitoring Operasional Admin
│   ├── 09_admin_proses_pesanan.jpg    # Pemrosesan Pesanan & Dispatch Teknisi
│   └── 10_admin_kelola_produk_layanan.png # Master Data Paket Layanan & Produk Listrik
│
├── diagram-perancangan/               # Diagram Arsitektur & Perancangan Sistem (Format JPG)
│   ├── 1_flowchart_asi_swimlane.jpg   # Flowchart Aliran Sistem Informasi (ASI)
│   ├── 2_activity_flowchart_qris.jpg  # Activity Flowchart Pemesanan & QRIS
│   ├── 3_dfd_level_0_konteks.jpg      # DFD Level 0 (Diagram Konteks)
│   ├── 4_dfd_level_1.jpg              # DFD Level 1 (13 Proses)
│   ├── 5_use_case_diagram.jpg         # Use Case Diagram UML
│   ├── 6_erd_normalisasi_3nf.jpg      # ERD Normalisasi 3NF
│   └── 7_rancangan_relasi_antar_tabel.jpg # Rancangan Relasi Antar Tabel
│
├── assets/                            # Asset Gambar & Banner QRIS Nasional
│   └── qris_midtrans_standar.png      # Template QR Code Standar Pembayaran Nasional GPN
│
└── uploads/                           # Direktori Berkas Dinamis
    ├── bukti/                         # Arsip Unggahan Bukti Transfer Manual
    ├── produk/                        # Foto Dokumentasi Pekerjaan Listrik
    └── profil/                        # Foto Avatar Pengguna
```

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### Persyaratan Lingkungan (Prerequisites)
1. **PHP:** Versi 8.2 atau lebih baru (ekstensi `pdo_mysql`, `curl`, `mbstring`, `fileinfo` aktif).
2. **Database:** MySQL 8.0+ atau MariaDB (tersedia di XAMPP).
3. **Web Browser:** Google Chrome, Mozilla Firefox, atau Microsoft Edge versi terbaru.

### Langkah-Langkah Menjalankan:
1. **Kloning / Unduh Repositori:**
   ```bash
   git clone https://github.com/USERNAME/pemesanan-instalasi-listrik.git
   cd pemesanan-instalasi-listrik
   ```
2. **Nyalakan Layanan MySQL:**
   Buka **XAMPP Control Panel**, lalu klik **Start** pada modul **MySQL**.
3. **Impor Database:**
   Buat database bernama `pemesanan_instalasi_listrik` di phpMyAdmin, kemudian impor berkas:
   - File skrip: [`database.sql`](file:///c:/pemesanan-instalasi-listrik/database.sql) atau [`schema_instalasi_listrik_gateway.sql`](file:///c:/pemesanan-instalasi-listrik/schema_instalasi_listrik_gateway.sql).
   *(Atau jalankan skrip `php setup_db.php` di terminal).*
4. **Jalankan Aplikasi Web:**
   - **Cara 1 (Satu Klik):** Klik dua kali berkas [`JALANKAN_WEB.bat`](file:///c:/pemesanan-instalasi-listrik/JALANKAN_WEB.bat). Browser Anda akan otomatis terbuka.
   - **Cara 2 (Terminal):**
     ```bash
     php -S 0.0.0.0:8000
     ```
5. **Buka di Browser:**
   Buka alamat: **[http://localhost:8000](http://localhost:8000)**

---

## 🔑 Akun Pengujian Demo

| Peran (Role) | Email Login | Kata Sandi | Hak Akses Utama |
|---|---|---|---|
| **Administrator** | `admin@eksavindo.com` | `admin123` | Dashboard, Verifikasi Pembayaran, Penjadwalan Armada Teknisi, CRUD Paket Listrik, Kategori, Bank, & Diskon |
| **Konsumen (Demo)** | `budi@gmail.com` | `user123` | Jelajah Paket, Keranjang, Checkout, Pembayaran GoPay QRIS Otomatis, Lacak Status Teknisi, Cetak Kuitansi |
| **Konsumen Baru** | *(Dapat mendaftar akun baru secara mandiri melalui tombol "Daftar Akun")* | Bebas | Fitur penuh pelanggan dengan multi-alamat pemasangan |

---

 Profil Penyusun
Nama Mahasiswa: Handika Saputra
NPM: 24430011
Program Studi : S1 Ilmu Komputer
Perguruan Tinggi : Universitas Muhammadiyah Metro
Tahun Akademik: 2026 / 2027
Instansi Penelitian: PT. EKSAVINDO MITRA MANDIRI

© 2026 Handika Saputra (NPM: 24430011) - S1 Ilmu Komputer UM Metro
Perancangan Sistem Informasi Pemesanan Instalasi Listrik Pada PT. Eksavindo Mitra Mandiri Berbasis Website