<?php
include_once "system/koneksi.php";

function anti_injection($data) {
    global $connect;
    $filter = mysqli_real_escape_string($connect, stripslashes(strip_tags(htmlspecialchars($data, ENT_QUOTES))));
    return $filter;
}

$username = anti_injection($_POST['username']);
$pass = md5($_POST['password']);

// Pastikan username dan password adalah berupa huruf atau angka.
if (!ctype_alnum($username) || !ctype_alnum($pass)) {
    echo "<script>alert('Kembalilah Kejalan yg benar!!!'); window.location = 'index.php';</script>";
} else {
    $login_adm = mysqli_query($connect, "SELECT * FROM login WHERE username='$username' AND password='$pass' AND status='Aktif'");
    $ketemu = mysqli_num_rows($login_adm);
    $r = mysqli_fetch_array($login_adm);

    // Apabila username dan password ditemukan
    if ($ketemu > 0) {
        session_start();
        include "system/timeout.php";
        $_SESSION['username'] = $r['username'];
        $_SESSION['level'] = $r['level'];

        if ($r['level'] == 'guru') {
            $qkd = "SELECT kd_guru FROM guru WHERE username='$r[username]'";
            $kd = mysqli_query($connect, $qkd);
            $kode = mysqli_fetch_array($kd);
            $_SESSION['kode'] = $kode['kd_guru'];

            $qk = date('Y-m-d');
            $q = date('Y-m-d H:i:s');

            $qkd1 = "INSERT INTO absensi VALUES ('$kode[kd_guru]','$qk','$q','', '')";
            $kd1 = mysqli_query($connect, $qkd1);
            header('location:admin/media.php?module=home');
        } else if ($r['level'] == 'siswa') {
            $qkd = "SELECT nis FROM siswa WHERE username='$r[username]'";
            $kd = mysqli_query($connect, $qkd);
            $kode = mysqli_fetch_array($kd);
            $_SESSION['kode'] = $kode['nis'];
            header('location:admin/media.php?module=home');
        } else if ($r['level'] == 'admin') {
            header('location:admin/media.php?module=homeadm');
        }

        // Session timeout
        $_SESSION['login'] = 1;
        timer();
    } else {
        echo "<script>alert('Maaf! Username atau Password anda salah, mohon diulangi kembali'); window.location = 'index.php';</script>";
    }
}
?>

