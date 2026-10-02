<?php

header('Content-Type: application/json');

// Koneksi ke database MySQL
$conn = new mysqli(
    "localhost",
    "root",
    "",
    "db_devclub"
);

// Cek koneksi database
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal!"
    ]);

    exit();
}

// Mengambil action dari URL
$action = $_GET['action'] ?? '';

// Memastikan request menggunakan POST
// dan action yang digunakan adalah daftar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'daftar') {

    // Mengambil JSON dari request body
    $input = json_decode(
        file_get_contents('php://input'),
        true
    );

    // Mengambil data dari JSON
    $nis = trim($input['nis'] ?? '');
    $nama = trim($input['nama'] ?? '');
    $divisi = trim($input['divisi'] ?? '');
    $email = trim($input['email'] ?? '');

    // Validasi field kosong
    if (
        empty($nis) ||
        empty($nama) ||
        empty($divisi) ||
        empty($email)
    ) {
        echo json_encode([
            "status" => "error",
            "message" => "Semua field formulir wajib diisi!"
        ]);

        $conn->close();
        exit();
    }

    // Validasi NIS di sisi server
    if (!preg_match('/^[0-9]{8}$/', $nis)) {
        echo json_encode([
            "status" => "error",
            "message" => "NIS harus terdiri dari 8 digit angka!"
        ]);

        $conn->close();
        exit();
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status" => "error",
            "message" => "Format email tidak valid!"
        ]);

        $conn->close();
        exit();
    }

    // Prepared Statement
    // Digunakan untuk mencegah SQL Injection
    $stmt = $conn->prepare(
        "INSERT INTO pendaftaran
        (nis, nama, divisi, email)
        VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "Query database gagal diproses!"
        ]);

        $conn->close();
        exit();
    }

    // Bind parameter
    $stmt->bind_param(
        "ssss",
        $nis,
        $nama,
        $divisi,
        $email
    );

    // Eksekusi query
    if ($stmt->execute()) {

        echo json_encode([
            "status" => "success",
            "message" => "Selamat! Data pendaftaran berhasil disimpan."
        ]);

    } else {

        // Error 1062 = duplicate entry
        if ($stmt->errno === 1062) {

            echo json_encode([
                "status" => "error",
                "message" => "NIS sudah terdaftar!"
            ]);

        } else {

            echo json_encode([
                "status" => "error",
                "message" => "Gagal menyimpan data pendaftaran!"
            ]);
        }
    }

    $stmt->close();

} else {

    // Jika endpoint/action tidak sesuai
    echo json_encode([
        "status" => "error",
        "message" => "Endpoint atau method tidak valid!"
    ]);
}

// Tutup koneksi database
$conn->close();

?>
