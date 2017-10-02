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
		if ($data[0]=="RGBValue"){
			$RGBValue=$data[1];
		}
		if ($data[0]=="Angle"){
			$Angle=$data[1];
		}
		$row++;
	}
	fclose($handle);
}
?>

<!--*************************Entete Serveur ***************************************-->

<head>
	<title>Serveur Raspberry</title>
	<link rel='shortcut icon' 	type='image/x-icon' href='image/favicon.ico' />
	<link rel='icon' 			type='image/x-icon' href='image/favicon.ico'  />
	<link rel='stylesheet' 		type='text/css'		href='css/styles.css'/>
</head>

<body onresize = "RestructPage(window.innerWidth)">

	<div id="particles-js"></div>

	<!--**************************** onglet ****************************************-->

<div class="container menu" id="menu">
	<ul id="onglets">
		<li><b href="Porte.php"> Porte </b></li>
		<li><a href="Lumiere.php"> Lumiere </a></li>
		<li><a href="Arduino.php"> Arduino </a></li>
		<li><a href="Remote.php"> Remote </a></li>
		<li><a href="Parametres.php"> Parametres </a></li>
	</ul>
</div>


<!--**************************** Bouton Cycle ***********************************-->


<table class="container" align="center">
	<tr>
		<td colspan=3 align="center">
			<p class='couleur'>Commandes Porte</p>
		</td>
	</tr>
	<tr>
		<td align='Center'>
			<input type='submit' id='Cycle' Value='Cycle' class='myButtonLED' onclick="Envoi(this.value),timer()"></input>
		</td>
		<td align='Center'>
			<input type='submit' id='Ouvrir' Value='Ouvrir' class='myButtonLED' style='border-color:Green;' onclick="Envoi(this.value)"></input>
		</td>
		<td align='Center'>
			<input type='submit' id='Fermer' Value='Fermer' class='myButtonLED' style='border-color:Red;' onclick="Envoi(this.value)"></input>
		</td>
	</tr>
	<tr>
		<td colspan=3>
			<p class='couleur' onclick="Afficher()"><Font size=3>Plus d'options</p>
		</td>
	</tr>	

	<tr id="regulation" style="display:none">
		<td align="center">
			<p class='couleur'>
				<span id="Valeur" ></span> 
				<span id="ValeurInstant">50</span>
			</p>
		</td>
		<td>
			<input id="SlideBar" type="range" min="0" max="135" step="5" name="rating" onmouseup="ModificationAngle(this.value)"></input>
		</td>
		<td>
			<div class="onoffswitch">
				<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ButtonRegulation" value="<?php echo $Regulation?>" onclick="ModeRegulation()" >
				<label class="onoffswitch-label" for="ButtonRegulation">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>

			</div>
		</td>
	</tr>
</table>
</body>

<script src='http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
<script src="js/particles.min.js"></script>
<script src="js/app.js"></script>
<script type="text/javascript">
function timer()
{
	document.getElementById("Cycle").style.display=None;
	var t= <?php echo $Time ?> ;
	setTimeout(function()
	{
		document.getElementById("Cycle").style.display="block";
	} ,t*1000+5000);
}

function Envoi(valeur)
{	
	var obj = {Porte : valeur}

	$.ajax({
		url: "Traitement.php",
		type:"POST",
		data: obj});
}

function Afficher()
{
	if (document.getElementById("regulation").style.display =="none")
	{
		document.getElementById("regulation").style.display ="";
	}
	else
	{
		document.getElementById("regulation").style.display ="none";
	}

}

function EnvoiValeurAngle(AngleModifier)
{
	var obj = {Angle: AngleModifier}
	$.ajax({
		url: "Traitement.php",
		type:"POST",
		data: obj});
}

function ChargementValeur()
{	
	document.getElementById("ValeurInstant").innerHTML= <?php echo $Angle ?> ;
	document.getElementById("ValeurInstant").value= <?php echo $Angle ?> ;
	document.getElementById("SlideBar").value= <?php echo $Angle ?> ;
	document.getElementById("Valeur").value= <?php echo $Angle ?> ;
	if ("<?php echo $Regulation?>"=="On")
		document.getElementById("ButtonRegulation").checked ="true";
}

function ModeRegulation()
{
	if (document.getElementById("ButtonRegulation").value=="On")
	{
		document.getElementById("ButtonRegulation").value="Off";
	}
	else 
	{
		document.getElementById("ButtonRegulation").value="On";
	}
	var obj = { Regulation: document.getElementById('ButtonRegulation').value}
	$.ajax({
		url: "Traitement.php",
		type:"POST",
		data: obj});
}

function showValue(newValue)
{
	document.getElementById("range").innerHTML=newValue;
}

function ModificationAngle(AngleModifier)
{
	document.getElementById("ValeurInstant").innerHTML= AngleModifier;
	document.getElementById("ValeurInstant").value= AngleModifier ;

	if (document.getElementById("ValeurInstant").value!=document.getElementById("Valeur").value)
	{
		document.getElementById("Valeur").value=document.getElementById("ValeurInstant").value;
		EnvoiValeurAngle(document.getElementById("Valeur").value);
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

