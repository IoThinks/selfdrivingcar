<?php
/**************************** Lecture Fichier CSV ****************************************/

$row = 1;
if (($handle = fopen("data/Data.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 100, ";")) !== FALSE) {

		if ($data[0]=="Cycle Porte") {
			$CyclePorte=$data[1];
		}
		if ($data[0]=="Capteur Exterieur"){
			$CapteurExt=$data[1];
		}
		if ($data[0]=="Capteur Interieur"){
			$CapteurInt=$data[1];
		}
		if ($data[0]=="Max"){
			$Max=$data[1];
		}
		if ($data[0]=="Min"){
			$Min=$data[1];
		}
		if ($data[0]=="Delta"){
			$Delta=$data[1];
		}
		if ($data[0]=="Time"){
			$Time=$data[1];
		}
		if ($data[0]=="RGBState"){
			$RGBState=$data[1];
		}
		if ($data[0]=="RGBvalue"){
			$RGBvalue=$data[1];
		}
		if ($data[0]=="Angle"){
			$Angle=$data[1];
		}
		$row++;
	}
	fclose($handle);
}
?>

<!--**************************** Entete Serveur ****************************************-->

<head>
	<title>Serveur Raspberry</title>
	<link rel='shortcut icon' 	type='image/x-icon' href='image/favicon.ico' />
	<link rel='icon' 			type='image/x-icon' href='image/favicon.ico'  />
	<link rel='stylesheet' 		type='text/css'		href='css/styles.css'/>
</head>

<body onload="ChargementValeur()" onresize = "RestructPage(window.innerWidth)">

	<div id="particles-js"></div>

	<!--**************************** onglet ****************************************-->

	<div class="container menu" id="menu">
		<ul id="onglets">
			<li><a href="Porte.php"> Porte </a></li>
			<li><a href="Lumiere.php"> Lumiere </a></li>
			<li><a href="Arduino.php"> Arduino </a></li>
			<li><a href="Remote.php"> Remote </a></li>
			<li><b href="Parametres.php"> Parametres </b></li>
		</ul>
	</div>

	<!--**************************** Bouton On/OFF ***********************************-->

	<table class="container" align="center">
		<tr>
			<td class="couleur" colspan=2 align="center">
				Capteur ultrason		
			</td>
		</tr>

		<tr>
			<td class="couleur">
				Exterieur :
			</td>
			<td>
				<div class="onoffswitch">
					<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="Capteur_Exterieur" value="<?php echo $CapteurExt?>" onclick="ModifCapteur("Capteur Exterieur")" >
					<label class="onoffswitch-label" for="Capteur_Exterieur">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<p class="couleur">Interieur : </p>	
			</td>
			<td>
				<div class="onoffswitch">
					<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="Capteur Interieur" value="<?php echo $CapteurInt?>" onclick="ModifCapteur("Capteur Interieur")" >
					<label class="onoffswitch-label" for="Capteur Interieur">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</td>
		</tr>
		<tr>	
			<td>
				<br></br>
			</td>
		</tr>
		<tr>	
			<td colspan=2 align="center">
				<p class='couleur'>Parametres</p>			
			</td>
		</tr>

		<tr>
			<td class="couleur" align="right">
				Maximum :
			</td>
			<td>
				<input type="Number" style="width:50px;" onchange="VerificationMax(this.value)" value="<?php echo $Max ?>" id="Maximum">
			</td>
		</tr>
		<tr>
			<td class="couleur" align="right">
				Minimum  :
			</td>
			<td>
				<input type="Number" style="width:50px;" onchange="VerificationMin(this.value)" value ="<?php echo $Min?>" id = "Minimum">
			</td>
		</tr>
		<tr>
			<td class="couleur" align="right">
				Delta :
			</td>
			<td>
				<input type="Number" style="width:50px;" value ="<?php echo $Delta?>" id = "Delta">
			</td>
		</tr>
		<tr>
			<td class="couleur" align="right">
				Tempo :		
			</td>
			<td>
				<input type="Number" style="width:50px;" value ="<?php echo $Time?>" id = "Tempo">
			</td>
		</tr>
		<tr>
			<td class="couleur" align="right">
				Valider :	
			</td>
			<td>
				<input type= "submit" onclick="Envoi()">
			</td>
		</tr>
		<tr>	
			<td>
				<br></br>
			</td>
		</tr>
		<tr>	
			<td colspan=2 align="center">
				<p class='couleur'>Wake On Lan</p>			
			</td>
		</tr>
		<td colspan=2 align="center" >
			<input type= "submit" onclick="self.location.href='WOL.php'">
		</td>
	</tr>

	<tr>
		<td>
			<p class="couleur" onclick="Afficher()">
				<Font size=3>Etat de la bdd
				</p>
			</td>
		</tr>
	</table>



	<table class="container" align="center" id="Tableau2" align="center" style="display:none;color:white;text-shadow: 1px 0 0 #000, 0 -1px 0 #000, 0 1px 0 #000, -1px 0 0 #000;">
		<tr>
			<td >
				Capteur ultrason exterieur : <?php echo $CapteurExt; ?>
			</td>
		</tr>
		<tr>
			<td >
				Capteur de mouvement interieur : <?php echo $CapteurInt; ?>
			</td>
		</tr>
		<tr>
			<td >
				Etat de la porte : <?php echo $CyclePorte; ?>
			</td>
		</tr>
		<tr>
			<td >
				Valeur maximum : <?php echo $Max; ?>
			</td>
		</tr>
		<tr>
			<td >
				Valeur minimum : <?php echo $Min; ?>
			</td>
		</tr>
		<tr>
			<td >
				Valeur Delta : <?php echo $Delta; ?>
			</td>
		</tr>
		<tr>
			<td >
				Valeur Timer : <?php echo $Time; ?>
			</td>
		</tr>
		<tr>
			<td >
				Etat RGB : <?php echo $RGBState; ?>
			</td>
		</tr>
		<tr>
			<td >
				Valeur RGB : <?php echo $RGBvalue; ?>
			</td>
		</tr>
		<tr>
			<td >
				Angle : <?php echo $Angle; ?>
			</td>	
		</tr>
	</table>


</body>



<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="js/particles.min.js"></script>
<script src="js/app.js"></script>
<script type="text/javascript">

function ChargementValeur()
{
	if ("<?php echo $CapteurExt?>"=="Activer")
		document.getElementById("Capteur Exterieur").checked ="true";

	if ("<?php echo $CapteurInt?>"=="Activer")
		document.getElementById("Capteur Interieur").checked ="true";

}

function VerificationMax(valeur)
{
	if (valeur > "690")
		document.getElementById("Maximum").value="690";
}

function VerificationMin(valeur)
{
	if (valeur < "1")
		document.getElementById("Minimum").value="1";
}

function Envoi()
{
	var obj = {	Max: document.getElementById("Maximum").value,
	Min: document.getElementById("Minimum").value,
	Delta: document.getElementById("Delta").value,
	Time: document.getElementById("Tempo").value}
	$.ajax({
		url: "Traitement.php",
		type:"POST",
		data: obj
	});
}

function ModifCapteur(Capteur)
{
	if (document.getElementById(Capteur).value=="Activer")
	{
		document.getElementById(Capteur).value="Desactiver";
	}
	else 
	{
		document.getElementById(Capteur).value="Activer";
	}

	var obj = {	Capteur: Capteur,
		Valeur: document.getElementById(Capteur).value}
		window.alert(obj);
		$.ajax({
			url: "Traitement.php",
			type:"POST",
			data: obj});

}

function Afficher()
{
	if (document.getElementById("Tableau2").style.display =="none")
	{
		document.getElementById("Tableau2").style.display ="";
	}
	else
	{
		document.getElementById("Tableau2").style.display ="none";
	}
}

function RestructPage(size)
{
	if (size<=905)
	{
		document.getElementById("menu").style.padding = "10px 10px 118px 10px";
	}
	else
	{
		document.getElementById("menu").style.padding = "10px 10px 60px 10px";
	}	
}
</script>