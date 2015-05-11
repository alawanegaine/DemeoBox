<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : Gestion de l'ouverture/fermeture des vannes									*/
/*	Parameter : Aucun																	*/
/* 	Information : Script appellé toutes les minutes par le scheduler					*/
/*	Sortie : Log pour débug																*/
/*--------------------------------------------------------------------------------------*/

	$settings = parse_ini_file("/var/www/html/config/config.ini", TRUE);

	$username = $settings['database']['user'];
	$password = $settings['database']['mdp'];
	$hostname = $settings['server']['URL']; 
	$dbname = $settings['database']['db'];

	// Create connection
	$conn = new mysqli($hostname, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	// On recupère le jour d'ajourd'hui en français
	switch (date("l")) {
		case 'Monday':
			$currentDay = "Lundi";
			break;
		case 'Tuesday':
			$currentDay = " Mardi";
			break;
		case 'Wednesday':
			$currentDay = " Mercredi";
			break;
		case 'Thursday':
			$currentDay = " Jeudi";
			break;
		case 'Friday':
			$currentDay = " Vendredi";
			break;
		case 'Saturday':
			$currentDay = " Samedi";
			break;
		case 'Sunday':
			$currentDay = " Dimanche";
			break;
		default:
			$currentDay = "" ;
			break;
	}
		
	$currentHour = date("H:i:s");// on recupére la valeur de l'heure courante

	$sql = "SELECT * FROM ProgrammationJour WHERE Jour='".$currentDay."'" ;
	echo "Sql : <strong>".$sql."</strong><br> \n" ;

	// itération sur le nombre de ligne du jour actuel 
	$result = $conn->query($sql) ;
	while ($row = $result->fetch_array()) 
	{
		// récupération de la valeur actuelle de l'interrupteur
		$sql2 = "SELECT DISTINCT saValeur,saDate FROM ValeurBroche WHERE id_module=".$row{'id_module'}." AND id_broche=".$row{'id_broche'}." ORDER BY saDate DESC LIMIT 1" ;
		//echo "SQL 2 : ".$sql2."<br>";
		$currentValue = $conn->query($sql2)->fetch_array() ;
		$currentValue = $currentValue{'saValeur'} ;
		echo "Current Value : <strong>".$currentValue."</strong> <br> \n";

		echo "Current hour : <strong>".$currentHour."</strong> <br> \n";
		echo "Date debut : <strong>".$row{'dateDebut'}."</strong> Date Fin : <strong>".$row{'dateFin'}."</strong><br> \n";
		$valhumidite = $row{'humidite'};
		echo "val humidite : <strong>".$valhumidite."</strong><br> \n";

		if ($valhumidite == "") // si le taux d'humidite n'est pas selectionner = NULL
		{
			echo "<strong>Arrosage par timer </strong><br> \n";
			if ((strtotime($currentHour) >= strtotime($row{'dateDebut'})) && (strtotime($currentHour) <= strtotime($row{'dateFin'}))) //si l'heure courante est comprise netre l'heure de debut et l'heure de fin
			{
				if($currentValue == "1") // si la vanne est ouverte
				{
					echo "==> Vanne deja allumee <br> \n" ; // on la laisse ouverte
				}
				else // si la vanne est fermer 
				{
					echo "==> Allumage de la vanne <br> \n" ; // on l'ouvre
					// Send informations to module
					//header('Location: sendToModule.php?idModule='.$row{'idModule'}.'&idBroche='.$row{'idBroche'}.'&value='.'1');
					$sql3 = "INSERT INTO ValeurBroche (id_module, id_broche, saDate, saValeur ) VALUES (".$row{'id_module'}.", ".$row{'id_broche'}.", NOW(),"."1".")" ;
					echo "Sql : <strong>".$sql3."</strong><br> \n" ;
					if ($conn->query($sql3) === TRUE) 
					{
						echo "Record updated successfully <br> \n";
					} 
					else 
					{
						echo "Error updating record: " . $conn->error. "<br> \n";
					}
				}
			}
			else // si l'heure courante n'est pas comprise entre l'heure de debut et l'heure de fin
			{
				if ($currentValue == "0") 
				{
					echo "==> Vanne deja eteinte <br> \n" ; // si la vanne est deja fermer obn la laisse tel quelle
				} 
				else  //sinon on la ferme
				{
					echo "==> Extinction de la vanne <br> \n" ;
					$sql3 = "INSERT INTO ValeurBroche (id_module, id_broche, saDate, saValeur ) VALUES (".$row{'id_module'}.", ".$row{'id_broche'}.", NOW(),"."0".")" ;
					echo "Sql : <strong>".$sql3."</strong><br> \n" ;
					if ($conn->query($sql3) === TRUE) 
					{
						echo "Record updated successfully <br> \n";
					} 
					else 
					{
						echo "Error updating record: " . $conn->error. "<br> \n";
					}
					// Send informations to module
					//header('Location: sendToModule.php?idModule='.$row{'idModule'}.'&idBroche='.$row{'idBroche'}.'&value='.'0');
				}
			}
		}
		else // si le taux d'humidite est different de NULL 
		{
			echo "<strong>Arrosage par humidite </strong><br> \n";
			if (strtotime($currentHour) >= strtotime($row{'dateDebut'})) // si l'heure de debut est passer
			{
				// récupération de la valeur actuelle du capteur d'humidité
				$currentValueHumidite = file_get_contents("http://87.89.52.252/php/getValueHumidite.php?id_module=2&id_broche=1");
				echo "Current Value humidite :<strong>".$currentValueHumidite." </strong> <br> \n";
				if ($valhumidite >= $currentValueHumidite) // si la valeur demandée est supérieur à la valeur actuelle
				{
					if($currentValue == "1") // si la vanne est deja ouverte
					{
						echo "==> Vanne deja allumee <br> \n" ; // on la laisse allumer
					}
					else // si la vanne est fermer
					{
						echo "==> Allumage de la vanne <br> \n" ; //on l'ouvre
						// Send informations to module
						//header('Location: sendToModule.php?idModule='.$row{'idModule'}.'&idBroche='.$row{'idBroche'}.'&value='.'1');
						$sql3 = "INSERT INTO ValeurBroche (id_module, id_broche, saDate, saValeur ) VALUES (".$row{'id_module'}.", ".$row{'id_broche'}.", NOW(),"."1".")" ;
						echo "Sql : <strong>".$sql3."</strong><br> \n" ;
						if ($conn->query($sql3) === TRUE) 
						{
							echo "Record updated successfully <br> \n";
						} 
						else 
						{
							echo "Error updating record: " . $conn->error. "<br> \n";
						}
					}
				}
				else // si la valeur demandée est superieur à la valeur courante
				{
					if ($currentValue == "0") 
					{ //si la vanne est fermer
						echo "==> Vanne deja eteinte <br> \n" ; // la vanne est deja fermer
					} 
					else // si la vanne est ouverte 
					{
						echo "==> Extinction de la vanne <br> \n" ; // on la ferme
						$sql3 = "INSERT INTO ValeurBroche (id_module, id_broche, saDate, saValeur ) VALUES (".$row{'id_module'}.", ".$row{'id_broche'}.", NOW(),"."0".")" ;
						echo "Sql : <strong>".$sql3."</strong><br> \n" ;
						if ($conn->query($sql3) === TRUE) 
						{
							echo "Record updated successfully <br> \n";
						} else 
						{
							echo "Error updating record: " . $conn->error. "<br> \n";
						}
						// Send informations to module
						//header('Location: sendToModule.php?idModule='.$row{'idModule'}.'&idBroche='.$row{'idBroche'}.'&value='.'0');
					}
				}
			}
			else // si il n'est pas l'heure
			{
				if ($currentValue == "0") 
				{ //si la vanne est fermer
					echo "==> Vanne deja eteinte <br> \n" ; // la vanne est deja fermer
				} 
				else // si la vanne est ouverte 
				{
					echo "==> Extinction de la vanne <br> \n" ; // on la ferme
					$sql3 = "INSERT INTO ValeurBroche (id_module, id_broche, saDate, saValeur ) VALUES (".$row{'id_module'}.", ".$row{'id_broche'}.", NOW(),"."0".")" ;
					echo "Sql : <strong>".$sql3."</strong><br> \n" ;
					if ($conn->query($sql3) === TRUE) 
					{
						echo "Record updated successfully <br> \n";
					} 
					else 
					{
						echo "Error updating record: " . $conn->error. "<br> \n";
					}
					// Send informations to module
					//header('Location: sendToModule.php?idModule='.$row{'idModule'}.'&idBroche='.$row{'idBroche'}.'&value='.'0');
				}
			}
		}
	}
?>