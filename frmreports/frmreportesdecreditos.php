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
$xHP		= new cHPage("TR.Reportes de Credito");
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();
$xHP->init();

$xRPT		= new cPanelDeReportes(iDE_CREDITO, "general_creditos");
$xRPT->setTitle($xHP->getTitle());
$xRPT->addOficialDeCredito();
$xRPT->setConOperacion(true);
$xCtrl		= $xSel->getListaDeDestinosDeCredito();
$xCtrl->addEspOption(SYS_TODAS, "TODAS");
$xCtrl->setOptionSelect(SYS_TODAS);

$xRPT->addControl( $xCtrl->get(true), "iddestinodecredito", "destino" );
$xRPT->addControl($xChk->get("TR.Incluir Otros Datos", "idotrosd"), "idotrosd", "otrosdatos", true);
$xRPT->addControl($xChk->get("TR.Datos Compacto", "idcompacto"), "idcompacto", "compacto", true);
$xRPT->addControl($xChk->get("TR.OMITIRCEROS", "nocero"), "nocero", "nocero", true);
//$xRPT->addTipoDeOperacion();
//$xRPT->setConRecibos(false);
echo $xRPT->get();

echo $xRPT->getJs(true);

$xHP->fin();
?>