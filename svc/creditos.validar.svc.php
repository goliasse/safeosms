<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
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
$xHP		= new cHPage("", HP_SERVICE);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones= parametro("idobservaciones");

$rs				= array();
$rs[SYS_ERROR]	= true;
$rs["messages"]	= "Sin datos validos";
$rs["message"]	= "Sin datos validos";
if($credito > DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	$incumple	= 0;
	if($xCred->init() == true){
		
		$xCred->setVerificarValidez(false);
		$xReg	= new cReglasDeCalificacion();
		$xReg->setCredito($credito);
		$xReg->getValoresDeCalificacion();
		$incumple	+= $xReg->getNumeroIncumplido();
		
		
		$xReg->setPersona($xCred->getClaveDePersona());
		$xReg->getValoresDeCalificacion();
		$incumple	+= $xReg->getNumeroIncumplido();
		
	} else {
		$incumple	= SYS_UNO;
	}
	$rs[SYS_NUMERO]	=	$incumple;
	
	$rs["message"]	= $xCred->getMessages();
}
header('Content-type: application/json');
echo json_encode($rs);
?>