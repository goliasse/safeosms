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
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE);
$txt		= "";
$recibo		= parametro("recibo", false, MQL_INT);
$rs			= array("error" => true);
$recibo		= setNoMenorQueCero($recibo);
if($recibo > 0){
	$xPol		= new cPoliza(false);
	if ( $xPol->setPorRecibo($recibo) == true ){
		$rs	= array(
			"codigo" => $xPol->getCodigo(),
			"codigo_unico" => $xPol->getCodigoUnico()
		);
	}
}

header('Content-type: application/json');
echo json_encode($rs);
?>