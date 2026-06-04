<?php
// submit_admission.php

// Replace these with your RDS/MySQL credentials
define('DB_HOST', 'RDS_ENDPOINT'); // <-- Replace this with your RDS endpoint
define('DB_NAME', 'student_admission');
define('DB_USER', 'admission_user'); // your DB username
define('DB_PASS', 'your_password_here'); // your DB password
define('DB_CHARSET', 'utf8mb4');

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect form inputs
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $gender   = $_POST['gender'] ?? '';
    $course   = $_POST['course'] ?? '';
    $dob      = $_POST['dob'] ?? '';
    $address  = trim($_POST['address'] ?? '');

    // Basic validation
    $errors = [];
    if ($fullname === '') $errors[] = "Full name is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($phone === '') $errors[] = "Phone number is required.";
    if ($gender === '') $errors[] = "Gender is required.";
    if ($course === '') $errors[] = "Course selection is required.";
    if ($dob === '') $errors[] = "Date of Birth is required.";
    if ($address === '') $errors[] = "Address is required.";

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO students (fullname, email, phone, gender, course, dob, address) 
                    VALUES (:fullname, :email, :phone, :gender, :course, :dob, :address)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':fullname' => $fullname,
                ':email'    => $email,
                ':phone'    => $phone,
                ':gender'   => $gender,
                ':course'   => $course,
                ':dob'      => $dob,
                ':address'  => $address
            ]);

            echo "<h3 style='color:green;text-align:center;margin-top:20px;'>Admission submitted successfully!</h3>";

        } catch (PDOException $e) {
            // Handle duplicate email
            if ($e->getCode() == 23000) {
                echo "<h3 style='color:red;text-align:center;margin-top:20px;'>Email already registered.</h3>";
            } else {
                echo "<h3 style='color:red;text-align:center;margin-top:20px;'>Database error: ".$e->getMessage()."</h3>";
            }
        }
    } else {
        echo "<div style='color:red;margin:20px;'><ul>";
        foreach ($errors as $err) {
            echo "<li>" . htmlspecialchars($err) . "</li>";
        }
        echo "</ul></div>";
    }
} else {
    echo "<h3 style='text-align:center;margin-top:20px;'>Invalid request method.</h3>";
}
?>