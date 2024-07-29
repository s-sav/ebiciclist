<?php

@include 'config.php';

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Metode plata</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/general.css">

</head>
<body>

<?php include 'header.php'; ?>
<section class="payment-methods">
   <h1 class="title">Metode de plată</h1> 
</section>

<section class="about">
   <div class="payment-options">
      <div class="payment-option">
         <img src="images/transfer.png" alt="Transfer bancar">
         <p class="payment-method">Transfer bancar</p>
         <h2>Poți folosi opiunea de transfer bancar pentru a plati suma.
         
         </h2>
      </div>
      <div class="payment-option">
         <img src="images/ramburs.png" alt="Plata ramburs">
         <p class="payment-method">Plata ramburs</p>
         <h2>Poți folosi plata ramburs, așa vei plăti la livrare.</h2>
      </div>
      <div class="payment-option">
         <img src="images/card.png" alt="Plata cu cardul prin intermediul Stripe" style="width: 320px; height: auto;">
         <p class="payment-method">Plata cu cardul</p>
         <h2>Plațile online sunt realizate prin platforma Stripe.</h2>
      </div>
   </div>
</section>
<section>
   <h4>Date necesare pentru transfer bancar:<br>
      <hr>
      SC EBICICLIST SRL<br>
      CUI RO 563754310<br>
      Nr. Reg.Com. J41/5841/2020<br>
      Sediul social: Soseaua Stefan Cel Mare 9 Bucuresti<br>
      Banca Raiffeisen Bank Cont IBAN: RO75 RZBR 0000 0100 1001 0001
      
   </h4>
            
</section>


<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>