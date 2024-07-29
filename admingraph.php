<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
   header('Location: autentificare.php');
   exit;
}

function sanitizeData($data) {
   return $data;
}

$total_sales_query = $conn->prepare("SELECT SUM(total_price) AS total_sales FROM orders");
$total_sales_query->execute();
$total_sales_fetch = $total_sales_query->fetch(PDO::FETCH_ASSOC);
$total_sales = $total_sales_fetch['total_sales'];

$paymentMethodData = [];
$select_sales_by_method = $conn->query("SELECT method, SUM(total_price) AS total_sales FROM orders GROUP BY method");
while ($row = $select_sales_by_method->fetch(PDO::FETCH_ASSOC)) {
   $paymentMethodData[sanitizeData($row['method'])] = $row['total_sales'];
}

$productCategoryData = [];
$select_sales_by_category = $conn->query("SELECT p.category, SUM(o.total_price) AS total_sales FROM orders o INNER JOIN products p ON o.total_products LIKE CONCAT('%', p.name, '%') GROUP BY p.category");
while ($row = $select_sales_by_category->fetch(PDO::FETCH_ASSOC)) {
   $productCategoryData[sanitizeData($row['category'])] = $row['total_sales'];
}
$methodLabels = array_keys($paymentMethodData);
$methodSales = array_values($paymentMethodData);
$categoryTotalSales = array_sum($methodSales);

$categoryLabels = array_keys($productCategoryData);
$categorySales = array_values($productCategoryData);
$categoryTotalSales = array_sum($categorySales);
?>
<?php include 'admin_header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Grafice Vanzari</title>
   <link rel="stylesheet" href="css/adminmeniu.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <style>
      .container {
         width: 50%; 
         margin: 0 auto;
      }
      canvas {
         width: 100% !important;
         height: auto !important;
      }
   </style>
</head>
<body>
   <h1>Grafice Vanzari(Total: <?php echo $total_sales; ?>)</h1>

   <div class="container">
      <canvas id="paymentMethodChart" width="400" height="200"></canvas>
   </div>

   <div class="container">
      <canvas id="productCategoryChart" width="400" height="200"></canvas>
   </div>

   <script>
var totalSales = <?php echo array_sum($methodSales); ?>;

// Grafic metode de plata
var paymentMethodChartCtx = document.getElementById('paymentMethodChart').getContext('2d');
var paymentMethodChart = new Chart(paymentMethodChartCtx, {
   type: 'bar',
   data: {
      labels: <?php echo json_encode($methodLabels); ?>,
      datasets: [{
            label: 'arată datele',
            data: <?php echo json_encode($methodSales); ?>,
            backgroundColor: [
               
               'rgba(255, 99, 132, 0.2)',
               'rgba(54, 162, 235, 0.2)',
               'rgba(255, 206, 86, 0.2)',
               'rgba(75, 192, 192, 0.2)',
               'rgba(153, 102, 255, 0.2)',
               'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
               'rgba(255, 99, 132, 1)',
               'rgba(54, 162, 235, 1)',
               'rgba(255, 206, 86, 1)',
               'rgba(75, 192, 192, 1)',
               'rgba(153, 102, 255, 1)',
               'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
      }]
   },
   options: {
      plugins: {
            title: {
               display: true,
               text: 'Vanzari in functie de metoda de plata ',
               font: {
                  weight: 'bold' 
               }
            }
      },
      scales: {
            y: {
               beginAtZero: true
            }
      }
   }
});

      // Grafic categorii de produse
      var productCategoryChartCtx = document.getElementById('productCategoryChart').getContext('2d');
      var productCategoryChart = new Chart(productCategoryChartCtx, {
         type: 'pie',
         data: {
            labels: <?php echo json_encode($categoryLabels); ?>,
            datasets: [{
               label: 'Vanzari in functie de categoria',
               data: <?php echo json_encode($categorySales); ?>,
               backgroundColor: [
                  'rgba(255, 99, 132, 0.2)',
                  'rgba(54, 162, 235, 0.2)',
                  'rgba(255, 206, 86, 0.2)',
                  'rgba(75, 192, 192, 0.2)',
                  'rgba(153, 102, 255, 0.2)',
                  'rgba(255, 159, 64, 0.2)'
               ],
               borderColor: [
                  'rgba(255, 99, 132, 1)',
                  'rgba(54, 162, 235, 1)',
                  'rgba(255, 206, 86, 1)',
                  'rgba(75, 192, 192, 1)',
                  'rgba(153, 102, 255, 1)',
                  'rgba(255, 159, 64, 1)'
               ],
               borderWidth: 1
            }]
         },
         options: {
            plugins: {
               title: {
                  display: true,
                  text: 'Vanzari în funcție de categoria de produse  '
               }
            }
         }
      });
   </script>
</body>
</html>
