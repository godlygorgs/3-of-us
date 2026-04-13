<?php
include 'koneksi1.php';


// Mengambil data dari form (sama seperti contoh guru pakai mysqli_real_escape_string)
$nama  = mysqli_real_escape_string($conn, $_POST['nama']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
$pesan = mysqli_real_escape_string($conn, $_POST['pesan']);

// Simpan ke database (pola INSERT sama seperti contoh guru)
$query = "insert into db_kontak(nama, email, no_hp, pesan) values ('$nama', '$email', '$no_hp', '$pesan')";


if (mysqli_query($conn, $query)) {

    // Kirim WA ke pengirim (pola curl sama persis seperti contoh guru)
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => array(
            'target'  => $no_hp,   // kirim WA ke nomor pengirim
            'message' => "Halo $nama! 🌸

Terima kasih sudah mengirim pesan ke *3 of Us*.
Kami akan segera membalas pesanmu!

Pesan yang kamu kirim:
\"$pesan\"

— Kanaka Azalia 🌷"
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: H9b4Kzhe7WnqP92NZRsi'  // token Fonnte kamu
        ),
    ));

    curl_exec($curl);
    curl_close($curl);

    // Kirim WA notif ke admin (kamu sendiri)
    $curl2 = curl_init();

    curl_setopt_array($curl2, array(
        CURLOPT_URL            => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => array(
            'target'  => '6281262614972',   // nomor WA kamu (admin)
            'message' => "📩 *Pesan Baru di Website 3 of Us!*

Nama  : $nama
Email : $email
No HP : $no_hp
Pesan : $pesan"
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: H9b4Kzhe7WnqP92NZRsi'
        ),
    ));

    curl_exec($curl2);
    curl_close($curl2);

    // Redirect kembali ke halaman utama (sama seperti contoh guru)
    echo "<script>alert('Pesan berhasil dikirim!'); window.location='index.html'</script>";

} else {
    echo "Kesalahan: " . mysqli_error($conn);
}

// Menutup koneksi (sama seperti contoh guru)
mysqli_close($conn);
?>
