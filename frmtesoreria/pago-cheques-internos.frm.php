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
$xHP		= new cHPage("Pagos.- Cheques Propios");
$xJS		= new jsBasicForm("frmCobrosEnCheque");
//=========================== AJAX
$jxc 		= new TinyAjax();
$recibo		= parametro("idrecibo", 0, MQL_INT); $recibo		= parametro("r", $recibo, MQL_INT); $recibo		= parametro("recibo", $recibo, MQL_INT);

function jsaSetPago($Recibo, $cuentabancaria, $monto, $fecha, $cheque){
    $xF		= new cFecha(); 
    $fecha	= $xF->getFechaISO($fecha);
    
    $xCta	= new cCuentaBancaria($cuentabancaria);
    $msg	= "";
    if( $xCta->init() == true){
    	$xCta->setNuevoCheque($cheque, $cuentabancaria, $Recibo, "", $monto, $fecha);
    	$xRec	= new cReciboDeOperacion(false, false, $Recibo);
		if($xRec->init() == true){
			$xRec->setDatosDePago(AML_CLAVE_MONEDA_LOCAL, $monto, $cheque, TESORERIA_PAGO_CHEQUE, $cuentabancaria);
		}
    }
    //if(MODO_DEBUG == true){ setLog($xCta->getMessages()); }
}
$jxc ->exportFunction('jsaSetPago', array('iRecibo', 'idcuentabancaria', 'iMonto', 'idfechapago', 'idcheque'), '#avisos' );
$jxc ->process();

$xHP->init();

if(setNoMenorQueCero($recibo) <= 0){ header ("location:../404.php?i=" . DEFAULT_CODIGO_DE_ERROR); }
?> <style> #idavisopago, #idimporte, #iMonto { font-size : 1.3em !important; } </style> <?php
if( setNoMenorQueCero($recibo) >0 ){
	$xRec			= new cReciboDeOperacion(false, false, $recibo);
	$xRec->init();
	$DRec				= $xRec->getDatosInArray();
	$MontoOperacion		= $xRec->getTotal();// $DRec["total_operacion"];
	
    $xFRM	= new cHForm("frmPagosEnCheques", "pago-cheques-internos.frm.php");
    $xTxt	= new cHText("");
    $xDat	= new cHDate();
    $xHSel	= new cHSelect();
    $xHNot	= new cHNotif();
    $xFRM->addGuardar("jsActualizarPago()");
    $xTxt->addEvent("this.select()", "onfocus");
    $xTxt->addEvent("jsActualizarPago()", "onblur");
    $xFRM->addHElem( $xHSel->getListaDeCuentasBancarias("idcuentabancaria", true)->get( "TR.Cuenta Bancaria del Cheque", true) );
    $xFRM->addHElem( $xHNot->get($xHP->lang("importe") . " : " . getFMoney($MontoOperacion), "idimporte") );
    $xFRM->ODate("idfechapago", $xRec->getFechaDeRecibo(), "TR.Fecha del Cheque");
	$xFRM->addHElem( $xTxt->getDeMoneda("idcheque", "TR.Codigo de Cheque") );    
    $xFRM->OHidden("iMonto", $MontoOperacion, "");
    $xFRM->addHTML("<input type='hidden' id='iRecibo' name='iRecibo' value='$recibo' />");
    $xFRM->addHTML("<input type='hidden' id='iTotal' name='iTotal' value='$MontoOperacion' />");
    $xFRM->addHTML("<div id='avisos'></div>");
	    
    echo $xFRM->get();
    $jxc ->drawJavaScript(false, true);
}

?>
<script>
	var oMnt	= $("#iMonto");
	var oTot	= $("#iTotal");
	var oFecha	= $("#idfechapago");
	var oidcheq	= $("#idcheque");
	var oBanc	= $("#idcuentabancaria");
	var onEnd	= false;
	var xG		= new Gen();
	var xV		= new ValidGen();
function initComponents(){
	oBanc.focus(); oBanc.select();
	onEnd		= false;
}
function jsActualizarPago(){
	if( xV.NoCero(oidcheq.val()) == true){
		var sip	= confirm("Desea guardar el Pago?");
		if (sip) {	
			jsaSetPago();
			xG.disTime("#id-submit", 1000);
		try  {
			setTimeout("parent.jsRevalidarRecibo()", 500);
		} catch ( err ){}
		}
	} else {
		alert("El cheque no puede quedar vacio!");
		oidcheq.focus();
	}
}
</script>
<?php
echo $xHP->fin();
?>