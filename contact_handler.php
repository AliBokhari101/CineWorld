<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $query = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";
    $inserted = mysqli_query($conn, $query);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'haroonmzhrx@gmail.com';
        $mail->Password   = 'ussvezyibzsfqtkm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('haroonmzhrx@gmail.com', 'CineWorld Contact Form');
        $mail->addAddress('haroonmzhrx@gmail.com');

        $mail->isHTML(false);
        $mail->Subject = "New Contact Message from $name";
        $mail->Body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

        $mail->send();
        $mailed = true;
    } catch (Exception $e) {
        $mailed = false;
    }

    if ($inserted && $mailed) {
        header("Location: contact.php?status=success");
    } else {
        header("Location: contact.php?status=error");
    }
    exit();
}
?>
