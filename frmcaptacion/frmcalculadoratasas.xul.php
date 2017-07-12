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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$oficial = elusuario($iduser);
/**
 * Funciones con TinyAjax
 */
	require_once(TINYAJAX_PATH . "/TinyAjax.php");
	$jxc = new TinyAjax();
function calcula_tasa($monto=0, $tipo=10, $form){
		/** @deprecated */
		$tasa = obtentasa($monto, $tipo);
		$tab = new TinyAjaxBehavior();
		$tab -> add(TabSetValue::getBehavior("itasa", $tasa));
		return $tab -> getString();
}
	$jxc ->exportFunction('calcula_tasa', array('imonto', "itipocta", "frmcalctasa"));	
	$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Calculadora Simple para Tasas de Captacion</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php 
			$jxc ->drawJavaScript(false, true);
?>
<body>
<hr />
	<p class="frmTitle"><script> document.write(document.title); </script></p>
<hr />

<form name="frmcalctasa" method="post" action="">
	<table border='0'  >
		<tr>
			<td>Tipo de Cuenta</td>
			<td><select name="tipocta" id="itipocta">
					<option value="10">Cuenta Corriente</option>
					<option value="20">Inversion a Plazo Fijo</option>
				</select>
			</td>
			<td><input type="button" name="calctasa" onclick="calcula_tasa();" value="Calcular Tasa" /></td>
		</tr>
		<tr>
			<td>Monto a Invertir o Saldo Diario Promedio</td>
			<td><input type='text' name='cmonto' value='' id='imonto'></td>
			<td>Tasa a Otorgar</td>
			<td><input type='text' name='ctasa' value='' id='itasa'></td>
		</tr>
	</table>
</form>
</body>
</html>
