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
	$xHP				= new cHPage("TR.CREDITOS POR RANGO DE DIAS DE IMPAGO", HP_REPORT);
	$xF					= new cFecha();
	$xLi				= new cSQLListas();
	$xQL				= new MQL();
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

$empresa				= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 					= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;


$es_por_estatus 		= "";
$BySaldo				= " AND (creditos_solicitud.saldo_actual>=0.99) ";

if($estatus == CREDITO_ESTADO_AUTORIZADO OR $estatus == CREDITO_ESTADO_SOLICITADO){ $BySaldo		= ""; }

$ByOficial				= $xLi->OFiltro()->CreditosPorOficial(parametro("oficial", SYS_TODAS ,MQL_INT));
$BySucursal				= $xLi->OFiltro()->CreditosPorSucursal(parametro("sucursal", ""));
$ByEmpresa				= $xLi->OFiltro()->CreditosPorEmpresa($empresa);
$es_por_frecuencia 		= $xLi->OFiltro()->CreditosPorFrecuencia($frecuencia);
$es_por_convenio 		= $xLi->OFiltro()->CreditosPorProducto($convenio);
$es_por_estatus 		= $xLi->OFiltro()->CreditosPorEstado($estatus);

/* ***************************************************************************** */
$senders				= getEmails($_REQUEST);
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

$init		= 6;
$arrCSumas	= array(
		4 => "saldo",
		$init => "dias_impago"

);

$rsrango		= $xQL->getDataRecord("SELECT * FROM `creditos_nievelesdereserva` WHERE `tipo_de_credito` = 1");
$casewhen		= "";
foreach ($rsrango as $rw){
	$li			= $rw["limite_inferior"];
	$ls			= $rw["limite_superior"];
	$casewhen	.= ($casewhen == "") ? "(CASE WHEN ( (dias_impago >=$li AND dias_impago <=$ls)) THEN (saldo) ELSE 0 END) AS 'sin_pago_" . $ls . "_dias'" : ",(CASE WHEN ( (dias_impago >=$li AND dias_impago <=$ls)) THEN (saldo) ELSE 0 END) AS 'sin_pago_" . $ls . "_dias'";
	$init++;
	$arrCSumas[$init]	= "sin_pago_" . $ls . "_dias";
}

$ConEmpresa		= (PERSONAS_CONTROLAR_POR_EMPRESA == true)? " `socios`.`alias_dependencia` AS `empresa`, " : "";
$sql	= "SELECT
	$ConEmpresa
	`socios`.`codigo`,
	`socios`.`nombre`,
	`creditos_solicitud`.`numero_solicitud`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`,
	`saldo`,
	`fecha_ultimo_mvto` AS `fecha`, 
	dias_impago AS 'dias_de_atraso',
	 $casewhen
	 FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
		ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
		`idcreditos_tipoconvenio` 
			INNER JOIN `socios` `socios` 
			ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
				INNER JOIN `dias_en_mora` `dias_en_mora` 
				ON `creditos_solicitud`.`numero_solicitud` = `dias_en_mora`.
				`numero_solicitud`
	 WHERE 
		saldo > " . TOLERANCIA_SALDOS . "
				AND dias_impago >=1 
				/*AND dias_impago <=90*/
		$es_por_convenio
		$es_por_frecuencia
		$es_por_estatus
		$ByOficial
		$BySucursal
		$ByEmpresa
		/*Disable castigados*/
		AND `creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_CASTIGADO . "
	 ORDER BY dias_impago DESC, `socios`.`nombre`
	";

	$xTBL	= new cTabla($sql);
	$xTBL->setTipoSalida($out);
	//$xTBL->setEventKey("jsGoEstadoDeCuentaDeCreditosPorPersona");
	$xTBL->setKeyField("numero_solicitud");
	//$xTBL->setTdClassByType();
	$xTBL->setUsarNullPorCero();
	$xTBL->setFechaCorte($FechaFinal);

	/*,
			($init+9)=> "vencido_7_dias",
			($init+10) => "vencido_15_dias",
			($init+11) => "vencido_30_dias",
			($init+12) => "vencido_60_dias",
			($init+13) => "vencido_90_dias",
			($init+14) => "vencido_120_dias",
			($init+15) => "vencido_180_dias",
			($init+16) => "vencido_mas_180_dias"*/
	$xTBL->setFootSum($arrCSumas);


$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($xHP->getTitle());
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
//$xT		= new cTabla($sql, 2);
//$xT->setTipoSalida($out);

$xTBL->setTipoSalida($out);
$body		= $xRPT->getEncabezado($xHP->getTitle());
$xRPT->setBodyMail($body);
$xRPT->addContent($body);
$xRPT->addContent( $xTBL->Show(  ) );
//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
//$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>