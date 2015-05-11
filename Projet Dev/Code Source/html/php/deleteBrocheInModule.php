<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : suppression d'une broche sur un module										*/
/*	Parameter : id module, id broche 													*/
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

	// Delete all values for this pin
	$sql = "DELETE FROM ValeurBroche WHERE id_module= ".$idModule." AND id_broche= ".$idBroche ;
	echo "sql :<strong>".$sql."</strong><br>" ;

	echo "Id module : <strong>".$idModule."</strong><br>";
	echo "Id broche : <strong>".$idBroche."</strong><br>";

	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error. "<br>";
	}

	// Delete all values for this pin in PROGRAMMATION
	$sql2 = "DELETE FROM ProgrammationJour WHERE id_module= ".$idModule." AND id_broche= ".$idBroche ;
	echo "sql :<strong>".$sql2."</strong><br>" ;

	echo "Id module : <strong>".$idModule."</strong><br>";
	echo "Id broche : <strong>".$idBroche."</strong><br>";

	if ($conn->query($sql2) === TRUE) {
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error. "<br>";
	}
	
	// finnaly, delete the pin
	$sql3 = "DELETE FROM BrocheModule WHERE id_module= ".$idModule." AND id_broche= ".$idBroche ;
	echo "sql2 :<strong>".$sql3."</strong><br>" ;

	if ($conn->query($sql3) === TRUE) {
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error. "<br>";
	}

	//close the connection
	$conn->close();
?>

