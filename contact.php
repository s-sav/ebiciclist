<?php

@include 'config.php';

session_start();

if(isset($_POST['send'])){
   $name = $_POST['name'];
   $name = filter_var($name, );
   $email = $_POST['email'];
   $email = filter_var($email, );
   $number = $_POST['number'];
   $number = filter_var($number, );
   $msg = $_POST['msg'];
   $msg = filter_var($msg, );
   $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

   $select_message = $conn->prepare("SELECT * FROM `message` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $select_message->execute([$name, $email, $number, $msg]);

   if($select_message->rowCount() > 0){
      $message[] = 'mesaj deja trimis!';
   }else{
      if ($user_id !== null) {
         $insert_message = $conn->prepare("INSERT INTO `message`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
         $insert_message->execute([$user_id, $name, $email, $number, $msg]);
         $message[] = 'mesaj trimis cu succes!';
      } else {
         $message[] = 'Eroare: User-ul nu este autentificat!';
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
   <title>Contact</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/general.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="contact">

   <h1 class="title">Ia legatura cu noi </h1>

   <form action="" method="POST">
      <input type="text" name="name" class="box" required placeholder="nume">
      <input type="email" name="email" class="box" required placeholder="email">
      <input type="text" name="number" min="0" class="box" required placeholder="0760000000" pattern="07\d{8}" required>
      <textarea name="msg" class="box" required placeholder="mesaj" cols="30" rows="10"></textarea>
      <input type="submit" value="trimite mesaj" class="btn" name="send">
   </form>

</section>
<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
