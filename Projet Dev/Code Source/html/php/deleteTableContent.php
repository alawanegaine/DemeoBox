<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : Suppression de tout le contenu d'une table									*/
/*	Parameter : nom de la table à vider													*/
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
	$table = $_POST['table'];
	echo "table : <strong>".$table."</strong><br>";

	$sql = "DELETE FROM ".$table ;
	echo "sql : <strong>".$sql."</strong><br>";
	
	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error. "<br>";
	}

	//close the connection
	$conn->close();
?>
