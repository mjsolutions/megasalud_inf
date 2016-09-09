<?php 
include("cpanel/control/connection.php");

function seguridad($entrada){
	global $mysqli;
	return $mysqli->real_escape_string(htmlentities(trim($entrada)));
}

$mail = "De: ".seguridad($_POST["name"])."\r\n";
$mail .= "Email: ".seguridad($_POST["email"])."\r\n";
$mail .= "Asunto: ".seguridad($_POST["subject"])."\r\n";
$mail .= "----------------------------------------------------\r\n\r\n";
			//Mensaje
$mail .= htmlentities($_POST['message']).PHP_EOL.PHP_EOL;
$mail .= "----------------END OF MESSAGE----------------";
			//Titulo
$titulo = "|Sitio web|".seguridad($_POST["subject"]);
			//cabecera
$headers = "MIME-Version: 1.0\r\n"; 
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			//dirección del remitente 
$headers = "From: ".seguridad($_POST["name"])." ".seguridad($_POST["email"])."\r\n";
			//Enviamos el mensaje a la direccion mail 
$bool = mail("martinalanis.dev@gmail.com", $titulo,$mail,$headers);
if($bool){
	echo "Gracias por tu mensaje, en breve nos pondremos en contacto contigo";
}else{
	echo "Algo anda mal... por favor intentalo de nuevo mas tarde";
}

?>