<?php
//header("Content-type: text/plain");
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
	$xHP		= new cHPage("PAGARE", HP_REPORT);
$oficial 		= elusuario($iduser);
$idsolicitud 	= parametro("i", DEFAULT_CREDITO, MQL_INT); $idsolicitud = parametro("credito", $idsolicitud, MQL_INT); $idsolicitud = parametro("solicitud", $idsolicitud, MQL_INT);
$formato		= parametro("forma", 21, MQL_INT);
$xHP->addCSS("../css/contrato.css.php");
$xHP->init();

$xForma			= new cFormato($formato);
$xForma->setCredito($idsolicitud);

$xForma->setProcesarVars();
echo $xForma->get();

?>
</body>
</html>
