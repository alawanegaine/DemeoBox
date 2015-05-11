<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : Récupération des status des broches de type INTERRUPTEUR						*/
/*	Parameter : id module																*/
/* 	Information : Si id module nul, on retourne toutes les broches						*/
/*	Sortie : Création d'un "swipe" (jquery) par broche									*/
/*--------------------------------------------------------------------------------------*/
	$settings = parse_ini_file("../config/config.ini", TRUE);

	$username = $settings['database']['user'];
	$password = $settings['database']['mdp'];
	$hostname = $settings['server']['URL']; 
	$dbname = $settings['database']['db'];

	//connection to the database
	$dbhandle = mysql_connect($hostname, $username, $password) 
	 or die("Unable to connect to MySQL");
	//echo "Connected to MySQL<br>";

	//select a database to work with
	$selected = mysql_select_db("demeobox",$dbhandle) 
	  or die("Could not select examples");
	//echo "Database selected : $dbname <br>" ;

	$idModule = $_POST['idModule'];
	//echo "Id module recu : ".$idModule."<br>" ;

	// si un un id module est reçu
	if ($idModule == "") {
		$sql = "SELECT * FROM BrocheModule WHERE sonType='INTERRUPTEUR'" ;
	} else {
		$sql = "SELECT * FROM BrocheModule WHERE sonType='INTERRUPTEUR' AND id_module = ".$idModule ;
	}

	//echo "SQL : ".$sql."<br>";

	//execute the SQL query and return records
	$result = mysql_query($sql);

    echo "<script src=\"./Ressources/jquery.mobile-1.4.4/jquery.mobile-1.4.4.min.js\"></script>";
    echo "<ul>";

	//fetch tha data from the database 
	while ($row = mysql_fetch_array($result)) {

		// récupération de la dernière valeur connue pour la broche
		$sql2 = "SELECT DISTINCT saValeur,saDate FROM ValeurBroche WHERE id_module=".$row{'id_module'}." AND id_broche=".$row{'id_broche'}." ORDER BY saDate DESC LIMIT 1" ;
		//echo "SQL 2 : ".$sql2."<br>";

		$result2 = mysql_query($sql2);

		$row1 = mysql_fetch_array($result2);
		$value = $row1{'saValeur'};

		// changement de l'état du swipe en fonction de la valeur
		if($value == 1)
		{
			$isOnSelected = "selected" ;
			$isOffSelected = "";
		}
		else
		{
			$isOnSelected = "" ;
			$isOffSelected = "selected";
		}

		// ajout de données en option pour pouvoir les récupérer dans la page html
		$dataOptions= "\"idModule\":\"".$row{'id_module'}."\",\"idBroche\":\"".$row{'id_broche'}."\"" ;
		//echo $dataOptions;

		// création du swipe button
		echo "<li>" ;
          echo "<form>" ;
            echo "<label for=\"idModule=".$row{'id_module'}."&idBroche=".$row{'id_broche'}."\">".$row{'saDescription'}." :"."</label>" ;
              echo "<select id=\"".$row{'id_module'}."_".$row{'id_broche'}."\" data-role=\"slider\" data-options={".$dataOptions."}>";
                echo "<option ".$isOffSelected." value=\"off\">Off</option>";
                echo "<option ".$isOnSelected." value=\"on\">On</option>" ;
              echo "</select>";
          echo "</form>";
       echo "</li>" ;
	}

	echo "</ul>";

	//close the connection
	mysql_close($dbhandle);
?>
