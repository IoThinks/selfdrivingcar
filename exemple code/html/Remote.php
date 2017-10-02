
<!--*************************Entete Serveur ***************************************-->

<head>
	<title>Serveur Raspberry</title>
	<link rel='shortcut icon' 	type='image/x-icon' href='image/favicon.ico' />
	<link rel='icon' 			type='image/x-icon' href='image/favicon.ico'  />
	<link rel='stylesheet' 		type='text/css'		href='css/styles.css'/>
</head>

<body onresize = RestructPage(window.innerWidth)>

	<div id="particles-js"></div>

	<!--**************************** onglet ****************************************-->

	<div class="container menu" id="menu" >
		<ul id="onglets">
			<li><a href="Porte.php"> Porte </a></li>
			<li><a href="Lumiere.php"> Lumiere </a></li>
			<li><a href="Arduino.php"> Arduino </a></li>
			<li><b href="Remote.php"> Remote </b></li>
			<li><a href="Parametres.php"> Parametres </a></li>
		</ul>
	</div>


	<!--**************************** Bouton Remote ***********************************-->

	
		<table class="container" align="center">
			<tr>
				<td colspan=2 align="center">
					<p class='couleur'>Commandes DI.O</p>
				</td>
			</tr>
			<tr>
				<td align='Center'>
					<input type='submit' id='DIO1ON' Value='1' class='myButtonLED' onclick="Envoi(this.id)"></input>
				</td>
				<td align='Center'>
					<input type='submit' id='DIO1OFF' Value='0' class='myButtonLED' onclick="Envoi(this.id)"></input>
				</td>
			</tr>
			<tr>
				<td align='Center'>
					<input type='submit' id='DIO2ON' Value='1' class='myButtonLED' onclick="Envoi(this.id)"></input>
				</td>
				<td align='Center'>
					<input type='submit' id='DIO2OFF' Value='0' class='myButtonLED' onclick="Envoi(this.id)"></input>
				</td>
			</tr>
			<tr>
				<td align='Center'>
					<input type='submit' id='DIO3ON' Value='1' class='myButtonLED' onclick="Envoi(this.id)"></input>
				</td>
				<td align='Center'>
					<input type='submit' id='DIO3OFF' Value='0' class='myButtonLED' onclick="Envoi(this.id)"></input>
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
				<td align="center">
					<p class='couleur'><Font size=3>Association</p>
				</td>
				<td>
					<div class="onoffswitch">
						<input type="checkbox" name="ProgMod" class="onoffswitch-checkbox" id="PrgMod" value="off" onclick="SwitchVal(this.value)">
						<label class="onoffswitch-label" for="PrgMod">
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

function Envoi(valeur)
{	
	var obj = {DIOCmd : valeur}

	$.ajax({
		url: "Traitement.php",
		type:"POST",
		data: obj});
}

function SwitchVal(val)
{
	if (val=="on")
	{
		document.getElementById("PrgMod").value ="off";
		Envoi("ModeNormal");
	}
	else
	{
		document.getElementById("PrgMod").value ="on";
		Envoi("ModeProg");
	}
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

