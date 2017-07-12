<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================

	$xHP		= new cHPage("Reporte de Creditos", HP_RPTXML );
	$xF			= new cFecha();
//=====================================================================================================
/**
 * Filtrar si Existe Caja Local
 */
$estatus 				= (isset($_GET["f2"]) ) ? $_GET["f2"] : SYS_TODAS;
$frecuencia 			= (isset($_GET["f1"]) ) ? $_GET["f1"] : SYS_TODAS;
$convenio 				= (isset($_GET["f3"]) ) ? $_GET["f3"] : SYS_TODAS;

$estatus 				= (isset($_GET["estado"])) ? $_GET["estado"] : $estatus;
$frecuencia 			= (isset($_GET["periocidad"])) ? $_GET["periocidad"] : $frecuencia;
$frecuencia 			= (isset($_GET["frecuencia"])) ? $_GET["frecuencia"] : $frecuencia;
$convenio 				= (isset($_GET["convenio"])) ? $_GET["convenio"] : $convenio;
$convenio 				= (isset($_GET["producto"])) ? $_GET["producto"] : $convenio;

$empresa				= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 					= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

$es_por_estatus 		= "";
$BySaldo				= " AND (creditos_solicitud.saldo_actual>=0.99) ";
$ByEmpresa				= ( $empresa == SYS_TODAS ) ? "" : " AND socios.iddependencia = $empresa ";

if($estatus != SYS_TODAS){ 
	$es_por_estatus = " AND creditos_solicitud.estatus_actual=$estatus ";
	if($estatus == CREDITO_ESTADO_AUTORIZADO OR $estatus == CREDITO_ESTADO_SOLICITADO){ $BySaldo		= ""; }
 }
$es_por_frecuencia 	= ($frecuencia != SYS_TODAS) ? " AND creditos_solicitud.periocidad_de_pago =$frecuencia " : "";
$es_por_convenio 		= ($convenio != SYS_TODAS) ? " AND creditos_solicitud.tipo_convenio = $convenio " : "";

$mx 					= (isset($_GET["mx"])) ? true : false;
if($mx == true){
	$fechaInicial		= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$fechaInicial		= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal		= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}
$ByFecha				= " AND (creditos_solicitud.fecha_ministracion >='$fechaInicial' AND creditos_solicitud.fecha_ministracion <= '$fechaFinal') ";
/* ******************************************************************************/
$OperadorFecha			= ($out == OUT_EXCEL) ?  " " : "getFechaMX";

$setSql = "SELECT 
	socios.alias_dependencia AS 'empresa',
	creditos_solicitud.numero_socio AS 'socio',
	socios.nombre, 
	creditos_solicitud.numero_solicitud AS 'credito',
	creditos_tipoconvenio.descripcion_tipoconvenio AS 'producto',
	creditos_periocidadpagos.descripcion_periocidadpagos AS 'condiciones_de_pago',
	`creditos_tipo_de_autorizacion`.`descripcion_tipo_de_autorizacion` AS 'modalidad_de_autorizacion',
	`creditos_tipo_de_pago`.`descripcion` AS `tipo_de_pago`,
		 
	$OperadorFecha(creditos_solicitud.fecha_ministracion) AS 'fecha_de_otorgamiento',
	creditos_solicitud.monto_autorizado AS 'monto_original', 
	$OperadorFecha(creditos_solicitud.fecha_vencimiento) AS 'fecha_de_vencimiento',
	(creditos_solicitud.tasa_interes * 100) AS 'tasa_anual',
	CONCAT(creditos_solicitud.ultimo_periodo_afectado, '/', creditos_solicitud.pagos_autorizados) AS 'numero_de_pagos',
 
	creditos_solicitud.saldo_actual AS 'saldo_insoluto',
	creditos_estatus.descripcion_estatus AS 'estatus',

	
	`creditos_letras_pendientes`.`interes_exigible`,
	`creditos_letras_pendientes`.`iva_exigible`,
	`creditos_letras_pendientes`.`interes_moratorio` 	

FROM
	`creditos_letras_pendientes` `creditos_letras_pendientes` 
		RIGHT OUTER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `creditos_letras_pendientes`.`docto_afectado` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `creditos_estatus` `creditos_estatus` 
			ON `creditos_solicitud`.`estatus_actual` = `creditos_estatus`.
			`idcreditos_estatus` 
				INNER JOIN `creditos_tipo_de_pago` `creditos_tipo_de_pago` 
				ON `creditos_solicitud`.`tipo_de_pago` = `creditos_tipo_de_pago`
				.`idcreditos_tipo_de_pago` 
					INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
					ON `creditos_solicitud`.`tipo_convenio` = 
					`creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
						INNER JOIN `creditos_tipo_de_autorizacion` 
						`creditos_tipo_de_autorizacion` 
						ON `creditos_solicitud`.`tipo_autorizacion` = 
						`creditos_tipo_de_autorizacion`.
						`idcreditos_tipo_de_autorizacion` 
							INNER JOIN `creditos_periocidadpagos` 
							`creditos_periocidadpagos` 
							ON `creditos_solicitud`.`periocidad_de_pago` = 
							`creditos_periocidadpagos`.
							`idcreditos_periocidadpagos` 
								INNER JOIN `socios` `socios` 
								ON `creditos_solicitud`.`numero_socio` = 
								`socios`.`codigo`

	WHERE (creditos_solicitud.numero_solicitud != 0)
	$BySaldo
	$es_por_estatus
	$es_por_frecuencia
	$es_por_convenio
	$ByEmpresa
	$ByFecha
	ORDER BY `creditos_solicitud`.`tipo_convenio`, socios.nombre ";



$senders		= getEmails($_REQUEST);


$sql			= $setSql;
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);
$xT->setFootSum(array(
		9 => "monto_original",
		13 => "saldo_insoluto",
		15 => "interes_exigible",
		16 => "iva_exigible",
		17 => "interes_moratorio"
));

$body		= $xRPT->getEncabezado($xHP->getTitle(), $fechaInicial, $fechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

exit;
	//echo $setSql; exit();
if ($out!= OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	//$oRpt->setConnection( cnnGeneral() );
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report45-e.xml");
	$oOut = $oRpt->createOutputPlugin($out);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
	$xHP	= new cHExcel();
	$xHP->convertTable($setSql);
}
?>