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
$xHP		= new cHPage("TR.Estado de pocision financiera ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();

$out 			= parametro("out", SYS_DEFAULT);
$senders		= getEmails($_REQUEST);
$ejercicio		= parametro("ejercicio", 0, MQL_INT);
$periodo		= parametro("periodo", 0, MQL_INT);
$moneda			= parametro("moneda", AML_CLAVE_MONEDA_LOCAL);

$fecha			= $xF->getDiaFinal("$ejercicio-$periodo-01");
$sql			= "";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());

$activo			= 1;
$xHP->init();
$xFormat		= new cFormato(501);
//============ Procesar estado de Resultados
$xSec			= new cCuentasPorSector(CONTABLE_CLAVE_INGRESOS, $fecha);
$xSec->init(false);
$suma_ingresos	= $xSec->getSumaTitulo();

$xSec			= new cCuentasPorSector(CONTABLE_CLAVE_EGRESOS, $fecha);
$xSec->init(false);
$suma_egresos	= $xSec->getSumaTitulo();

$resultado		= $suma_ingresos - $suma_egresos;
//Actualizar Resultados
$xConf			= new cConfiguration();
$xConf->set("resultado_del_periodo_contable", $resultado);

//============ Reporte
$xSec			= new cCuentasPorSector(CONTABLE_CLAVE_ACTIVO, $fecha); 
$xSec->init(false);
$activo			= $xSec->render();
$suma_activo	= $xSec->getSumaTitulo();

$xSec			= new cCuentasPorSector(CONTABLE_CLAVE_PASIVO, $fecha); 
$xSec->init(false);
$pasivo			= $xSec->render();
$suma_pasivo	= $xSec->getSumaTitulo();

$xSec			= new cCuentasPorSector(CONTABLE_CLAVE_CAPITAL, $fecha);
$xSec->init(false);
$capital		= $xSec->render();
$suma_capital	= $xSec->getSumaTitulo() + $resultado;

$pasivo_mas_capital	= $suma_pasivo + $suma_capital;

$xFormat->setProcesarVars(array(
		"variable_ficha_activo" => $activo,
		"variable_total_activo" => getFMoney($suma_activo),
		"variable_ficha_pasivo" => $pasivo,
		"variable_total_pasivo" => getFMoney($suma_pasivo),
		"variable_ficha_capital" => $capital,
		"variable_total_capital" => getFMoney($suma_capital),
		"variable_pasivo_mas_capital" => getFMoney($pasivo_mas_capital),
		"variable_resultado_del_periodo" => getFMoney($resultado)
));

echo $xFormat->get();
$xHP->fin();
?>