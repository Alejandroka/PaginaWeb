<?php
include("Conexiones/Conexion.php");

session_start();

// ESTE METODO DE CONSULTA PERMITE SABER SABER SI ES ALUMNO O DOCENTE DESDE LA TABLA LoginUsuarios...
$user = @mysqli_query($conexionBaseDatos, "SELECT * FROM LoginUsuarios WHERE Usuario= '" . $_SESSION["nombreUsuario"] . "'");
$resultadoFilasUsuario = @mysqli_fetch_assoc($user);

// RESTRICCION DE PAGINAS, SI SE QUIERE INGRESAR A LA PAGINA PRINCIPAL NO ACCEDERA HASTA QUE SE INICIE SESION OTRA VEZ....
/*if (!isset($_SESSION["nombreUsuario"])) 
	{
		# code...
		header("Location:Login.php");
	}*/

// AL DAR CLIC EN CERRAR SE CIERRA LA SESION...
if (isset($_REQUEST["cerrar"])) {
	# code...
	session_destroy();
	header("Location:Login.php");
}


// CREACION DE UNA CLASE (CURSO)...
if (isset($_REQUEST["nombreProducto"])) {
	# code...
	$nombreProductos = $_REQUEST["nombreProducto"];
	$descripcionProductos = $_REQUEST["descripcionProducto"];
	$precioProductos = $_REQUEST["precioProducto"];

	$str = "1234567890";
	$cad = "";

	for ($i = 0; $i < 11; $i++)
		$cad .= substr($str, 'rand'(0, 10), 1);

	$fotos = $_FILES["imagenProducto"]["name"];
	$tipos = $_REQUEST["tipo"];

	$usuario = $_SESSION["nombreUsuario"];

	@mysqli_query($conexionBaseDatos, "INSERT INTO Productos (IdProducto, NombreProducto, DescripcionProducto, PrecioProducto, CodigoProducto, Estado, RutaImagenProducto) VALUES (Null, '$nombreProductos','$descripcionProductos','$precioProductos', '$cad', '$tipos','$fotos')");
	move_uploaded_file($_FILES['imagenProducto']['tmp_name'], "Imagenes/Productos/" . $fotos);
	header("Location:Index.php");
}

if (isset($_REQUEST["modificar"])) {
	# code...
	$usuario = $_SESSION["nombreUsuario"];
	$nombreProducto = $_SESSION["nombreProducto"];
	$descripcionProducto = $_REQUEST["descripcionProducto"];
	$precioProducto = $_REQUEST["precioProducto"];
	$textoDescripcion = $_REQUEST["texto"];
	$fechaEntrega = $_REQUEST["fechaEntrega"];
	$horaEntrega = $_REQUEST["horaEntrega"]; //////////////////////////////////

	@mysqli_query($conexionBaseDatos, "UPDATE Productos SET NombreProducto='$nombreProducto', DescripcionProducto='$descripcionProducto', PrecioProducto='$precioProducto' WHERE IdProducto=" . $_REQUEST["modificar"]);
}

// METODO PARA ACTUALIZAR LOS DATOS DE LA ACTIVIDAD, MEDIANTE LA VARIABLE "modificarPlanClase"...
if (isset($_REQUEST["modificarPlanClase"])) {
	# code...
	$modificarP = $_REQUEST["modificarPlanClase"];

	$modificarQuery = @mysqli_query($conexionBaseDatos, "SELECT * FROM PlanClases WHERE Id=$modificarP");
	$arrayModificarP = @mysqli_fetch_assoc($modificarQuery);
}

// REALIZAMOS LAS CONSULTAS O BUSCAMOS SI HAY CURSOS EXISTENTES PARA DESPUES VISUALIZARLO EN LA TABLA.........
$consulta = @mysqli_query($conexionBaseDatos, "SELECT * FROM Productos WHERE Estado = 1");
$filasProducto = @mysqli_num_rows($consulta);
$resultadoFilasProducto = @mysqli_fetch_assoc($consulta);


?>
<!DOCTYPE html>
<html lang="es">

<head>
	<title>Pasteleria SF</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="Estilos/Index.css">
	<link rel="icon" href="Imagenes/Logos/angular_solidBlack.png" >

</head>

