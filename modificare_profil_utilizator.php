<?php

@include 'config.php';

session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:autentificare.phpcare.php');
    exit;
}

$message = []; 

if (isset($_POST['update_profile'])) {
    
    if (empty($_POST['old_pass']) || empty($_POST['new_pass']) || empty($_POST['confirm_pass']) || empty($_POST['email'])) {
        $message[] = 'All fields are required.';
    } else {
        
        $old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];
        $new_email = $_POST['email'];
        $new_email = filter_var($new_email, FILTER_SANITIZE_EMAIL);

        
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
        $stmt->execute([$user_id]);
        $fetch_profile = $stmt->fetch(PDO::FETCH_ASSOC);

    
        if ($fetch_profile && isset($fetch_profile['password'])) {
        
            if (password_verify($old_pass, $fetch_profile['password'])) {
                
                $new_password = password_hash($new_pass, PASSWORD_DEFAULT);

                
                if ($new_pass === $confirm_pass) {
                    
                    $update_profile = $conn->prepare("UPDATE `users` SET password = ?, email = ? WHERE id = ?");
                    $update_profile->execute([$new_password, $new_email, $user_id]);
                    $message[] = 'Profil modificat cu succes!';
                } else {
                    $message[] = 'parolele nu se potrivesc.';
                }
            } else {
                $message[] = 'parolă veche greșită.';
            }
        } else {
            $message[] = 'Utilizatorul nu a fost găsit.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>update profil user</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<link rel="stylesheet" href="css/componente.css">
</head>
<body>
<?php include 'header.php'; ?>
<section class="update-profile">
<h1 class="title">modificare cont</h1>
<form action="" method="POST" enctype="multipart/form-data">
    <div class="flex">
        <div class="inputBox">
            <span>nume utilizator :</span>
            <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" placeholder="update username" required class="box">
            <span>email :</span>
            <input type="email" name="email" value="<?= $fetch_profile['email']; ?>" placeholder="update email" required class="box">
        </div>
        <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
            <span>parolă veche :</span>
            <input type="password" name="old_pass" placeholder="introduceți vechea parolă" class="box">
            <span>parolă nouă :</span>
            <input type="password" name="new_pass" placeholder="introduceți o nouă parolă" class="box">
            <span>confirmare parolă :</span>
            <input type="password" name="confirm_pass" placeholder="confirmați noua parolă" class="box">
        </div>
    </div>
    <div class="flex-btn">
        <input type="submit" class="btn" value="modifică" name="update_profile">
        <a href="index.php" class="option-btn">înapoi</a>
    </div>
</form>
</section>
<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>