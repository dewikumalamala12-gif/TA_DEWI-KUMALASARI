<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once "classes.php";

// tampilkan alert bila ada flash_error
if (!empty($_SESSION['flash_error'])) {
    echo '<div style="background:#ffe6e6;padding:10px;border-left:4px solid #e74c3c;margin:10px 0;">'
         .htmlspecialchars($_SESSION['flash_error']).'</div>';
    unset($_SESSION['flash_error']);
}

// cek flag supaya form tidak ditampilkan
$already = isset($_SESSION['sudah_absen_once']) && $_SESSION['sudah_absen_once'] === true;

// Lokasi kelas (tetap) 7°03'04.8"S 110°26'24.2"E 
$lokasiKelas = new LokasiKelas(-7.051333, 110.440055, 50); // radius 50 meter
$absensiQueue = new AbsensiQueue();
$daftar = $absensiQueue->getAll();

?>
<!DOCTYPE html>
<html lang="id"> 
    <style>
.popup-overlay{
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.popup-box{
    background: #0a1a2f;
    padding: 25px;
    border-radius: 14px;
    border: 2px solid #ffae00;
    box-shadow: 0px 10px 25px rgba(0,0,0,0.6);
    color: #fff;
    width: 90%;
    max-width: 380px;
    text-align: center;
}

.popup-btn{
    margin-top: 18px;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    background: #ff7a00;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    transition: .3s;
}
.popup-btn:hover{
    background: #ff9933;
    box-shadow: 0 0 12px rgba(255,165,0,0.55);
}
</style>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Absensi GPS - Teknik Komputer A (Demo)</title>
    <link rel="stylesheet" href="style.css">
</head>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const btnMengerti = document.getElementById('btnMengerti');
        const popupAlready = document.getElementById('popupAlready');
        if(btnMengerti){
            btnMengerti.addEventListener('click', () => {
                if (popupAlready){
                    popupAlready.style.display = "none";
                }
                window.location.href = "index.php";
            });
        }
    });
    </script>

<body>
    <div class="topbar">
    <img src="logo_UNDIP.png" alt="Logo UNDIP" class="logo-undip">
    <span class="topbar-title">Universitas Diponegoro</span>
</div>

    <div class="wrap">
        <!-- kiri: info & lokasi sekarang -->
        <div class="panel">
            <h1>Absensi Lokasi</h1>
            <p class="small">Area kelas (fix):</p>
            <p><strong>Lintang:</strong> <?= $lokasiKelas->lat ?><br><strong>Bujur:</strong> <?= $lokasiKelas->lon ?><br><strong>Radius valid:</strong> <?= $lokasiKelas->radius ?> meter</p>
            <a class="map-link" target="_blank" href="https://www.google.com/maps/search/?api=1&query=<?= $lokasiKelas->lat . ',' . $lokasiKelas->lon ?>">Buka di Google Maps</a>
            <hr>
            <p class="small">Lokasi Anda sekarang:</p>
            <div id="lokasi_now" class="info">Tekan tombol <em>Ambil Lokasi</em></div>
            <div style="margin-top:12px">
            <button id="btnGet" class="btn">Ambil Lokasi</button>
            </div>

            <!-- form absensi (disembunyikan sampai lokasi diambil) -->
            <form id="formAbsensi" method="POST" action="proses.php" style="display:none;margin-top:12px;">
                <input type="hidden" name="lat" id="lat" value="">
                <input type="hidden" name="lon" id="lon" value="">
                <input type="hidden" name="radius" id="radius" value="<?= $lokasiKelas->radius ?>">

                <div>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" placeholder="Nama lengkap" required class="input">
                </div>
                <div style="margin-top:8px;">
                    <label for="nim">NIM</label><br>
                    <input type="text" id="nim" name="nim" placeholder="NIM" required class="input">
                </div>

                <div style="margin-top:12px;">
                    <button id="btnSubmit" type="button" class="btn secondary" disabled>Ambil & Absen</button>
                </div>
            </form>

        </div>
        <!-- kanan: daftar peserta & instruksi -->
        <div class="panel">
            <h2>Daftar Absen Terkini</h2>
            <?php if (count($daftar) === 0): ?>
            <p class="small">Belum ada yang absen.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                        <th>#</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Lokasi</th>
                            <th>Jarak</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftar as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= clean($row['nama'] ?? '-') ?></td>
                                <td><?= clean($row['nim'] ?? '-') ?></td>
                                <td><?= ($row['lat'] ?? '-') . ' , ' . ($row['lon'] ?? '-') ?></td>
                                <td><?= intval($row['jarak']) ?> m</td>
                                <td class="<?= (isset($row['inside']) && $row['inside']) ? 'ok' : 'nok' ?>"><?= nl2br(clean($row['status'])) ?></td>
                                <td><?= htmlspecialchars($row['waktu'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <hr>
            <p class="small">Catatan:</p>
            <ul>
                <li>Form akan mengisi otomatis koordinat dari browser (izin lokasi harus diizinkan).</li>
                <li>Jika berada dalam radius <strong><?= $lokasiKelas->radius ?> m</strong> maka absen diterima.</li>
            </ul>
        </div>
    </div>

    <script>
        const btnGet = document.getElementById('btnGet');
        const btnSubmit = document.getElementById('btnSubmit');
        const lokasiNow = document.getElementById('lokasi_now');
        const latInput = document.getElementById('lat');
        const lonInput = document.getElementById('lon');
        const form = document.getElementById('formAbsensi');

        let lastPosition = null;

        btnGet.addEventListener('click', () => {
            if (!navigator.geolocation) {
                lokasiNow.innerText = 'Geolocation tidak didukung di browser ini.';
                return;
            }
            lokasiNow.innerText = 'Mengambil lokasi...';
            navigator.geolocation.getCurrentPosition(position => {
                const lat = position.coords.latitude.toFixed(6);
                const lon = position.coords.longitude.toFixed(6);
                lastPosition = {
                    lat,
                    lon
                };
                lokasiNow.innerHTML = `<strong>${lat} , ${lon}</strong><br><a target="_blank" href="https://www.google.com/maps/search/?api=1&query=${lat},${lon}">Buka di Maps</a>`;
                latInput.value = lat;
                lonInput.value = lon;
                btnSubmit.disabled = false;
                
                // tampilkan form singkat (nama/nim) agar user isi
                form.style.display = 'block';
            }, err => {
                lokasiNow.innerText = 'Gagal mendapatkan lokasi: ' + err.message;
            }, {
                enableHighAccuracy: true,
                timeout: 10000
            });
        });

        // ketika klik Ambil & Absen -> submit form (validasi sederhana)
        btnSubmit.addEventListener('click', () => {
            // pastikan lokasi sudah ada
            if (!latInput.value || !lonInput.value) {
                alert('Silakan ambil lokasi terlebih dahulu.');
                return;
            }
            // simple validation for name & nim
            const nama = document.getElementById('nama').value.trim();
            const nim = document.getElementById('nim').value.trim();
            if (!nama || !nim) {
                alert('Isi Nama dan NIM terlebih dahulu.');
                return;
            }
            form.submit();
        });
    </script>
    </div> <!-- .wrap -->
</body>
</html>