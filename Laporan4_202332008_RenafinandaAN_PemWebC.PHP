<?php
// Inisialisasi variabel buat nampung hasil akhir, warna tombol, warna header, dan pesan error (validasi)
$hasil = "";
$warnaBtn = "";
$warnaHeader = "";
$validasiPesan = "";

// Cek apakah tombol submit udah diklik
if (isset($_POST['proses'])) {
    // Ambil nilai dari form, terus bersihin dari spasi di awal/akhir
    $nama       = trim($_POST['nama']);
    $nim        = trim($_POST['nim']);
    $kehadiran  = $_POST['kehadiran'];
    $tugas      = $_POST['tugas'];
    $uts        = $_POST['uts'];
    $uas        = $_POST['uas'];

    // Validasi awal: ngecek apakah ada kolom yang belum diisi
    $kolomKosong = [];
    if ($nama == "")        $kolomKosong[] = "Nama";
    if ($nim == "")         $kolomKosong[] = "NIM";
    if ($kehadiran === "")  $kolomKosong[] = "Nilai Kehadiran";
    if ($tugas === "")      $kolomKosong[] = "Nilai Tugas";
    if ($uts === "")        $kolomKosong[] = "Nilai UTS";
    if ($uas === "")        $kolomKosong[] = "Nilai UAS";

    // Buat nampilin pesan error sesuai jumlah kolom yang kosong
    if     (count($kolomKosong) == 6) $validasiPesan = "Semua kolom harus diisi.";
    elseif (count($kolomKosong) == 1) $validasiPesan = "Kolom {$kolomKosong[0]} belum diisi.";
    elseif (count($kolomKosong) == 2) $validasiPesan = "Kolom {$kolomKosong[0]} dan {$kolomKosong[1]} belum diisi.";
    elseif (count($kolomKosong) >= 3) {
        // Misahin kolom terakhir buat pakai kata "dan"
        $last = array_pop($kolomKosong);
        $validasiPesan = "Kolom " . implode(", ", $kolomKosong) . ", dan $last belum diisi.";
    }

    // Validasi nilai: harus angka dan rentangnya antara 0 sampai 100
    if ($validasiPesan == "") {
        $nilaiTidakValid = [];
        if (!is_numeric($kehadiran) || $kehadiran < 0 || $kehadiran > 100) $nilaiTidakValid[] = "Nilai Kehadiran";
        if (!is_numeric($tugas)     || $tugas     < 0 || $tugas     > 100) $nilaiTidakValid[] = "Nilai Tugas";
        if (!is_numeric($uts)       || $uts       < 0 || $uts       > 100) $nilaiTidakValid[] = "Nilai UTS";
        if (!is_numeric($uas)       || $uas       < 0 || $uas       > 100) $nilaiTidakValid[] = "Nilai UAS";

        // Kalau ada nilai yang gak valid, tampilin pesan error
        if (count($nilaiTidakValid) > 0) {
            $validasiPesan = implode(", ", $nilaiTidakValid) . " harus di antara 0 sampai 100.";
        }
    }

    // Kalau semuanya udah valid, lanjut hitung nilai akhir dan status
    if ($validasiPesan == "") {
        // Hitung nilai akhir berdasarkan bobot
        $nilai_akhir = ($kehadiran * 0.10) + ($tugas * 0.20) + ($uts * 0.30) + ($uas * 0.40);

        // Tentuin grade berdasarkan nilai akhir
        if     ($nilai_akhir >= 85) $grade = "A";
        elseif ($nilai_akhir >= 70) $grade = "B";
        elseif ($nilai_akhir >= 55) $grade = "C";
        elseif ($nilai_akhir >= 40) $grade = "D";
        else                        $grade = "E";

        // Cek apakah mahasiswa lulus atau nggak, tergantung dari kehadiran dan nilai minimum
        if ($kehadiran < 70) {
            $status      = "TIDAK LULUS";
            $warnaHeader = "bg-danger text-white";
            $warnaBtn    = "btn-danger";
        } elseif ($nilai_akhir >= 60 && $tugas >= 40 && $uts >= 40 && $uas >= 40) {
            $status      = "LULUS";
            $warnaHeader = "bg-success text-white";
            $warnaBtn    = "btn-success";
        } else {
            $status      = "TIDAK LULUS";
            $warnaHeader = "bg-danger text-white";
            $warnaBtn    = "btn-danger";
        }

        // Tampilkan hasil akhir dalam bentuk kartu Bootstrap
        $hasil = "
        <div class='card mt-4'>
            <div class='card-header $warnaHeader'>
                <strong>Hasil Penilaian</strong>
            </div>
            <div class='card-body'>
                <div class='row mb-3 fs-5'>
                    <div class='col text-start'><strong>Nama:</strong> <span class='fw-normal'>$nama</span></div>
                    <div class='col text-end'><strong>NIM:</strong> <span class='fw-normal'>$nim</span></div>
                </div>
                <hr>
                <p><strong>Nilai Kehadiran:</strong> <span class='fw-normal'>{$kehadiran}%</span></p>
                <p><strong>Nilai Tugas:</strong>     <span class='fw-normal'>{$tugas}</span></p>
                <p><strong>Nilai UTS:</strong>       <span class='fw-normal'>{$uts}</span></p>
                <p><strong>Nilai UAS:</strong>       <span class='fw-normal'>{$uas}</span></p>
                <p><strong>Nilai Akhir:</strong>     <span class='fw-normal'>" . number_format($nilai_akhir, 2) . "</span></p>
                <p><strong>Grade:</strong>           <span class='fw-normal'>$grade</span></p>
                <p><strong>Status:</strong>          <span class='fw-normal'>$status</span></p>
                <div class='d-grid'>
                    <a href='laporan.php' class='btn $warnaBtn'>Selesai</a>
                </div>
            </div>
        </div>";
    }
}
?>

