<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : Ajout d'une broche sur un module												*/
/*	Parameter : id module, id broche, type broche, son module météo, sa description 	*/
/*	Sortie : Log pour débug																*/
/*--------------------------------------------------------------------------------------*/

	$settings = parse_ini_file("../config/config.ini", TRUE);

	$username = $settings['database']['user'];
	$password = $settings['database']['mdp'];
	$hostname = $settings['server']['URL']; 
	$dbname = $settings['database']['db'];

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	// Get value from ajax method
	$idModule = $_POST['idModule'];
	$idBroche = $_POST['idBroche'];
	$typeBroche = $_POST['typeBroche'];
	$sonModuleMeteo = $_POST['sonModuleMeteo'];
	$descriptionBroche = $_POST['descriptionBroche'];

	// Insertion de la broche 
	$sql = " INSERT INTO BrocheModule (id_module, id_broche, sonType, sonModuleMeteo, saDescription ) VALUES (".$idModule.", ".$idBroche.", '".$typeBroche."','".$sonModuleMeteo."',\"".$descriptionBroche."\")" ;
	echo "sql :<strong>".$sql."</strong><br>" ;

	echo "Id module : <strong>".$idModule."</strong><br>";
	echo "Id broche : <strong>".$idBroche."</strong><br>";
	echo "Type broche : <strong>".$typeBroche."</strong><br>";
	echo "Son module meteo : <strong>".$sonModuleMeteo."</strong><br>";
	echo "Description broche : <strong>".$descriptionBroche."</strong><br>";

	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error. "<br>";
	}
	
	// configuration de la broche à l'état "off"
	$sql2 = "INSERT INTO `ValeurBroche`(`id_module`, `id_broche`, `saDate`, `saValeur`) VALUES (".$idModule.",".$idBroche.",NOW(),0)";
	echo "sql2 :<strong>".$sql2."</strong><br>" ;

	if ($conn->query($sql2) === TRUE) {
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error. "<br>";
	}

	//close the connection
	$conn->close();
?>

