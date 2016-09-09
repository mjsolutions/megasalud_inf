<?php
	global $mysqli;
	//sustituye los valores de usuario, contraseña y base de datos a los valores que tengas de tu base de datos
	$mysqli=new mysqli("kamuhro.ipowermysql.com","adminmega","adminmega","ventas");
	if($mysqli->errno){
		echo "<h1>Fallo la conexión con la base de datos";
	}
	$mysqli->set_charset("utf8");
	global $mysqli;
?>