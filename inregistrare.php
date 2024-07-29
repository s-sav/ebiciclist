<?php
@include 'config.php';

if(isset($_POST['submit'])){
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_UNSAFE_RAW);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);

   $pass = $_POST['password'];
   $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

   $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select->execute([$email]);

   if($select->rowCount() > 0){
      $message[] = 'adresă de email deja folosită!';
   }else{
      $insert = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
      $insert->execute([$name, $email, $hashed_pass]);
      $message[] = 'User creat cu succes!!';
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Înregistrare</title>
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
      <h3>Înregistrează-te</h3>
      <input type="text" name="name" class="box" placeholder="nume" required>
      <input type="email" name="email" class="box" placeholder="email" required>
      <input type="password" name="password" class="box" placeholder="parolă" required>
      <input type="password" name="cpass" class="box" placeholder="confirmă parola" required>
      <input type="submit" value="Creează cont" class="btn" name="submit">
      <p>Ai deja cont? <a href="autentificare.php">autentifică-te aici</a></p>
   </form>
</section>
<script src="js/script.js"></script>
</body>
</html>
