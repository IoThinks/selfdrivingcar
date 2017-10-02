<!DOCTYPE html>
<html>
<!--**************************** Entete Serveur ****************************************-->

<head>
	<title>Serveur Raspberry</title>
	<link rel='shortcut icon' 	type='image/x-icon' href='image/favicon.ico' />
	<link rel='icon' 			type='image/x-icon' href='image/favicon.ico'  />
	<link rel='stylesheet' 		href='css/styles.css'/>
</head>

<body onload="WOL()" style="margin:0;">

	<div id="loader"></div>

	<div  style="display:none;" id="myDiv" class="animate-bottom">

		<h2 class='couleur'>SUCCESS!</h2>
		<p class='couleur'>L'ordinateur est allum&eacute;</p>
	</div>

</body>

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript">

function WOL()
{
	$.ajax({
		url: "Traitement.php",
		type:"POST",
		data:"WOL=1",
		success : 	function showPage() {
			document.getElementById("loader").style.display = "none";
			document.getElementById("myDiv").style.display = "block";
			setTimeout(function(){document.location.href="Parametres.php"}, 2000);
		}
	});
}

</script>


</html>