<body>
	
	<header>

		<div class="logoPrincipal"><a href="Index.php"><img src="Imagenes/Logos/icons8-casa-50.png"></a></div>
		<div class="Nosotros" title="Nosotros"><a href="Nosotros/index.html"><img src="Imagenes/Iconos/15659.png"></a></div>

		<div class="header2">
			<ul class="nav">
			<li><a href="">Servicio</a>

			<ul>
				<li><a href="">Bautizo</a></li>
				<li><a href="">xv años</a></li>
				<li><a href="">Bodas</a></li>
				<li><a href="">Especiales</a></li>
			</ul>

			</li>
			</ul>
		</div>

		<!-- LOGO DE LA TIENDA -->
		<form class="form" action="Buscar.php" method="POST">
			<!-- CONTENEDOR DE BARRA DE BUSQUEDA RECTANGULAR CAJA (DIV) -->
			<div class="barraBusquedaCaja">

				<!-- TEXTO DE BUSQUEDA DE PRODUCTOS -->
				<input type="text" id="idBusqueda" name="buscar" placeholder="Buscar Productos...">
				<!-- BOTON DE BUSQUEDA -->
				<button class="botonPrincipal botonBusqueda" value="buscar">
					<!-- ICONO DE BUSQUEDA -->
					<img src="Imagenes/Iconos/BarraBusqueda1.png">
				</button>
			</div>
		</form>

		<!-- CONTENEDOR DE OPCIONES (DIV) -->
		<div class="opcionesUsuario">
			<?php if (isset($_SESSION['nombreUsuario'])) {
			?>
				<div class="dir"><?php echo $resultadoFilasUsuario['Direccion']; ?></div>
			<?php } ?>
			<?php
			if (isset($_SESSION['nombreUsuario']))
				echo "<div class='OpcionRegistrarse' title='" . $resultadoFilasUsuario['Nombre'] . "'><a href='PerfilUsuario.php'><img src='Imagenes/Iconos/SesionIniciada1.png'></a></div>";
			else
				echo "<div class='OpcionRegistrarse' title='Registrate'><a href='Login.php'><img src='Imagenes/Iconos/Login2.png'></a></div>";

			?>
			<!-- OPCION REGISTRARSE -->
			<div class="objetoOpcionCerrarSesion" tittle="Cerrar Sesión"><a href="Index.php?cerrar=1"><img src="Imagenes/Iconos/CerraSesion3.png"></a></div> <!-- OPCION INGRESAR -->
			<div class="OpcionCarrito" title="Mis compras"><a href="MiPedido.php"><img src="Imagenes/Iconos/AgregarCarrito2.png"></a></div> <!-- OPCION MIS COMPRAS -->
		</div>

	</header>

	<!-- CONTENEDOR PRINCIPAL DEL HTML DONDE SE VISUALIZA LOS PRODUCTOS -->
	<div class="contenidoPrincipalPagina">
		<div>
			<center><img class="imglog" src="Imagenes/Logos/Logo-PSF.png"></center>
		</div>
		<!-- CONTENIDO DE LA PAGINA -->
		<div class="contenidoPagina">
			<div class="seccionTituloProductos">
			</div> <!-- MENSAJE DEL CONTENIDO DE LA PAGINA DE LOS PRODUCTOS -->
			<!-- CREAMOS UN CONTENEDOR (DIV) PARA MOSTRAR LOS PORDUCTOS -->
			<div class="listaProductos" id="listaEspacioProductos">
				<!-- <a href="Atras/Atras.php">Atras</a> -->

				<?php
				// print_r($nombreUsuario);
				function formatoPrecio($valor)
				{
					$precio = explode(".", $valor); // LA FUNCION explode FUNCIONA COMO EL SPLIT, SEPARA EL TEXTO DE ACUERDO A CUANTAS VECES APARECE EL CARACTER ASIGNADO...
					return "$ " . $precio[0] . ".<span>" . $precio[1] . "</span>";
				}

				if ($filasProducto > 0) {
					# code...
					do {
						# code...
						echo "<div class='cajaProductosTabla'>";
						// LA VARIABLE productosDetalles ESPECIFICA EL ID DEL PRODUCTO SELECCIONADO....
						echo "<a href='DetallesProducto.php?productosDetalles=" . $resultadoFilasProducto['IdProducto'] . "'>";
						echo "<div class='productos'>";
						echo "<img src='Imagenes/Productos/" . $resultadoFilasProducto['RutaImagenProducto'] . "'>";
						echo "<div class='detalleTitulo'>" . $resultadoFilasProducto['NombreProducto'] . "</div>";
						echo "<div class='detalleDescripcion'>" . $resultadoFilasProducto['DescripcionProducto'] . "</div>";
						echo "<div class='detallePrecio'>" . formatoPrecio($resultadoFilasProducto['PrecioProducto']) . "</div>";
						echo "</div>";
						echo "</a>";
						echo "</div>";
					} while ($resultadoFilasProducto = @mysqli_fetch_assoc($consulta));
				}
				?>

			</div>
		</div>
	</div>
<section class="whatsapp-contact-section">
    <a id="whatsapp-contact-button" href="https://www.whatsapp.com/?lang=es" target="_blank" aria-label="Consulta nuestro Bot">
      <img src="Imagenes/Iconos/WhatsApp.svg.png" alt="WhatsApp icon" aria-label="WhatsApp icon" width="43">
      Consulta nuestro Bot
    </a>
  </section>

	<?php include("ParteInferiorFooter.php") ?>
</body>

</html>