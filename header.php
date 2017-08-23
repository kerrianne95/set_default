<?php

  function connectMongo() {
    $connection = new MongoClient("mongodb://admin:admin@ds135983.mlab.com:35983/intro_to_iot");
    $db = $connection->intro_to_iot;
    return $db;
  }

?>

<link rel="stylesheet" type="text/css" href="assets/css/header.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>

<!-- HEADER -->
<header>
	<ul>
		<li><img src="assets/img/lightbox-base-3.jpg"></li>
		<li><a href="index.php">AmbiLamp</a></li>
		<li><a href="details.php">Details</a></li>
	</ul>
</header>
