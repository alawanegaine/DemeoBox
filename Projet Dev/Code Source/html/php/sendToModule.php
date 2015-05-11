<?php
/*--------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin											*/
/*	Role : Ecrit dans "toSend.txt" puis envoi sur le module xBee						*/
/*	Parameter : id module, id broche, value												*/
/* 	Information : Appel de l'executable c++ du module xBee								*/
/*	Sortie : L'execution du binaire c++ pour l'envoi									*/
/*--------------------------------------------------------------------------------------*/
	$myfile = fopen("/var/www/c++/buffers/toSend.txt","w") 
	or die("Unable to open file !");

	$idModule=$_GET['idModule'] ;
	$idBroche=$_GET['idBroche'] ;
	$valueBroche=$_GET['value'];

	$txt = $idModule."_".$idBroche."_".$valueBroche ;

	fwrite($myfile, $txt);

	fclose($myfile);

	echo shell_exec('sudo ./../../c++/sending')."<br>";

?>