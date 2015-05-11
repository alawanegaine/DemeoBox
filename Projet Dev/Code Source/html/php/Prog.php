<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : Ajout de de la programmation dans labase de donnée							*/
/*	Parameter : id module, id broche, on/off si on utilise l'humidte ou pas, le taux, 	*/
/* 				heure de début, durée d'arrosage 										*/
/* 	Information : Si une programmation existe déjà  pour ce jour, on écrase la config 	*/
/*	Sortie : Log																		*/
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

	$idModule = $_POST['idModule'];

	$idBroche = $_POST['idBroche'];

	echo "Module : <strong>".$idModule."</strong> broche : <strong>".$idBroche."</strong><br>" ;

	$JourArrosage = $_POST['JourArrosage'];

	$Jours = preg_split("/[,]/", $JourArrosage);
	//print_r($array);
	//echo "<br>" ;

	$HumiditeOk = $_POST['HumiditeOk'];
	//echo $HumiditeOk;

	$TauxHumidite = $_POST['TauxHumidite'];
	//echo $TauxHumidite;

	$dateDebut = $_POST['HeureArrosage'];
	//echo $HeureDebut."<br>";

	// Get minutes and hours to add
	$DureeArrosage = date_parse($_POST['DureeArrosage']);
	$HeureDureeArrosage = $DureeArrosage['hour'];
	$MinuteDureeArrosage = $DureeArrosage['minute'];
	//echo "Heure : ".$HeureDureeArrosage." Minutes : ".$MinuteDureeArrosage."<br>";

	$dateFin = date('H:i',strtotime("+$HeureDureeArrosage hours +$MinuteDureeArrosage minutes", strtotime($dateDebut))) ;
	echo "Date Fin : ".$dateFin."<br>";

	// iteration sur la liste des jour configurés
	foreach ($Jours as $jour)
	{
		if($idBroche == "ALL") // If "all" selected
		{
			$sql = "SELECT * FROM BrocheModule WHERE sonType='INTERRUPTEUR'" ;
			$result = $conn->query($sql);
			echo "Result : ".$result->num_rows."<br>";
			while ($row = $result->fetch_array()) {
				$sql2 = "SELECT * FROM ProgrammationJour WHERE Jour='".$jour."' AND id_module=".$row{'id_module'}." AND id_broche=".$row{'id_broche'};
				$result2 = $conn->query($sql2);
				echo "Result 2: ".$result2->num_rows."<br>";

				// si il y a dejà une valeur, on met à jour
				if ($result2->num_rows > 0) {
					$sql3 = "UPDATE ProgrammationJour SET dateDebut='".$dateDebut."' , dateFin='".$dateFin."' , humidite=".$TauxHumidite." , temperature="."NULL"." , luminosite="."NULL"." WHERE Jour='".$jour."' AND id_module=".$row{'id_module'}." AND id_broche=".$row{'id_broche'} ;
					echo "sql : <strong>".$sql3."</strong><br>";
				}
				// sinon on insert lanouvelle config
				else {
					$sql3 = "INSERT INTO ProgrammationJour (Jour, id_module, id_broche, dateDebut, dateFin, humidite, temperature, luminosite) VALUES ('".$jour."',".$row{'id_module'}.",".$row{'id_broche'}.",'".$dateDebut."','".$dateFin."',".$TauxHumidite.","."NULL".","."NULL".")" ;
					echo "sql : <strong>".$sql3."</strong><br>";
				}
				// execute query
				if ($conn->query($sql3) === TRUE) {
					echo "Record updated successfully <br>";
				} else {
					echo "Error updating record: " . $conn->error. "<br>";
				}
			}
		}
		else  // if only one broche selected
		{
			$sql2 = "SELECT * FROM ProgrammationJour WHERE Jour='".$jour."' AND id_module=".$idModule." AND id_broche=".$idBroche;
			$result2 = $conn->query($sql2);
			echo "Result 2 : ".$result2->num_rows."<br>";

			if ($result2->num_rows > 0) {
				$sql3 = "UPDATE ProgrammationJour SET dateDebut='".$dateDebut."' , dateFin='".$dateFin."' , humidite=".$TauxHumidite." , temperature="."NULL"." , luminosite="."NULL"." WHERE Jour='".$jour."' AND id_module=".$idModule." AND id_broche=".$idBroche ;
				echo "sql : <strong>".$sql3."</strong><br>";
			}
			else {
				$sql3 = "INSERT INTO ProgrammationJour (Jour, id_module, id_broche, dateDebut, dateFin, humidite, temperature, luminosite) VALUES ('".$jour."',".$idModule.",".$idBroche.",'".$dateDebut."','".$dateFin."',".$TauxHumidite.","."NULL".","."NULL".")" ;
				echo "sql : <strong>".$sql3."</strong><br>";
			}
			echo $sql3."<br>" ;
			if ($conn->query($sql3) === TRUE) {
				echo "Record updated successfully <br>";
			} else {
				echo "Error updating record: " . $conn->error. "<br>";
			}
		}
	}

	//close the connection
	$conn->close();
?>