<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : Modification d'un attribut dans une table donnée								*/
/*	Parameter : id module, id broche, attribut à changer, la nouvelle valeur			*/
/* 	Information : Appel du script php qui envoi au module								*/
/*	Sortie : Log pour debug																*/
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
	$idModule = $_POST['idModule'];
	$idBroche = $_POST['idBroche'];
	$attributeName=$_POST['attribute'];
	$value = $_POST['value'];

	// convert value of swipe to numeric value
	if($value == "on")
		$value=1 ;
	else 
		$value=0 ;

	// Send informations to module
	header('Location: sendToModule.php?idModule='.$idModule.'&idBroche='.$idBroche.'&value='.$value);

	//$sql = "UPDATE ".$table." SET ".$attributeName."=".$value." WHERE id_module=".$idModule." AND id_broche=".$idBroche;
	$sql = " INSERT INTO ValeurBroche (id_module, id_broche, saDate, saValeur ) VALUES (".$idModule.", ".$idBroche.", NOW(),".$value.")" ;

	echo "Table : <strong>".$table."</strong><br>";
	echo "Id module : <strong>".$idModule."</strong><br>";
	echo "Id broche : <strong>".$idBroche."</strong><br>";
	echo "Nom attribut : <strong>".$attributeName."</strong><br>";
	echo "Value : <strong>".$value."</strong><br>";
	echo "Commande : <strong>".$sql."</strong><br>" ;

	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error. "<br>";
	}
	

	//close the connection
	$conn->close();
?>
