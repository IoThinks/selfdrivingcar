<?php
	$donnee=file_get_contents("data/Data.csv");	

	//Regulation ON/OFF
	if (isset($_POST['Regulation']))
	{
		$donnee=ModificationValeur($donnee,"Regulation",$_POST['Regulation']);
		echo $donnee;
	}

	//gestion de l'angle de la porte
	if (isset($_POST['Angle']))
	{
		$donnee=ModificationValeur($donnee,"Angle",$_POST['Angle']);
		echo $donnee;
	}

	//gestion des capteurs
	if (isset($_POST['Capteur']))
	{
		$donnee=ModificationValeur($donnee,$_POST['Capteur'],$_POST['Valeur']);
	}

	//modifications des parametres de la porte
	if (isset($_POST['Max']))
	{
		$donnee=ModificationValeur($donnee,"Max",$_POST['Max']);
		$donnee=ModificationValeur($donnee,"Min",$_POST['Min']);
		$donnee=ModificationValeur($donnee,"Delta",$_POST['Delta']);
		$donnee=ModificationValeur($donnee,"Time",$_POST['Time']);
	}

	//gestion Valeur RGB
	if (isset($_POST['RGBValue']))
	{
		$donnee=ModificationValeur($donnee,"RGBValue",$_POST['RGBValue']);
		echo $_POST['RGBValue'];
	}

	//gestion Alumage LED
	if (isset($_POST['RGBState']))
	{
		$donnee=ModificationValeur($donnee,"RGBState",$_POST['RGBState']);
		echo $donnee;
	}

	//gestion de la porte
	if (isset($_POST['Porte']))
	{
		$donnee=ModificationValeur($donnee,"Cycle Porte",$_POST['Porte']);
	}

	//Wake On LAN
	if (isset($_POST['WOL']))
	{
		exec("wakeonlan 50:46:5D:6B:07:9C");
		#exec("wakeonlan DC:85:DE:7C:FE:B9");
	}

	if (isset($_POST['Horloge']))
	{
		$donnee=ModificationValeur($donnee,$_POST['Horloge'],$_POST['Valeur']);
		$donnee=ModificationValeur($donnee,$_POST['Horloge']."Time",$_POST['Time']);
	}

	//gestion des prises DIO
	if (isset($_POST['DIOCmd']))
	{
		$donnee=ModificationValeur($donnee,"DIOCmd",$_POST['DIOCmd']);
	}
		

	//enregisterment de la valeur dans le fichier CSV
	if ($donnee!=file_get_contents("data/Data.csv") and $donnee!=NULL)
	{

		EnregistrementCSV($donnee);
	}	


	function ModificationValeur($contenu,$categorie,$valeur)
	{
		//recherche du début et la fin de la valeur a modifier
		$debut=stripos($contenu,";",stripos($contenu,$categorie))+1;
		$fin=stripos($contenu,";",$debut);
		//mise a jour de la valeur
		$update=substr_replace($contenu, $valeur, $debut,($fin-$debut));

		return $update;
	}

	function EnregistrementCSV($enregistrement)
	{	
		
		$fichier=fopen("data/Data.csv","w+");
		fwrite($fichier,$enregistrement);
		fclose($fichier);
		echo $enregistrement;
		
	}
?>
