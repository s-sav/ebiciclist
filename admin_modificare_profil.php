<?php

@include 'config.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:autentificare.php');
    exit;
}

$message = []; 


$fetch_profile = [];

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
        $stmt->execute([$admin_id]);
        $fetch_profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fetch_profile && isset($fetch_profile['password'])) {
        
            if (password_verify($old_pass, $fetch_profile['password'])) {
            
                $new_password = password_hash($new_pass, PASSWORD_DEFAULT);

                if ($new_pass === $confirm_pass) {
                    $update_profile = $conn->prepare("UPDATE `users` SET password = ?, email = ? WHERE id = ?");
                    $update_profile->execute([$new_password, $new_email, $admin_id]);
                    $message[] = 'Profilul a fost updatat cu succes!';
                } else {
                    $message[] = 'Noua parolă nu se potrivește';
                }
            } else {
                $message[] = 'Parola veche nu se potrivește';
            }
        } else {
            $message[] = 'Utilizatorul nu a fost gasit';
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
<?php include 'admin_header.php'; ?>
<section class="update-profile">
<h1 class="title">update profil</h1>
<form action="" method="POST" enctype="multipart/form-data">
    <div class="flex">
        <div class="inputBox">
            <span>Nume :</span>
            <input type="text" name="name" value="<?= isset($fetch_profile['name']) ? $fetch_profile['name'] : ''; ?>" placeholder="Update nume" required class="box">
            <span>Email :</span>
            <input type="email" name="email" value="<?= isset($fetch_profile['email']) ? $fetch_profile['email'] : ''; ?>" placeholder="Update email" required class="box">
        </div>
        <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?= isset($fetch_profile['password']) ? $fetch_profile['password'] : ''; ?>">
            <span>Vechea parolă :</span>
            <input type="password" name="old_pass" placeholder="Introduceți vechea parolă" class="box">
            <span>Noua parolă :</span>
            <input type="password" name="new_pass" placeholder="Introduceți noua parolă" class="box">
            <span>Confirmare parolă nouă :</span>
            <input type="password" name="confirm_pass" placeholder="Confirmati noua parolă" class="box">
        </div>
    </div>
    <div class="flex-btn">
        <input type="submit" class="btn" value="Update profil" name="update_profile">
        <a href="index.php" class="option-btn">Înapoi</a>
    </div>
</form>
</section>



<script src="js/script.js"></script>
</body>
</html>