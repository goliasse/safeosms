<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
 */
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
$xHP		= new cHPage("TR.Historial de Nomina", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();

$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT); $credito = parametro("pb", $credito, MQL_INT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$senders		= getEmails($_REQUEST);
$out 			= parametro("out", SYS_DEFAULT);

$titulo			= "";
$archivo		= "";
$sql			= "SELECT
	`socios_aeconomica_dependencias`.`descripcion_dependencia` AS `empresa`,
	`empresas_operaciones`.`fecha_de_operacion`,
	`empresas_cobranza`.`clave_de_nomina`,
	`creditos_periocidadpagos`.`descripcion_periocidadpagos`   AS `frecuencia`,
	`empresas_cobranza`.`clave_de_credito`,
	`empresas_cobranza`.`parcialidad`,
	`empresas_cobranza`.`monto_enviado`,
	`empresas_cobranza`.`observaciones`,
	`empresas_cobranza`.`saldo_inicial`,
	`empresas_cobranza`.`estado`,
	`empresas_cobranza`.`recibo`,
	getFechaMXByInt(`empresas_cobranza`.`tiempocobro` ) AS `fecha_de_cobro`
FROM
	`empresas_operaciones` `empresas_operaciones` 
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
		ON `empresas_operaciones`.`periocidad` = `creditos_periocidadpagos`.
		`idcreditos_periocidadpagos` 
			INNER JOIN `empresas_cobranza` `empresas_cobranza` 
			ON `empresas_cobranza`.`clave_de_nomina` = `empresas_operaciones`.
			`idempresas_operaciones` 
				INNER JOIN `socios_aeconomica_dependencias` 
				`socios_aeconomica_dependencias` 
				ON `empresas_operaciones`.`clave_de_empresa` = 
				`socios_aeconomica_dependencias`.
				`idsocios_aeconomica_dependencias`
			WHERE
	(`empresas_cobranza`.`clave_de_credito` =$credito)  AND
	(`empresas_operaciones`.`tipo_de_operacion` =1) 
ORDER BY
	
	`empresas_cobranza`.`parcialidad`,
	`empresas_operaciones`.`fecha_de_operacion`
";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());

//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);
$xT->setFootSum(array( 
		7 => "dias_transcurridos",
		9 => "monto_calculado",
		8 => "saldo",
		10 => "interes_normal" ,
		11 => "interes_moratorio"
));
$xCred		= new cCredito($credito);
if($xCred->init() == true){
	$xRPT->addContent($xCred->getFicha(true, "", true, true));
}

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);


$xRPT->addContent( $xT->Show( ) );
//============ Agregar HTML

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>