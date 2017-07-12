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
$xHP		= new cHPage("TR.ENVIO de ECONTABILIDAD", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$observaciones= parametro("idobservaciones");

$xHP->init();

$xSel		= new cHSelect();
$xRPT		= new cPanelDeReportesContables(true, false);
$xRPT->OFRM()->setTitle($xHP->getTitle());
$xRPT->addNivelesDeCuentas();
$xRPT->addListaDeReportes("econtabilidad");
echo $xRPT->render();
?>
<script>
var xG 	= new Gen();
function jsGetReporte(){
	var idejercicioinicial	= $("#idejercicioinicial").val();
	var idperiodoinicial	= $("#idperiodoinicial").val();
	var idniveldecuenta		= $("#idniveldecuenta").val();
	var idreporte			= $("#idreporte").val();
	var uRpt				= idreporte + "&ejercicio="+ idejercicioinicial + "&periodo=" + idperiodoinicial + "&nivel=" + idniveldecuenta;
	xG.w({url:uRpt});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>