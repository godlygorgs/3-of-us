<?php
include 'koneksi.php';

function kirimWA($nomor, $pesan) {
    $token = "H9b4Kzhe7WnqP92NZRsi"; // GANTI DENGAN TOKEN ASLI ANDA
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ["target" => $nomor, "message" => $pesan],
        CURLOPT_HTTPHEADER => ["Authorization: " . $token],
    ]);
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    return ["response" => $response, "httpCode" => $httpCode];
}

// Cek apakah ada data yang dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil dan bersihkan data
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    
    // Validasi: cek tidak ada yang kosong
    if (empty($nama) || empty($email) || empty($no_hp) || empty($pesan)) {
        echo "<script>alert('❌ Semua field harus diisi!'); window.history.back();</script>";
        exit;
    }
    
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('❌ Format email tidak valid!'); window.history.back();</script>";
        exit;
    }
    
    // Validasi nomor HP (minimal 10 digit)
    if (strlen($no_hp) < 10) {
        echo "<script>alert('❌ Nomor HP terlalu pendek!'); window.history.back();</script>";
        exit;
    }
    
    // Gunakan prepared statement untuk keamanan (cegah SQL Injection)
    $sql = "INSERT INTO users (nama, email, no_hp, pesan) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $no_hp, $pesan);
    
    if (mysqli_stmt_execute($stmt)) {
        // Kirim notif WA ke admin
        $nomorAdmin = "6281262614972";
        $pesanWA = "📩 *Pesan Baru dari 3 of Us!*\n\n";
        $pesanWA .= "👤 Nama: $nama\n";
        $pesanWA .= "📧 Email: $email\n";
        $pesanWA .= "📱 No. HP: $no_hp\n";
        $pesanWA .= "💬 Pesan: $pesan\n\n";
        $pesanWA .= "Dikirim via website 3 of us.";
        
        $waResult = kirimWA($nomorAdmin, $pesanWA);
        
        // Cek apakah WA berhasil terkirim
        $resultArray = json_decode($waResult['response'], true);
        if ($waResult['httpCode'] == 200 && isset($resultArray['status']) && $resultArray['status'] == true) {
            echo "<script>alert('✅ Pesan berhasil dikirim! Admin akan segera merespons.'); window.location='index.html';</script>";
        } else {
            // Pesan tetap tersimpan meskipun WA gagal
            echo "<script>alert('⚠️ Pesan tersimpan, tetapi notifikasi WA gagal dikirim. Admin akan cek secara manual.'); window.location='index.html';</script>";
        }
    } else {
        echo "<script>alert('❌ Gagal menyimpan pesan. Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Akses tidak valid!'); window.location='index.html';</script>";
}

mysqli_close($conn);
?>