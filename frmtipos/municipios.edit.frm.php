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
$xHP		= new cHPage("TR.EDITAR MUNICIPIO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$estado			= parametro("estado",0, MQL_INT);

$xHP->init();


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cGeneral_municipios();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmmunicipios", "municipios.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

if($clave <= 0){
	$xTabla->idgeneral_municipios('NULL');
	$xTabla->clave_de_entidad($estado);
	if($estado > 0){
		$nuevo	= $xQL->getDataValue("SELECT MAX(`clave_de_municipio`) +1 AS `nuevo` FROM `general_municipios` WHERE `clave_de_entidad`=$estado", "nuevo");
		$xTabla->clave_de_municipio($nuevo);
	}
}


$xFRM->OHidden("idgeneral_municipios", $xTabla->idgeneral_municipios()->v());

$xFRM->ONumero("clave_de_municipio", $xTabla->clave_de_municipio()->v(), "TR.CLAVE");
$xFRM->OText("nombre_del_municipio", $xTabla->nombre_del_municipio()->v(), "TR.NOMBRE");
$xFRM->addHElem($xSel->getListaDeEntidadesFed("clave_de_entidad", true, $xTabla->clave_de_entidad()->v())->get(true) );



$xFRM->OMoneda("habitantes", $xTabla->habitantes()->v(), "TR.HABITANTES");
$xFRM->OMoneda("indice_de_marginacion", $xTabla->indice_de_marginacion()->v(), "TR.INDICE DE MARGINACION");
$xFRM->OText("grado_de_marginacion", $xTabla->grado_de_marginacion()->v(), "TR.GRADO DE MARGINACION");
$xFRM->OMoneda("lugar_nacional", $xTabla->lugar_nacional()->v(), "TR.LUGAR NACIONAL");

//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>