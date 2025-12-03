<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/classes.php';

// RESET HARIAN (jika tanggal tersimpan bukan hari ini, hapus flag)
$tgl_hari_ini = date('Y-m-d');
if (!empty($_SESSION['last_absen_date']) && $_SESSION['last_absen_date'] !== $tgl_hari_ini) {
    unset($_SESSION['sudah_absen_once']);
    unset($_SESSION['last_absen_date']);
}

// HANYA UNTUK POST: cek apakah perangkat SUDAH absen BERHASIL hari ini
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_SESSION['sudah_absen_once']) && $_SESSION['sudah_absen_once'] === true) {
        // perangkat sudah absen (tampilkan halaman pemberitahuan)
        $popup = __DIR__ . '/popup_sudah_absen.php';
        if (is_readable($popup)) {
            include $popup;
        } else {
            // fallback: set flash message and redirect
            $_SESSION['flash_error'] = 'Anda sudah absen hari ini.';
            header('Location: index.php');
        }
        exit;
    }
} 
else {
    header('Location: index.php');
    exit;
}

// AMBIL INPUT
$nama   = clean($_POST['nama'] ?? '');
$nim    = clean($_POST['nim'] ?? '');
$lat    = floatval($_POST['lat'] ?? 0);
$lon    = floatval($_POST['lon'] ?? 0);
$radius = intval($_POST['radius'] ?? 50);

// VALIDASI
$errors = [];
if ($nama === '') $errors[] = 'Nama harus diisi.';
if ($nim === '')  $errors[] = 'NIM harus diisi.';
if ($lat === 0 && $lon === 0) $errors[] = 'Lokasi tidak ditemukan.';
if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    header('Location: index.php');
    exit;
}

// HITUNG JARAK
$lokasiKelas = new LokasiKelas(-7.051333, 110.440055, $radius);
$lokasiMhs   = new LokasiMahasiswa($lat, $lon);
$jarak       = HitungJarak::haversine($lokasiKelas, $lokasiMhs);

$inside = ($jarak <= $radius);
$status = $inside ?
    "Berhasil absen (jarak " . intval($jarak) . " m)" :
    "Gagal: terlalu jauh (jarak " . intval($jarak) . " m)";

    // HITUNG JARAK
$lokasiKelas = new LokasiKelas(-7.051333, 110.440055, $radius);
$lokasiMhs   = new LokasiMahasiswa($lat, $lon);
$jarak       = HitungJarak::haversine($lokasiKelas, $lokasiMhs);

$inside = ($jarak <= $radius);
$status = $inside ?
    "Berhasil absen (jarak " . intval($jarak) . " m)" :
    "Gagal: terlalu jauh (jarak " . intval($jarak) . " m)";

// SIMPAN RIWAYAT (GAGAL / BERHASIL)
$queue = new AbsensiQueue();
$queue->enqueue([
    'nama'   => $nama,
    'nim'    => $nim,
    'lat'    => $lat,
    'lon'    => $lon,
    'jarak'  => intval($jarak),
    'inside' => $inside,
    'status' => $status,
    'waktu'  => date('Y-m-d H:i:s')
]);

//  Popup jika gagal GPS
if(!$inside) {
    $_SESSION['flash_error'] = "Gagal absen! Jarak anda " . intval($jarak) . "m → di luar radius!";
    header('Location: index.php');
    exit;
}
// HANYA JIKA BERHASIL → KUNCI SAMPAI BESOK
if ($inside) {
    $_SESSION['sudah_absen_once'] = true;
    $_SESSION['last_absen_date']  = date('Y-m-d');  // simpan tanggal hari ini
}

// SELESAI -> redirect balik ke index
header("Location: index.php");
exit;
