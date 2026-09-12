<html>

<head>

 <title>Umbrella Corporation</title>

 <!---- JavaScript ----->
 <script language="JavaScript" src="scripts/loginformcheck.js"></script>
 <script language="JavaScript" src="scripts/SHA256.js"></script>

 <!---- CSS ----->
 <?php
 // Kontrollera om användaren är inloggad 
 if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === 'ok') 
 {
     echo '<link rel="stylesheet" href="css/grid_loggedin.css" />';
 } 
 else 
{
     echo '<link rel="stylesheet" href="css/grid.css" />';
 }
 ?>

</head>
<body>