<?php
// === Mulai Session untuk menyimpan data ===
session_start();

// === Reset Session jika parameter reset=1 ===
if (isset($_GET['reset']) && $_GET['reset'] == '1') {
    session_destroy();
    session_start();
    $_SESSION['mahasiswa'] = [];
    header("Location: index.php");
    exit();
}

// === Inisialisasi data mahasiswa jika belum ada ===
if (!isset($_SESSION['mahasiswa'])) {
    $_SESSION['mahasiswa'] = [];
}

// === Proses Form Submit (Menambah Data) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $nilai = intval($_POST['nilai']);
    
    // Validasi input
    if (!empty($nama) && $nilai >= 0 && $nilai <= 100) {
        $_SESSION['mahasiswa'][] = ['nama' => $nama, 'nilai' => $nilai];
    }
}

// === Proses Reset Data (Menghapus Semua) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
    $_SESSION['mahasiswa'] = [];
}

// === Ambil data dari session ===
$mahasiswa = $_SESSION['mahasiswa'];

// === Variabel untuk menghitung statistik ===
$jumlahLulus = 0;
$jumlahTidakLulus = 0;
$totalNilai = 0;
$nilaiTertinggi = 0;
$namaTertinggi = '';

// === Proses Menghitung Statistik ===
if (!empty($mahasiswa)) {
    foreach ($mahasiswa as $mhs) {
        $totalNilai += $mhs['nilai'];
        
        if ($mhs['nilai'] >= 60) {
            $jumlahLulus++;
        } else {
            $jumlahTidakLulus++;
        }
        
        if ($mhs['nilai'] > $nilaiTertinggi) {
            $nilaiTertinggi = $mhs['nilai'];
            $namaTertinggi = $mhs['nama'];
        }
    }
    
    $jumlahMahasiswa = count($mahasiswa);
    $rataRata = $totalNilai / $jumlahMahasiswa;
} else {
    $jumlahMahasiswa = 0;
    $rataRata = 0;
    $nilaiTertinggi = 0;
    $namaTertinggi = '-';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Nilai Mahasiswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            padding: 20px;
            color: #e0e0e0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #252525;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            padding: 30px;
            border: 1px solid #333;
        }

        h1 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 30px;
            font-size: 28px;
        }

        /* Form Input Section */
        .form-section {
            background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
        }

        .form-section h2 {
            color: #ffffff;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #b0b0b0;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #444;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            background: #1a1a1a;
            color: #e0e0e0;
        }

        .form-group input:focus {
            outline: none;
            border-color: #666;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a4a4a 0%, #5a5a5a 100%);
            color: white;
            border: 1px solid #666;
        }

        .btn-danger {
            background: linear-gradient(135deg, #3a3a3a 0%, #4a4a4a 100%);
            color: #ff6b6b;
            border: 1px solid #666;
        }

        /* Statistik Section */
        .statistik {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
            color: #e0e0e0;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
        }

        .stat-card h3 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
            color: #b0b0b0;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
        }

        .stat-card.lulus {
            background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
            border: 1px solid #4a4a4a;
        }

        .stat-card.tidak-lulus {
            background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
            border: 1px solid #4a4a4a;
        }

        /* Tabel Section */
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #2a2a2a;
        }

        thead {
            background: linear-gradient(135deg, #3a3a3a 0%, #4a4a4a 100%);
            color: #ffffff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #444;
            color: #e0e0e0;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        tbody tr:hover {
            background-color: #3a3a3a;
        }

        .status-lulus {
            background-color: #2a2a2a;
            color: #4ade80;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #4ade80;
        }

        .status-tidak-lulus {
            background-color: #2a2a2a;
            color: #ff6b6b;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #ff6b6b;
        }

        .nilai-tertinggi {
            background-color: #3a3a3a;
            font-weight: bold;
            color: #ffd700;
        }

        /* Info Nilai Tertinggi */
        .info-tertinggi {
            background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
            color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
        }

        .info-tertinggi h3 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #b0b0b0;
        }

        .info-tertinggi .mahasiswa-tertinggi {
            font-size: 24px;
            font-weight: bold;
            color: #ffd700;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #b0b0b0;
            font-size: 16px;
            background: #2a2a2a;
            border-radius: 10px;
            border: 1px solid #444;
        }

        .no-data strong {
            color: #ffd700;
            font-size: 20px;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Sistem Nilai Mahasiswa</h1>

        <!-- === Form Input Data === -->
        <div class="form-section">
            <h2>➕ Tambah Data Mahasiswa</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama">Nama Mahasiswa:</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama mahasiswa" required>
                </div>
                <div class="form-group">
                    <label for="nilai">Nilai (0-100):</label>
                    <input type="number" id="nilai" name="nilai" min="0" max="100" placeholder="Masukkan nilai" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="tambah" class="btn btn-primary">Tambah Data</button>
                    <button type="submit" name="reset" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus semua data?')">Reset Semua Data</button>
                </div>
            </form>
        </div>

        <!-- === Info Progress Input === -->
        <?php if (!empty($mahasiswa) && count($mahasiswa) < 5): ?>
            <div class="no-data">
                <p>📝 Data yang sudah masuk: <strong><?php echo count($mahasiswa); ?></strong> dari 5</p>
                <p>Silakan tambah data hingga 5 mahasiswa untuk melihat hasil.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($mahasiswa) && count($mahasiswa) >= 5): ?>
            <!-- === Bagian Statistik === -->
            <div class="statistik">
                <div class="stat-card">
                    <h3>Jumlah Mahasiswa</h3>
                    <div class="value"><?php echo $jumlahMahasiswa; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Nilai Rata-rata</h3>
                    <div class="value"><?php echo number_format($rataRata, 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Nilai Tertinggi</h3>
                    <div class="value"><?php echo $nilaiTertinggi; ?></div>
                </div>
                <div class="stat-card lulus">
                    <h3>Jumlah Lulus</h3>
                    <div class="value"><?php echo $jumlahLulus; ?></div>
                </div>
                <div class="stat-card tidak-lulus">
                    <h3>Jumlah Tidak Lulus</h3>
                    <div class="value"><?php echo $jumlahTidakLulus; ?></div>
                </div>
            </div>

            <!-- === Info Nilai Tertinggi === -->
            <div class="info-tertinggi">
                <h3>🏆 Mahasiswa dengan Nilai Tertinggi</h3>
                <div class="mahasiswa-tertinggi">
                    <?php echo $namaTertinggi; ?> (<?php echo $nilaiTertinggi; ?>)
                </div>
            </div>

            <!-- === Tabel Data Mahasiswa === -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($mahasiswa as $mhs) {
                            if ($mhs['nilai'] >= 60) {
                                $status = 'Lulus';
                                $classStatus = 'status-lulus';
                            } else {
                                $status = 'Tidak Lulus';
                                $classStatus = 'status-tidak-lulus';
                            }
                            
                            $classNilai = ($mhs['nilai'] == $nilaiTertinggi) ? 'nilai-tertinggi' : '';
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $mhs['nama']; ?></td>
                                <td class="<?php echo $classNilai; ?>"><?php echo $mhs['nilai']; ?></td>
                                <td><span class="<?php echo $classStatus; ?>"><?php echo $status; ?></span></td>
                            </tr>
                        <?php
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>📭 Belum ada data mahasiswa. Silakan tambah data melalui form di atas.</p>
                <p>📝 Tambahkan minimal 5 data untuk melihat hasil lengkap.</p>
            </div>
        <?php endif; ?>

        <footer>
            <p>© 2024 Sistem Nilai Mahasiswa - Dibuat dengan PHP Native</p>
        </footer>
    </div>
</body>
</html>
