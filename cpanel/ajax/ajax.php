<?php
	// include("../control/connection.php");
	include("../control/security.php");
	//se incluyen los archivos de coneccción a la base de datos y el de seguridad
	//TODA VARIABLE RECIBIDA A ESTE ARCHIVO DEBE PASARSE POR LA FUNCIÓN seguridad(variable)
	switch(seguridad($_GET["opcion"])){
		case 1://enviar email
			//Datos generales:
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
			// $headers = "MIME-Version: 1.0\r\n"; 
			// $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			//dirección del remitente 
			$headers = "From: ".seguridad($_POST["name"])." ".seguridad($_POST["email"])."\r\n";
			//Enviamos el mensaje a la direccion mail 
			$bool = mail("martinalanis.dev@gmail.com", $titulo,$mail,$headers);
			if($bool){
			    echo "Gracias por tu mensaje, en breve nos pondremos en contacto contigo";
			}else{
			    echo "Algo anda mal... por favor intentalo de nuevo mas tarde";
			}
		break;
		case 2:
			//sesión
			if(isset($_POST['usuario'])&&isset($_POST['contrasena'])){
				$usuario=seguridad($_POST['usuario']);
				$contrasena=seguridad($_POST['contrasena']);
				$contrasena=hash('sha256', md5($contrasena));
				$resu=$mysqli->query("SELECT id,tipo FROM usuario WHERE correo='{$usuario}' AND estado='1'")or die("Error en:".$mysqli->error); //verificar que el usuario este bien
				$resu2=$mysqli->query("SELECT id,tipo FROM usuario WHERE correo='{$usuario}' AND contrasena='{$contrasena}' AND estado=1")or die("Error en:".$mysqli->error);//verificar que la contraseña este bien
				$num=$resu2->num_rows;
				if($resu){//usuario existe
					if($resu2&&$num>0){
						$id=$resu2->fetch_array(MYSQLI_ASSOC); //guarda el array de la columna en la variable $id
						crea_sesion($id);
						echo "success";
						// var_dump($_SESSION);
						}else{
							echo "error_2"; // contraseña mal
						}
					}
					else{
						echo "error_1"; // usuario mal
					}
			}
			else{
				echo "error_0";//no fueron declaradas las variables
			}
		break;
		case 3:
			//actualizar tabla de listado de usuarios
			//codigo para consulta
			session_start();
			$resultado=$mysqli->query("SELECT * FROM usuario");
			while($row=$resultado->fetch_array(MYSQLI_ASSOC)){
				$nombre = $row['nombre']." ".$row['apellido_p']." ".$row['apellido_m'];
				$tipo = "";
				$tipo_m = "";
				$row['tipo']==1?$tipo_m = "Adm." : $tipo_m = "Emp.";
				$estado = "";
				$row['tipo']==1?$tipo = "Administrador" : $tipo = "Empleado";
				$row['estado']==1?$estado = "<span class='glyphicon glyphicon-ok'></span>" : $estado = "<span class='glyphicon glyphicon-remove'></span>";
				if($row['id'] == $_SESSION['user']['id']) {$disabled = "disabled='disabled'";}else{$disabled = "";}

				echo "<tr>
						<td class='hidden-xs'>{$row['id']}</td>
						<td>".$nombre."</td>
						<td class='hidden-xs'>{$row['correo']}</td>
						<td>".$tipo."</td>
						<td class='visible-xs'>".$tipo_m."</td>
						<td>".$estado."</td>
						<td><button type='button' class='btn-danger btn' onclick=baja(\"{$row['id']}\") title='Eliminar' ".$disabled."><span class='glyphicon glyphicon-remove'></span></button>
							<button type='button' class='btn-success btn' onclick=editar(\"{$row['id']}\") title='Editar'><span class='glyphicon glyphicon-edit'></span></button>
							<button type='button' class='btn-warning btn' onclick=cambiarPass(\"{$row['id']}\") title='Cambiar contraseña'><span class='glyphicon glyphicon-lock'></span></button></td>
					</tr>";
			}
		break;
		case 4:
		//Alta de nuevo usuario
			if(isset($_POST['nombre'])&&isset($_POST['correo'])&&isset($_POST['password1'])){
				$nombre=seguridad($_POST['nombre']);
				$apellido_p=seguridad($_POST['apellido_p']);
				$apellido_m=seguridad($_POST['apellido_m']);
				$correo=seguridad($_POST['correo']);
				$tipo=seguridad($_POST['tipo']);
				$pass=seguridad($_POST['password1']);
				$pass=hash('sha256', md5($pass));
				$resultado=$mysqli->query("INSERT INTO usuario(nombre, apellido_p,apellido_m,correo,contrasena,tipo,estado) VALUES('{$nombre}','{$apellido_p}','{$apellido_m}','{$correo}','{$pass}','{$tipo}','1')")or die("error en: ".$mysqli->error);
				if($resultado){
					echo "success";
				}
				else{
					echo "error_2";//error de alta
				}
			}
			else{
				echo "error_1";
			}
		break;
		case 5:
		//Baja de usuario
			if(isset($_POST['id'])){
				$id=seguridad($_POST['id']);
				$resultado=$mysqli->query("DELETE FROM usuario WHERE id ='{$id}'")or die("Error en: ".$mysqli->error);
				if($resultado){
					echo "success";
				}
				else{
					echo "error_1";
				}
			}
			else{
				echo "error_2";
			}
		break;
		case 6:
			//Regresa array con los datos que se desean editar del usuario
			$id=seguridad($_POST['id']);
			$resultado=$mysqli->query("SELECT * FROM usuario WHERE id = '{$id}'")or die("error en: ".$mysqli->error);
			echo json_encode($resultado->fetch_array(MYSQLI_ASSOC));
		break;
		case 7:
		//Update de usuario
			$id=seguridad($_POST['id']);
			$nombre=seguridad($_POST['nombre']);
			$apellido_p=seguridad($_POST['apellido_p']);
			$apellido_m=seguridad($_POST['apellido_m']);
			$correo=seguridad($_POST['correo']);
			$tipo=seguridad($_POST['tipo']);
			$estado=seguridad($_POST['estado']);

			$resultado=$mysqli->query("UPDATE usuario SET nombre = '{$nombre}', apellido_p = '{$apellido_p}', apellido_m = '{$apellido_m}', tipo = '{$tipo}', estado = '{$estado}' WHERE id = '{$id}'")or die("error en: ".$mysqli->error);

			if($resultado){
				echo "success";
			}
		break;
		case 8:
		//cambiar contraseña
			$id=seguridad($_POST['id']);
			$contrasena=seguridad($_POST['password1']);
			$contrasena=hash('sha256', md5($contrasena));
			$contrasena2=seguridad($_POST['password']);
			$contrasena2=hash('sha256', md5($contrasena2));
			$resu=$mysqli->query("SELECT contrasena FROM usuario WHERE id='{$id}'")or die("Error en:".$mysqli->error);
			$aux = $resu->fetch_array(MYSQLI_ASSOC);
			if($aux['contrasena'] == $contrasena){
				$resultado=$mysqli->query("UPDATE usuario SET contrasena = '{$contrasena2}' WHERE id = '{$id}'")or die("error en: ".$mysqli->error);

				if($resultado){
					echo "success";
				}

			}else{
				echo "La contraseña proporcionada no coincide";
			}
		break;
		case 9:
			//Alta de reportes de servicio
			if(isset($_POST['folio'])&&isset($_POST['nombre'])&&isset($_POST['descripcion'])){
				$nombre=seguridad_utf8($_POST['nombre']);
				$folio=seguridad_utf8($_POST['folio']);
				$status=seguridad_utf8($_POST['estatus']);
				$fecha=seguridad($_POST['fecha']);
				$descripcion=seguridad_utf8($_POST['descripcion']);

				$resultado=$mysqli->query("INSERT INTO reportes(nombre, folio, descripcion, status, fecha) VALUES('{$nombre}','{$folio}','{$descripcion}','{$status}','{$fecha}')")or die("error en: ".$mysqli->error);
				if($resultado){
					echo "success";
				}
				else{
					echo "error_2";//error de alta
				}
			}
			else{
				echo "error_1";
			}
		break;
		case 10:
		//Listado de reportes de servicios
			if(isset($_POST['tipo'])) {

			if(seguridad($_POST['tipo']) == "all"){
				$query = "SELECT * FROM reportes";
			}else if(seguridad($_POST['tipo']) == "activos"){
				$query = "SELECT * FROM reportes WHERE activo = 1";
			}else if(seguridad($_POST['tipo']) == "inactivos"){
				$query = "SELECT * FROM reportes WHERE activo = 0";
			}else {
				$aux = seguridad($_POST['tipo']);
				$query = "SELECT * FROM reportes WHERE folio = '{$aux}' OR nombre LIKE '%$aux%' OR status LIKE '%$aux%' order by id DESC";
				// echo $aux;
			}

			$resultado=$mysqli->query($query);

				if($resultado->num_rows > 0){
					while($row=$resultado->fetch_array(MYSQLI_ASSOC)){
						$folio = seguridad_decode($row['folio']);
						$nombre = seguridad_decode($row['nombre']);
						$status = seguridad_decode($row['status']);
						$descripcion = seguridad_decode($row['descripcion']);
						$descripcion = substr($descripcion, 0, 80);

						$row['activo']==1?$estado = "<span class='glyphicon glyphicon-ok'></span>" : $estado = "<span class='glyphicon glyphicon-remove'></span>";

						echo "<tr>
								<td>".$folio."</td>
								<td>".$nombre."</td>
								<td class='hidden-xs'>".$descripcion."</td>
								<td class='hidden-xs'>".$status."</td>
								<td class='hidden-xs'>".$estado."</td>
								<td><button type='button' class='btn-danger btn' onclick=baja(\"{$row['id']}\") title='Eliminar'><span class='glyphicon glyphicon-remove'></span></button>
									<button type='button' class='btn-success btn' onclick=editar(\"{$row['id']}\") title='Editar'><span class='glyphicon glyphicon-edit'></span></button>
									<button type='button' class='btn-primary btn' onclick=ver(\"{$row['id']}\") title='Cambiar contraseña'><span class='glyphicon glyphicon-eye-open'></span></button></td>
							</tr>";
					}
				}else{
					echo "No hay resultados";					
				}
			}
		break;
		case 11:
		//Regresar listado para editar
			$id=seguridad($_POST['id']);
			$resultado=$mysqli->query("SELECT * FROM reportes WHERE id = '{$id}'")or die("error en: ".$mysqli->error);
			$row = $resultado -> fetch_array(MYSQLI_ASSOC);
			$res['nombre'] = seguridad_decode($row['nombre']);
			$res['folio'] = seguridad_decode($row['folio']);
			$res['descripcion'] = seguridad_decode($row['descripcion']);
			$res['status'] = seguridad_decode($row['status']);
			$res['activo'] = $row['activo'];
			$res['fecha'] = $row['fecha'];
	
			echo json_encode($res);
		break;
		case 12:
		//Update de reporte de servicio
			//Alta de reportes de servicio
			if(isset($_POST['folio'])&&isset($_POST['id'])&&isset($_POST['descripcion'])){
				$id=seguridad($_POST['id']);
				$nombre=seguridad_utf8($_POST['nombre']);
				$folio=seguridad_utf8($_POST['folio']);
				$status=seguridad_utf8($_POST['estatus']);
				$fecha=seguridad($_POST['fecha']);
				$activo=seguridad($_POST['activo']);
				$descripcion=seguridad_utf8($_POST['descripcion']);

				$resultado=$mysqli->query("UPDATE reportes SET nombre ='{$nombre}', folio ='{$folio}' , descripcion='{$descripcion}', status ='{$status}', activo = '{$activo}', fecha ='{$fecha}' WHERE id = '{$id}'")or die("error en: ".$mysqli->error);
				
				if($mysqli->affected_rows >= 0){
					echo "success";
				}
				else{
					echo "error_2";//error de alta
				}
			}
			else{
				echo "error_1";
			}
		break;
		case 13:
			//Regresar listado para modal ver
			$id=seguridad($_POST['id']);
			$resultado=$mysqli->query("SELECT * FROM reportes WHERE id = '{$id}'")or die("error en: ".$mysqli->error);
			$row = $resultado -> fetch_array(MYSQLI_ASSOC);
			$nombre = seguridad_decode($row['nombre']);
			$folio = seguridad_decode($row['folio']);
			$descripcion = seguridad_decode2($row['descripcion']);
			$status = seguridad_decode($row['status']);
			$row['activo']==1 ? $activo = "Activo" : $activo = "Inactivo"; ;
			$fecha = $row['fecha'];

			echo "<div class='col-md-12 spadding10'>
					<label class='col-md-2'><b>FOLIO</b></label>
					<label class='col-md-10'>".$folio."</label>
				</div>
				<div class='col-md-12 spadding10'>
					<label class='col-md-2'><b>NOMBRE</b></label>
					<label class='col-md-10'>".$nombre."</label>
				</div>
				<div class='col-md-12 spadding10'>
					<label class='col-md-2'><b>ESTATUS</b></label>
					<label class='col-md-10'>".$status."</label>
				</div>
				<div class='col-md-12 spadding10'>
					<label class='col-md-2'><b>FECHA</b></label>
					<label class='col-md-10'>".$fecha."</label>
				</div>
				<div class='col-md-12 spadding10'>
					<label class='col-md-2'><b>AC/ IN</b></label>
					<label class='col-md-10'>".$activo."</label>
				</div>
				<div class='col-md-12 spadding10'>
					<legend class='col-md-11 txt15 col-centered text-center spadding10'><b>DESCRIPCIÓN</b></legend>
					<p class='col-md-12 tpadding20'>".$descripcion."</p>
				</div>
				<div class='col-md-12 spadding10'>
					<div class='col-md-4 col-centered'><button type='button' class='btn-primary btn btn-block' onclick=editar(\"{$id}\")>Editar</button></div>
				</div>";
	
			// echo json_encode($res);
		break;
		case 14:
			//Eliminar servicio
			if(isset($_POST['id'])){
				$id=seguridad($_POST['id']);
				$resultado=$mysqli->query("DELETE FROM reportes WHERE id ='{$id}'")or die("Error en: ".$mysqli->error);
				if($resultado){
					echo "success";
				}
				else{
					echo "error_1";
				}
			}
			else{
				echo "error_2";
			}
		break;
		case 15:
		//Listado de reportes de servicios
			if(isset($_POST['busqueda'])) {
				$aux = seguridad($_POST['busqueda']);
				$query = "SELECT * FROM reportes WHERE folio = '{$aux}' AND activo = 1";
				$resultado=$mysqli->query($query);

				if($resultado->num_rows > 0){
				$row=$resultado->fetch_array(MYSQLI_ASSOC);
				$folio = seguridad_decode($row['folio']);
				$nombre = seguridad_decode($row['nombre']);
				$status = seguridad_decode($row['status']);
				$descripcion = seguridad_decode2($row['descripcion']);
				$fecha = $row['fecha'];

				echo "<div class='col-md-3 col-sm-6 text-left'>
		            <div class='sec-less-padding-2 div-border'>
		              <label>Folio:</label>
		              <p>".$folio."</p>
		              <label>Nombre:</label>
		              <p>".ucwords($nombre)."</p>
		               <label>Fecha:</label>
		              <p>".$fecha."</p>
		            </div>
		         </div>
		         <div class='col-md-3 col-sm-6 text-left'>
		            <div class='sec-less-padding-2 div-border'>
		              <label>Estatus:</label>
		              <p>".$status."</p>
		            </div>
		         </div>
		         <div class='col-md-6 col-sm-6 text-left'>
	            <div class='sec-less-padding-2 div-border'>
	              <label>Detalles:</label>
	              <p>".$descripcion."</p>
	            </div>
	         </div>";


				}else{
					echo "No hay resultados";					
				}
			}
		break;
		default:
			echo "error_400";//opción no valida
		break;
	}
?>