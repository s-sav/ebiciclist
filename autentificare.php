<?php


@include 'config.php';
session_start();
$message = []; 
if(isset($_POST['submit'])){
   if(empty($_POST['email']) || empty($_POST['password'])){
      $message[] = 'trebuiesc completate toate campurile';
   }else{
      $email = $_POST['email'];
      $email = filter_var($email, FILTER_SANITIZE_EMAIL);
      $pass = $_POST['password'];
      $pass = filter_var($pass);
      $sql = "SELECT * FROM `users` WHERE email = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$email]);
      $rowCount = $stmt->rowCount();  
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if($rowCount > 0){
         if(password_verify($pass, $row['password'])){
            if($row['user_type'] == 'admin'){
               $_SESSION['admin_id'] = $row['id'];
               header('location:admin_meniu.php');
            }elseif($row['user_type'] == 'user'){
               $_SESSION['user_id'] = $row['id'];
               header('location:index.php');
            }else{
               $message[] = 'nu a fost găsit nici un utilizator!';
            }
         }else{
            $message[] = 'adresă de email sau parolă gresită!';
         }
      }else{
         $message[] = 'adresă de email sau parolă gresită!';
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
   <title>Autentificare</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/componente.css">

</head>
<body>
<?php include 'header.php'; ?>
<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
<section class="form-container">
   <form action="" enctype="multipart/form-data" method="POST">
      <h3>Ai deja cont?</h3>
      <input type="email" name="email" class="box" placeholder=" email" required>
      <input type="password" name="password" class="box" placeholder="parolă" required>
      <p><a href="resetare.php">Ai uitat parola?</a></p>
      <input type="submit" value="Autentifică-te" class="btn" name="submit">
      <p>Nu ai cont? <a href="inregistrare.php">Înregistrează-te aici</a></p>
      
   </form>
</section>
</body>
</html>
