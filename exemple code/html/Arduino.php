<?php
/**************************** Lecture Fichier CSV ****************************************/

$row = 1;
if (($handle = fopen("data/Data.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 100, ";")) !== FALSE) {

		if ($data[0]=="AutoOpen") {
			$AutoOpen=$data[1];
		}
		if ($data[0]=="AutoOpenTime") {
			$AutoOpenTime=$data[1];
		}
		if ($data[0]=="AutoClose") {
			$AutoClose=$data[1];
		}
		if ($data[0]=="AutoCloseTime") {
			$AutoCloseTime=$data[1];
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

<body onload="Envoi('Request'),ChargementValeur()" onresize = "RestructPage(window.innerWidth)">

	<div id="particles-js"></div>

	<!--**************************** onglet ****************************************-->

	<div class="container menu" id="menu">
		<ul id="onglets">
			<li><a href="Porte.php"> Porte </a></li>
			<li><a href="Lumiere.php"> Lumiere </a></li>
			<li><b href="Arduino.php"> Arduino </b></li>
			<li><a href="Remote.php"> Remote </a></li>
			<li><a href="Parametres.php"> Parametres </a></li>
		</ul>
	</div>


	<table class="container" id="Tableau" align="center">
		<tr>
			<td align='Center' colspan=2>
				<p class='couleur'>Lumieres</p>
			</td>
		</tr>
		<tr>
			<td align='Center'>
				<button class="myButtonLED" onclick="Envoi('Bureau')"> Bureau </button>
			</td>
			<td align='Center'>
				<button class="myButtonLED" onclick="Envoi('Plafond')"> Plafond </button>
			</td>
		</tr>
		<tr>
			<td align='Center' colspan=2>
				<p class='couleur'>Store</p>
			</td>
		</tr>
		<tr>
			<td align='Center' rowspan=2>
				<input id="SlideBar" type="range" orient="vertical" min="0" max="100" step="2" value="100" name="rating" onmouseup="Store('')"></input>
			</td>
			<td align='Center'>
				<button class="myButtonLED" onclick="Store('+')"> + </button>
			</td>
		</tr>
		<tr>
			<td align='Center'>
				<button class="myButtonLED" onclick="Store('-')"> - </button>
			</td>
		</tr>
		<tr >
			<td align='Center'>
				<button class="myButtonLED" onclick="Envoi('Request')"> Requete </button>
			</td>
		</tr>
		<tr>
			<td>
				<p class='couleur' onclick="Afficher()"><Font size=3>Plus d'options</p>
			</td>
		</tr>	
	</table>

	<table class="container" id="HideTable" align="center" style="display:none">
		<tr>
			<td>
				<div class="onoffswitch">
					<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="AutoOpen" value="<?php echo $AutoOpen?>" onclick="ModifAutoVolet('AutoOpen')" >
					<label class="onoffswitch-label" for="AutoOpen">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</td>
			<td>
				<input type="time" id="AutoOpenTime" onmouseleave="ModifBDD('AutoOpen')">
			</td>
		</tr>
		<tr>
			<td>
				<div class="onoffswitch">
					<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="AutoClose" value="<?php echo $AutoClose?>" onclick="ModifAutoVolet('AutoClose')" >
					<label class="onoffswitch-label" for="AutoClose">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</td>
			<td>
				<input type="time" id="AutoCloseTime" onmouseleave="ModifBDD('AutoClose')">
			</td>
		</tr>
	</table>


</body>

<script src='http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
<script src="js/particles.min.js"></script>
<script src="js/app.js"></script>
<script type="text/javascript">

function ChargementValeur()
{
	if ("<?php echo $AutoOpen?>"=="Activer")
		document.getElementById("AutoOpen").checked ="true";

	if ("<?php echo $AutoClose?>"=="Activer")
		document.getElementById("AutoClose").checked ="true";

	document.getElementById("AutoOpenTime").value= "<?php echo $AutoOpenTime ?>";
	document.getElementById("AutoCloseTime").value= "<?php echo $AutoCloseTime ?>";
}

function Envoi(valeur)
{
	var obj = {valeur}

	$.ajax({
		url: "http://192.168.0.21:80/",
		type:"POST",
		data: obj,
		success : 	function Slide(data)
		{
			if (data.indexOf("Volet")>-1)
			{
				document.getElementById("SlideBar").value=data.substring(5);
			}
		}
	});
}

function Store(valeur)
{
	if (valeur=="+")
	{
		document.getElementById("SlideBar").value = parseInt(document.getElementById("SlideBar").value) + 10;
	}
	else if (valeur=="-")
	{
		document.getElementById("SlideBar").value = parseInt(document.getElementById("SlideBar").value) - 10;
	}
	Envoi("Volet"+document.getElementById("SlideBar").value);
}

function Afficher()
{
	if (document.getElementById("HideTable").style.display =="none")
	{
		document.getElementById("HideTable").style.display ="";
	}
	else
	{
		document.getElementById("HideTable").style.display ="none";
	}
}

function ModifAutoVolet(Horloge)
{
	if (document.getElementById(Horloge).value=="Activer")
	{
		document.getElementById(Horloge).value="Desactiver";
	}
	else 
	{
		document.getElementById(Horloge).value="Activer";
	}

	ModifBDD(Horloge)



}

function ModifBDD(Horloge)
{
	var obj = {
		Horloge: Horloge,
		Valeur: document.getElementById(Horloge).value,
		Time: document.getElementById(Horloge+"Time").value 
	}
	console.log(obj);
	$.ajax({
		url: "Traitement.php",
		type:"POST",
		data: obj});
}

function ChangeHeure (Horloge) 
{
	alert(document.getElementById(Horloge+"Time").value);
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


//		http://192.168.0.27:80/
//		http://192.168.0.9:80/
</script>