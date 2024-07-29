<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
@include 'config.php';
session_start();
$message = [];

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $token = bin2hex(random_bytes(50)); 
            date_default_timezone_set('Europe/Bucharest');
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); 
            
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expiry]);
            
            
            $resetLink = "http://localhost/ebiciclist/resetare_parola.php?token=" . $token;

            // PHPMailer 
            $mail = new PHPMailer(true);

            try {
                //Server 
                $mail->isSMTP();
                $mail->Host = $_ENV['SMTP_HOST']; 
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USERNAME']; 
                $mail->Password = $_ENV['SMTP_PASSWORD']; 
                $mail->SMTPSecure = $_ENV['SMTP_SECURE']; 
                $mail->Port = $_ENV['SMTP_PORT']; 
            
                // admin si user 
                $mail->setFrom($_ENV['SMTP_USERNAME'], 'Mailer'); 
                $mail->addAddress($email); 
                
                
                $mail->isHTML(true); 
                $mail->Subject = 'Resetare Parola eBiciclist';
                $mail->Body    = 'A fost facuta o cerere de resetare a parolei. Apasa pe link pentru a reseta parola: <a href="'.$resetLink.'">'.$resetLink.'</a>';
                
                $mail->send();
                $message[] = 'Verifică adresa de email pentru a putea reseta parola.';
            } catch (Exception $e) {
                $message[] = 'Mesajul nu a putut fi trimis: '.$mail->ErrorInfo;
            }
        } else {
            $message[] = 'Daca adresa exista in baza de date, va fi trimis un link de resetare.';
        }
    } else {
        $message[] = 'Introdu o adresa de email validă.';
    }
    $message = array_unique($message);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetare Parolă</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/componente.css">

</head>
<body>
<?php include 'header.php'; ?>

<section class="form-container">
    <form action="" method="POST">
        <h3>Resetare Parolă</h3>
        <input type="email" name="email" class="box" placeholder="Email-ul tău" required>
        <input type="submit" value="Trimite link de resetare" class="btn" name="submit">
    </form>
</section>
</body>
</html>