<!-- HTML untuk form input -->
<!DOCTYPE html>
<html>
<head>
    <title>Penilaian Mahasiswa_202332008_Renafinanda AN</title>
    
    <!-- Import Bootstrap biar tampilan lebih rapi dan kekinian -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        /* Semua teks di body pakai bold, kecuali di inputan */
        body { font-weight: 700; }
        .form-control { font-weight: 400; }
    </style>
</head>
<body class="container py-4">

    <!-- Kartu utama untuk form -->
    <div class="card">
        <div class="card-header bg-primary text-white text-center fs-5">
            Form Penilaian Mahasiswa
        </div>
        <div class="card-body">
            <!-- Form pengisian data -->
            <form method="POST">
                
                <!-- Input Nama -->
                <div class="mb-2">
                    <label>Masukkan Nama</label>
                    <!-- Isi otomatis kalau sebelumnya udah submit -->
                    <input type="text" name="nama" class="form-control"
                           placeholder="Renafinanda Adianti Nugroho"
                           value="<?= $_POST['nama'] ?? '' ?>">
                </div>

                <!-- Input NIM -->
                <div class="mb-2">
                    <label>Masukkan NIM</label>
                    <input type="text" name="nim" class="form-control"
                           placeholder="202332008"
                           value="<?= $_POST['nim'] ?? '' ?>">
                </div>

                <!-- Input Nilai Kehadiran -->
                <div class="mb-2">
                    <label>Nilai Kehadiran (10%)</label>
                    <!-- Dikasih placeholder biar user tahu minimal kehadiran berapa -->
                    <input type="number" name="kehadiran" class="form-control"
                           placeholder="Untuk lulus minimal 70%"
                           value="<?= $_POST['kehadiran'] ?? '' ?>">
                </div>

                <!-- Input Nilai Tugas -->
                <div class="mb-2">
                    <label>Nilai Tugas (20%)</label>
                    <input type="number" name="tugas" class="form-control"
                           placeholder="0-100"
                           value="<?= $_POST['tugas'] ?? '' ?>">
                </div>

                <!-- Input Nilai UTS -->
                <div class="mb-2">
                    <label>Nilai UTS (30%)</label>
                    <input type="number" name="uts" class="form-control"
                           placeholder="0-100"
                           value="<?= $_POST['uts'] ?? '' ?>">
                </div>

                <!-- Input Nilai UAS -->
                <div class="mb-2">
                    <label>Nilai UAS (40%)</label>
                    <input type="number" name="uas" class="form-control"
                           placeholder="0-100"
                           value="<?= $_POST['uas'] ?? '' ?>">
                </div>

                <!-- Tombol proses nilai -->
                <div class="d-grid mb-2">
                    <button type="submit" name="proses" class="btn btn-primary">Proses</button>
                </div>

                <!-- Tampilkan pesan error validasi kalau ada -->
                <?php if (isset($_POST['proses']) && $validasiPesan != ""): ?>
                    <div class="alert alert-danger"><?= $validasiPesan ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tampilkan hasil perhitungan nilai di bawah form -->
    <?= $hasil ?>

</body>
</html>

