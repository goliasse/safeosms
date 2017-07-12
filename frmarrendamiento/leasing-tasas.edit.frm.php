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
$xHP		= new cHPage("TR.EDITAR TASAS DE ARRENDAMIENTO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->addJTableSupport();
$xHP->init();

$xFRM	= new cHForm("frmleasing_tasas", "leasing-tasas.frm.php?action=$action");

$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cLeasing_tasas();
$xTabla->setData( $xTabla->query()->initByID($clave));


$xFRM->OHidden("idleasing_tasas", $xTabla->idleasing_tasas()->v());

$xFRM->addHElem( $xSel->getListaDePeriocidadDePago("frecuencia", $xTabla->frecuencia()->v())->get("TR.FRECUENCIA", true) );
//$xFRM->OMoneda("frecuencia", $xTabla->frecuencia()->v(), "TR.FRECUENCIA");

$xFRM->addHElem( $xSel->getListaDeLeasingRAC("tipo_de_rac", $xTabla->tipo_de_rac()->v())->get(true) );

$xFRM->addHElem( $xSel->getListaDeLeasingLimInf("limite_inferior", $xTabla->limite_inferior()->v())->get(true) );
$xFRM->addHElem( $xSel->getListaDeLeasingLimSup("limite_superior", $xTabla->limite_superior()->v())->get(true) );
//$xFRM->OMoneda("limite_inferior", $xTabla->limite_inferior()->v(), "TR.LIMITEINFERIOR");
//$xFRM->OMoneda("limite_superior", $xTabla->limite_superior()->v(), "TR.LIMITESUPERIOR");

$xFRM->OMoneda2("tasa_ofrecida", $xTabla->tasa_ofrecida()->v(), "TR.TASA");
$xFRM->OMoneda2("comision_apertura", $xTabla->comision_apertura()->v(), "TR.COMISION_POR_APERTURA");
$xFRM->OMoneda2("tasa_marginal", $xTabla->tasa_marginal()->v(), "TR.TASAMARGINAL");
$xFRM->OMoneda2("tasa_vec", $xTabla->tasa_marginal()->v(), "TR.TASAVEC");

//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>