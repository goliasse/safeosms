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
$xHP			= new cHPage("TR.Reportes Acumulados");

$xHP->init();


$xRPT			= new cPanelDeReportes(0, "general_acumulados" );
$xChk			= new cHCheckBox();

$xRPT->setTitle($xHP->getTitle());
//$xRPT->addSucursales();
$xRPT->setConCreditos(true);
$xRPT->setConOperacion(true);
$xRPT->setConRecibos(false);

$xRPT->addControl($xChk->get("TR.OMITIRCEROS", "nocero"), "nocero", "nocero", true);

echo $xRPT->get();

echo $xRPT->getJs(true);

$xHP->fin();
 
?>