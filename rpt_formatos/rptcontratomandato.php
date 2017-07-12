<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";

$idcuenta = $_GET["idcuenta"];
	if (!$idcuenta){
		echo $regresar;
		exit;
	}

$oficial = elusuario($iduser);

//ini_set("display_errors", "on");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<!-- -->
<?php
echo getRawHeader();

	$sqlcontrato 						= "SELECT * FROM captacion_cuentas where numero_cuenta=$idcuenta";
	$datos_de_la_cuenta 				= obten_filas($sqlcontrato);
	$idsocio 							= $datos_de_la_cuenta["numero_socio"];
	$tasa 								= $datos_de_la_cuenta["tasa_otorgada"];
	$tasa 								= $tasa * 100;
	
	$xSoc								= new cSocio($idsocio);
	$xSoc->init();
	$DSoc								= $xSoc->getDatosInArray();
	$domicilio_del_socio 				= $xSoc->getDomicilio();
	$nombre_del_socio 					= $xSoc->getNombreCompleto();
	$numero_de_socio 					= $idsocio;
	
	$caja_local 						= eltipo("socios_cajalocal", $DSoc["cajalocal"]);

	/**
	 * Busca el Primer Deposito del Socio.
	 */
	$datos_primer_deposito 			= obten_filas("SELECT * FROM operaciones_mvtos WHERE docto_afectado=$idcuenta ORDER BY fecha_operacion LIMIT 0,1");
	$monto_inicial 					= $datos_primer_deposito[7];
	$monto_inicial_letras 			= convertirletras($monto_inicial);
	$numero_dias 					= $datos_primer_deposito[29];
	$variable_lugar 				= $eacp_estado . ",  " . $eacp_municipio;
	$variable_fecha_actual 			= fecha_larga();
	$variable_tasa_otorgada 		= $datos_primer_deposito[28] * 100;
	$variable_fecha_vencimiento 	= fecha_larga($datos_primer_deposito[11]);
	$variable_oficial 				=  $oficial;
	/**
	 *  Obtiene la Lista de Beneficiados
	 */
	$beneficiados = "";
	$sql_beneficiados = "SELECT * FROM socios_relaciones WHERE tipo_relacion=11 AND socio_relacionado=$idsocio LIMIT 0,100";
	$rs_beneficiados = mysql_query($sql_beneficiados);
	while ($row_beneficiado = mysql_fetch_array($rs_beneficiados)) {
		$beneficiados = $beneficiados . "<li>$row_beneficiado[6] $row_beneficiado[7] $row_beneficiado[5]</li> ";
	}
	$variable_lista_beneficiados = "<ol>
				$beneficiados
			</ol>";
	/**
	 * Compara si existen Datos de Mancomunados
	 */
	if ($datos_de_la_cuenta["nombre_mancomunado1"] != "" & $datos_de_la_cuenta["nombre_mancomunado1"] != "_") {
			$nombre_mancomunados = "<br /><br /><br />" . $datos_de_la_cuenta["nombre_mancomunado1"] . " <br /> <br /><br />" . $datos_de_la_cuenta["nombre_mancomunado2"];
	} else {
			$nombre_mancomunados = "";
	}
	/**
	 * Empieza el Intercambio de variables en el contrato
	 */
$texto_contrato = contrato(2,4);
$texto_contrato = str_replace("variable_nombre_del_socio", $nombre_del_socio, $texto_contrato);
$texto_contrato = str_replace("variable_numero_de_socio", $numero_de_socio, $texto_contrato);
$texto_contrato = str_replace("variable_domicilio_del_socio", $domicilio_del_socio, $texto_contrato);
$texto_contrato = str_replace("variable_nombre_de_la_entidad", EACP_NAME, $texto_contrato);
$texto_contrato = str_replace("variable_domicilio_de_la_entidad", EACP_DOMICILIO_CORTO, $texto_contrato);
$texto_contrato = str_replace("variable_monto_inicial_en_numero", $monto_inicial, $texto_contrato);
$texto_contrato = str_replace("variable_monto_inicial_en_letras", $monto_inicial_letras, $texto_contrato);
$texto_contrato = str_replace("variable_numero_de_dias", $numero_dias, $texto_contrato);
$texto_contrato = str_replace("variable_caja_local", $caja_local, $texto_contrato);
$texto_contrato = str_replace("variable_lugar", $variable_lugar, $texto_contrato);
$texto_contrato = str_replace("variable_fecha_actual", $variable_fecha_actual, $texto_contrato);
$texto_contrato = str_replace("variable_nombre_mancomunados", $nombre_mancomunados, $texto_contrato);
//$texto_contrato = str_replace("variable_", $, $texto_contrato);
$texto_contrato = str_replace("variable_tasa_otorgada", $variable_tasa_otorgada, $texto_contrato);
$texto_contrato = str_replace("variable_fecha_de_vencimiento", $variable_fecha_vencimiento, $texto_contrato);
$texto_contrato = str_replace("variable_oficial", $variable_oficial, $texto_contrato);
$texto_contrato = str_replace("variable_titular_de_cobranza", $titular_cobranza, $texto_contrato);
$texto_contrato = str_replace("variable_lista_de_beneficiados", $variable_lista_beneficiados, $texto_contrato);
$texto_contrato = str_replace("variable_nombre_de_la_sociedad", EACP_NAME, $texto_contrato);

echo $texto_contrato;

echo getRawFooter();
?>
</body>
</html>
