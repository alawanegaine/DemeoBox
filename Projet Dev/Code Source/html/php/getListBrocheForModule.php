<?php
/*--------------------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin														*/
/*	Role : Récupération de toutes les broches du module												*/
/*	Parameter : id module, yes/no pour désactivé l'option si pin affectée à un capteur/interrupteur	*/
/*	Sortie : Ajout d'une "option" (ligne dans tableList dans JQuery) par broche						*/
/*--------------------------------------------------------------------------------------------------*/

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
	  or die("Could not select database");
	//echo "Database selected : $dbname <br>" ;

	$idModule = $_POST['idModule'];
	//echo "Id module recu : ".$idModule."<br>" ;

	if ($_POST['disabledForAlreadyTaken'] == "yes") {
		$disabledForAlreadyTaken = "disabled=\"disabled\"" ;
	}

	// get how many broche are there
	$sql = "SELECT nbBroche FROM Module WHERE id_module=".$idModule ;

	$sql2 = "SELECT * FROM BrocheModule WHERE id_module=".$idModule." AND id_broche=" ;

	//execute the SQL query and return records
	$nbBrocheMax = mysql_query($sql);

	$row = mysql_fetch_array($nbBrocheMax);

	//fetch tha data from the database 
	for($i=1 ; $i <= $row{'nbBroche'} ; $i++ ) {

		$result = mysql_query($sql2.$i);
		$row1 = mysql_fetch_array($result);

		// si une broche est affectée
		if($row1 > 0) {
			echo "<option value=\"".$i."\" ".$disabledForAlreadyTaken.">".$i."   --   ".$row1{'saDescription'}."</option>" ;
		}
		else {
			echo "<option value=\"".$i."\">".$i."</option>" ;
		}
	}

	//close the connection
	mysql_close($dbhandle);
?>
