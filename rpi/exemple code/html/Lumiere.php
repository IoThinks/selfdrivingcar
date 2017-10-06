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

<body onload="ChargementValeur()" onresize = "RestructPage(window.innerWidth)">

	<div id="particles-js"></div>

	<!--**************************** onglet ****************************************-->

	<div class="container menu" id="menu">
		<ul id="onglets">
			<li><a href="Porte.php"> Porte </a></li>
			<li><b href="Lumiere.php"> Lumiere </b></li>
			<li><a href="Arduino.php"> Arduino </a></li>
			<li><a href="Remote.php"> Remote </a></li>
			<li><a href="Parametres.php"> Parametres </a></li>
		</ul>
	</div>


	<!--**************************** Choix couleur ***********************************-->

	<?php
	$R = (int)substr($RGBValue.toString,0,3);
	$G = (int)substr($RGBValue.toString,3,3);
	$B = (int)substr($RGBValue.toString,6,3);
	?>



	<table class="container" id="Tableau" align="center">
		<tr>
			<td rowspan=2>
				<canvas width="600" height="600" id="canvas_picker" onclick="Envoi(document.getElementById('CodeRGB').value,1)" onmouseleave="SwitchColor()" ></canvas>
			</td>
			<td >
				<p class='couleur'>Random</p>
				<div class="onoffswitch">
					<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="Rand" value="off" onclick="SwitchVal(this.value)">
					<label class="onoffswitch-label" for="Rand">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</td>
		</tr>
		<tr>
			<td align='Center'>
				<p class='couleur'>Dimmer<br>
					<input id="SlideBar" type="range" orient="vertical" min="2" max="100" step="2" value="100" name="rating" onmousemove="Dimmer(this.value)" onmouseup="Envoi(document.getElementById('CodeRGB').value,0)"></input>
				</p>
			</td>
		</tr>
		<tr>
			<td align="center">
				<div  id="test" class="couleur">
					<input id ="ValeurRGB" type="text" disabled="disabled" style="background:rgb(<?php echo $R?>,<?php echo $G?>,<?php echo $B?>);height:60px;"></input>
					<input id ="visuRGB" type="text" disabled="disabled" style="background:rgb(<?php echo $R?>,<?php echo $G?>,<?php echo $B?>);height:60px;"> </input>
				</div>
			</td>
		</tr>
		<tr>
			<td align="center">
				<div class="onoffswitch" >
					<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="EtatLED" onclick="ModifEtat()" >
					<label class="onoffswitch-label" for="EtatLED">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</td>
		</tr>
		<tr>
			<td align="center">
				<input hidden="hidden" id='CodeRGB'>
			</td>
		</tr>
	</table>

</body>




<script src='http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
<script src="js/particles.min.js"></script>
<script src="js/app.js"></script>
<script type="text/javascript">


function SwitchVal(val)
{
	if (val=="on")
		document.getElementById("Rand").value ="off";
	else
	{
		document.getElementById("Rand").value ="on";
		GeneRandom();
	}
}


function GeneRandom()
{
	if (document.getElementById("Rand").value =="on")
	{
		setTimeout(GeneRandom, 3000);
		var x = Random();
		var y = Random();

			// getting image data and RGB values
			var img_data = document.getElementById("canvas_picker").getContext("2d").getImageData(x, y, 1, 1).data;
			var R = img_data[0];
			var G = img_data[1];
			var B = img_data[2];
			
			var Rs=(R+1000).toString();
			var Gs=(G+1000).toString();
			var Bs=(B+1000).toString();
			var ValueRGB = Rs.slice(1,4)+Gs.slice(1,4)+Bs.slice(1,4);
			document.getElementById("visuRGB").style.backgroundColor = "rgb("+R+","+G+","+B+")";
			document.getElementById('CodeRGB').value=ValueRGB;	
			Envoi(document.getElementById('CodeRGB').value,1);
		}
	}

	function ChargementValeur()
	{
		var img = new Image();
		img.src = "image/color-map-icon-hi.png";

		$(img).load(function()
		{
			document.getElementById("canvas_picker").getContext("2d").drawImage(img,0,0);
		});

		if ("<?php echo $RGBState?>"=="on")
		{
			document.getElementById("EtatLED").checked ="true";
			document.getElementById("EtatLED").value ="Allumer";
		}
		else
			document.getElementById("EtatLED").value ="Eteint";

	}
	// Appliquer la couleur par défault quand la souris sort du canvas //
	function SwitchColor()
	{
		document.getElementById("visuRGB").style.backgroundColor =document.getElementById("ValeurRGB").style.backgroundColor;
	}

	function Envoi(RGBCode,change)
	{
		var obj = {	RGBValue: RGBCode}

		$.ajax({
			url: "Traitement.php",
			type:"POST",
			data: obj})

		if (change==1)
		{
			document.getElementById("ValeurRGB").style.backgroundColor = "rgb("+RGBCode.slice(0,3)+","+RGBCode.slice(3,6)+","+RGBCode.slice(6,9)+")";
			document.getElementById("SlideBar").value=100;
		}
	}


	function ModifEtat()
	{

		if (document.getElementById("EtatLED").value=="Allumer")
		{
			document.getElementById("EtatLED").value="Eteint";
		}
		else 
		{
			document.getElementById("EtatLED").value="Allumer";
		}

		var obj = {	RGBState: document.getElementById('EtatLED').value}
		$.ajax({
			url: "Traitement.php",
			type:"POST",
			data: obj});
	}

	function Dimmer(Percent)
	{
		var RGB=document.getElementById("ValeurRGB").style.backgroundColor.replace("rgb(","").replace(")","").split(", ");
		var Rs=(Math.round(RGB[0]*(Percent/100))+1000).toString();
		var Gs=(Math.round(RGB[1]*(Percent/100))+1000).toString();
		var Bs=(Math.round(RGB[2]*(Percent/100))+1000).toString();
		var ValueRGB = Rs.slice(1,4)+Gs.slice(1,4)+Bs.slice(1,4);
		document.getElementById("visuRGB").style.backgroundColor = "rgb("+Rs.slice(1,4)+","+Gs.slice(1,4)+","+Bs.slice(1,4)+")";

		document.getElementById('CodeRGB').value=ValueRGB;
	}



	function Random()
	{
		return Math.floor(Math.random()*600);
	}

	$("#canvas_picker").mousemove(function(event){
		// getting user coordinates
		var x = event.pageX - document.getElementById("Tableau").offsetLeft;
		var y = event.pageY - document.getElementById("Tableau").offsetTop;
		// getting image data and RGB values
		var img_data = document.getElementById("canvas_picker").getContext("2d").getImageData(x, y, 1, 1).data;
		var R = img_data[0];
		var G = img_data[1];
		var B = img_data[2];
		
		var Rs=(R+1000).toString();
		var Gs=(G+1000).toString();
		var Bs=(B+1000).toString();
		var ValueRGB = Rs.slice(1,4)+Gs.slice(1,4)+Bs.slice(1,4);
		document.getElementById("visuRGB").style.backgroundColor = "rgb("+R+","+G+","+B+")";
		document.getElementById('CodeRGB').value=ValueRGB;
		

	});
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