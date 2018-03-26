<?php
/**
 * Formato de
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
$xHP		= new cHPage("TR.Solicitud de Credito", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();


$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$siavales 		= parametro("avales", false, MQL_BOOL);
$sigarantias 	= parametro("garantias", false, MQL_BOOL);
$sipatrimonio 	= parametro("patrimonio", false, MQL_BOOL);
$siflujo 		= parametro("flujo", false, MQL_BOOL);
$sihistorial 	= parametro("historial", false, MQL_BOOL);

//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT * FROM socios LIMIT 0,100";
$titulo			= "";
$archivo		= "";

$xHP->addCSS("../css/contrato.css.php");


$xFMT			= new cFormato(1100);
$xFMT->setCredito($credito);
if($sipatrimonio == true){
	$xFMT->setConPersonasConRelacionPatrimonial();
}
if($siflujo == true){
	$xFMT->setConCreditosFlujoEfectivo();
}
//TODO: Agregar soporte a impresión.

$xFMT->setProcesarVars();
$xFMT->setToImprimir();

$xHP->init();
echo $xFMT->get();
$xHP->fin();

?>
