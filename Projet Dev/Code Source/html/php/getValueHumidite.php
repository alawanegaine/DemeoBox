<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : Récupération la dernière valeur du capteur d'Humidite pour la broche			*/
/*	Parameter : id module, id broche													*/
/* 	Information : Si pas de module météo associé, on prend celui par default			*/
/*	Sortie : dernière valeur d'humidité du module météo									*/
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

	$idModule = $_GET['id_module'];
	$idBroche = $_GET['id_broche'];

	//récupération du module météo associé à l'id broche 
	$sql = "SELECT * FROM BrocheModule WHERE id_module=".$idModule." AND id_broche=".$idBroche ;
	//echo "Sql : <strong>".$sql."</strong><br>" ;
	$row = $conn->query($sql)->fetch_array();

	// récupération des infos du module météo associé
	$sql2 = "SELECT * FROM BrocheModule WHERE id_module=".$row{'sonModuleMeteo'} ;	
	//echo "Sql : <strong>".$sql2."</strong><br>" ;
	$result2 = $conn->query($sql2);

	//récupération de l'id de la broche du capteur d'humidité
	while ($row2 = $result2->fetch_array()) {
		if (($row2{'saDescription'} == "Humidite") || ($row2{'saDescription'} == "humidite")) 
		{
			//echo "Broche humidite founded !! <br>" ;
			$idBrocheHumidite = $row2{'id_broche'} ;
		}
	}

	if ($idBrocheHumidite == "") {
		//echo "Default Broche ... <br>" ;
		$idBrocheHumidite = "2" ;
	}

	// récupération de la dernière valeur connue du capteur d'humidité
	$sql3 = "SELECT DISTINCT saValeur,saDate FROM ValeurBroche WHERE id_module=".$row{'sonModuleMeteo'}." AND id_broche=".$idBrocheHumidite." ORDER BY saDate DESC LIMIT 1" ;
	//echo "Sql : <strong>".$sql3."</strong><br>" ;
	$row3 = $conn->query($sql3)->fetch_array();

	//echo "Valeur Broche humidite : <strong>".$row3{'saValeur'}."</strong><br>";
	echo $row3{'saValeur'} ;
?>
