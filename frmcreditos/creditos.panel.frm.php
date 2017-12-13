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
$xHP				= new cHPage("TR.Panel de Creditos");
$mSQL				= new cSQLListas();
$xRuls				= new cReglaDeNegocio();
$xT					= new cTipos();
$xF					= new cFecha();
//$xHP->setIncludeJQueryUI();

$UsarRedir			= $xRuls->getValorPorRegla($xRuls->reglas()->RN_USAR_REDIRECTS);		//regla de negocio

//Reglas de negocio
//memprof_enable();

/**
 * Define el Recibo
 */
$recibo_de_prestamo = "";
$persona			= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito			= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$idsocio			= $persona;
$idsolicitud		= $credito;
$idrecibo			= DEFAULT_RECIBO;
$CargarEstado		= false;


echo $xHP->getHeader();
$oFrm				= new cHForm("frmextrasol", "creditos.panel.frm.php");
$xJs				= new jsBasicForm("frmextrasol");
//$xJs->setEstatusDeCreditos($estatus);
$xJs->setIncludeJQuery();

$pathContrato		= "";
$pathPagare			= "";

$oFrm->setTitle($xHP->getTitle() );
?>
<body>
<?php
if ( setNoMenorQueCero($idsolicitud) <= DEFAULT_CREDITO) {
	$idsocio	= getPersonaEnSession();
	
	if($UsarRedir == true){
		$xHP->goToPageX("../utils/frmbuscarsocio.php?next=credito");
	}
	
	$oFrm->addCreditBasico();
	$oFrm->addSubmit();
	echo $oFrm->get();
	//echo $xJs->get();
	$idsolicitud	= DEFAULT_CREDITO;
		//exit( "<p class='aviso'>AGREGUE UN NUMERO DE SOLICITUD</p></body></html>");
} else {

	$oFrm->addRefrescar("jsRecargar()");
	
	//Tabs
	$xHTabs		= new cHTabs();
	$xBtn		= new cHButton("");
	$xCred 		= new cCredito($idsolicitud);
	$xNot		= new cHNotif();		
	if($xCred->init() == false ){
		$idsocio	= getPersonaEnSession();
		$oFrm->addToolbar($xBtn->getRegresar("../index.xul.php?p=frmcreditos/creditos.panel.frm.php", true));
		$oFrm->addCreditBasico();
		$oFrm->addSubmit();
		$oFrm->addAviso($xCred->getMessages());
				
	} else {
		$xCred->setRevisarSaldo();
		//Define si forza la carga de estado
		if($xF->getInt($xCred->getFechaUltimoDePago()) >= $xF->getInt(fechasys()) ) {
			$CargarEstado	= true;
		}
		$idsocio	= $xCred->getClaveDePersona();
		$xOPdto		= $xCred->getOProductoDeCredito();
		
		$DEstatus	= ($CargarEstado == false) ? "" : $xCred->setDetermineDatosDeEstatus(fechasys(), true, true);
		
		if($idsocio != $xCred->getClaveDePersona()){
			$msg		= "ERROR\tLa Persona $idsocio no es la propietaria del credito, el credito marca " .$xCred->getClaveDePersona() . "\r\n";
			$oFrm->addToolbar($xBtn->getRegresar("../index.xul.php?p=frmcreditos/creditos.panel.frm.php", true));
			$oFrm->addAviso($msg);
		} else {
			$oFrm->addHElem( $xCred->getFichaDeSocio(true) );
			$oFrm->addHElem( $xCred->getFicha(true, "", true) );
			$oFrm->OButton("TR.Panel de Persona", "var xP=new PersGen();xP.goToPanel($idsocio)", $oFrm->ic()->PERSONA, "irpanelpersona", "persona");
			$codigo_de_oficial	=  $xCred->getClaveDeOficialDeCredito();
			if(MODO_DEBUG == true){
				$oFrm->addToolbar($xBtn->getBasic("TR.EDICION_AVANZADA", "jsActualizarCreditoRAW()", "aviso", "edit-credito2", false ));
				//$oFrm->addToolbar($xBtn->getBasic("TESTING", "var xG=new Gen();xG.w({url:'../unit/core.creditos.test.php?credito=$idsolicitud'})", "checar", "test-cred", false ));
			}
			$oFrm->addCreditoComandos($idsolicitud, $xCred->getEstadoActual(), $xCred->getSaldoActual());
			
			if(getUsuarioActual(SYS_USER_NIVEL)>= USUARIO_TIPO_OFICIAL_CRED){
				$oFrm->OButton("TR.ACTUALIZAR DATOS", "jsActualizarCredito()", $oFrm->ic()->EDITAR, "editar-credito", "green2");
				
			}
			
			
			/*$oFrm->addToolbar( $xBtn->getBasic("TR.IMPRIMIR PAGARE", "printpagare()", "dinero", "view-pagare", false ) );
			$oFrm->addToolbar( $xBtn->getBasic("TR.CONTRATO", "contratocredito()", "imprimir", "print-contrato", false ) );*/
			
			/*if($xCred->getEsAfectable() == false){
				$oFrm->addToolbar( $xBtn->getBasic("TR.IMPRIMIR formato NOTARIAL", "cedulanotario($idsolicitud)", "reporte", "view-cedula", false ) );
				$oFrm->addToolbar( $xBtn->getBasic("TR.ORDEN_DE_DESEMBOLSO", "printodes()", "imprimir", "print-order", false ) );
				$oFrm->addToolbar( $xBtn->getBasic("TR.IMPRIMIR MANDATO", "printMandato()", "imprimir", "print-mandato", false ));
				$oFrm->OButton("TR.Ministrar en cuenta", "var xG = new CredGen();xG.setMinistrarToPasivo({credito:$idsolicitud})", $oFrm->ic()->BLOQUEAR);
			}
			$oFrm->addToolbar( $xBtn->getBasic("TR.IMPRIMIR RECIBO DE credito", "printrec()", "imprimir", "print-recP", false ));*/
			
			if($xCred->getEsAfectable() == true){
				$oFrm->OButton("TR.ESTADO_DE_CUENTA", "var xC=new CredGen();xC.getEstadoDeCuenta($idsolicitud);", $oFrm->ic()->ESTADO_CTA);
				//$oFrm->addToolbar( $xBtn->getBasic("TR.ESTADO_DE_CUENTA", "getEstadoDeCuenta($idsolicitud)", "statistics", "estado-cta", false ));
				//$oFrm->addToolbar( $xBtn->getBasic("TR.ESTADO_DE_CUENTA Intereses", "getEstadoDeCuentaIntereses($idsolicitud)", $oFrm->ic()->COBROS, "estado-cta2", false ));
				if(getUsuarioActual(SYS_USER_NIVEL)>= USUARIO_TIPO_OFICIAL_CRED){
					$oFrm->OButton("TR.REESTRUCTURAR", "jsRenegociar()", $oFrm->ic()->RECARGAR, "",  "red");
					$oFrm->OButton("TR.RENOVAR", "jsRenovar()", $oFrm->ic()->RECARGAR, "",  "yellow");
				}
			} else {
				if(MODULO_CAPTACION_ACTIVADO == true){
					$oFrm->OButton("TR.Ministrar en cuenta", "var xG = new CredGen();xG.setMinistrarToPasivo({credito:$idsolicitud})", $oFrm->ic()->BLOQUEAR);
				}
			}
			//$oFrm->OButton("TR.Calculadora", "var xC=new CredGen();xC.getCalculadora($idsolicitud);", $oFrm->ic()->CALCULAR);
			
			$idrecibo 			= $xCred->getNumeroReciboDeMinistracion();
			$idnumeroplan		= $xCred->getNumeroDePlanDePagos();
		
			if( setNoMenorQueCero($idnumeroplan) > 0) {
				$oFrm->OButton("TR.PLAN_DE_PAGOS", "var xC=new CredGen();xC.getImprimirPlanPagos($idnumeroplan);", $oFrm->ic()->CALENDARIO1);
				$oFrm->OButton("TR.Parcialidades Pendientes", "var xcg = new CredGen();xcg.getLetrasEnMora($idsolicitud)", $oFrm->ic()->PREGUNTAR);
			}
			if($codigo_de_oficial == USUARIO_TIPO_OFICIAL_AML OR OPERACION_LIBERAR_ACCIONES == true){
				
			}
			//Agregar Listado de Recibos
			$mSQL->setInvertirOrden();
			$cTblx	= new cTabla($mSQL->getListadoDeRecibos("", $xCred->getClaveDePersona(), $xCred->getNumeroDeCredito()));
			$cTblx->setKeyField("idoperaciones_recibos");
			$cTblx->setTdClassByType();
			$cTblx->setEventKey("jsGoPanelRecibos");
			$cTblx->setOmitidos("nombre");
			$cTblx->setOmitidos("socio");
			$cTblx->setOmitidos("documento");
			
			$cTblx->setFootSum(array(3 => "total"));
			$xHTabs->addTab("TR.RECIBOS", $cTblx->Show());
			
			if($xCred->isAFinalDePlazo() == false ){
				//$oFrm->addToolbar($xBtn->getBasic("TR.GENERAR PLAN_DE_PAGOS", "regenerarPlanDePagos()", "reporte", "generar-plan", false ) );
				if($xCred->getEsAfectable() == true OR $xCred->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO){
					$oFrm->OButton("TR.Generar PLAN_DE_PAGOS", "var xC=new CredGen();xC.getFormaPlanPagos($idsolicitud)", $oFrm->ic()->CALCULAR);
				} else {
					$oFrm->OButton("TR.SIMULAR PLAN_DE_PAGOS", "var xC=new CredGen();xC.getFormaSimPlanPagos($idsolicitud)", $oFrm->ic()->CALCULAR);
				}
				
				$xHTabs->addTab("TR.Plan_De_pagos", $xCred->getPlanDePago(OUT_HTML, false, true, true));
				if(getUsuarioActual(SYS_USER_NIVEL)>= USUARIO_TIPO_GERENTE){
					//$xHTabs->addTab("TR.Plan_De_pagos", $xCred->getPlanDePago(OUT_HTML, false, true, true));
				}
				if($xCred->getEsArrendamientoPuro() == true){
					//Agregar Leasing
					$xPlan			= new cPlanDePagos();
					$xPlan->setClaveDeCredito($xCred->getClaveDeCredito());
					$xHTabs->addTab("TR.RENTA", $xPlan->getVersionImpresaLeasing());
				}
			}
			
			if (getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED)){
				
				
					$setSql 	= $mSQL->getListadoDeLlamadas($idsolicitud); 
					$setSql3 	= $mSQL->getListadoDeNotificaciones($idsolicitud);
					$c2Tbl 		= new cTabla($mSQL->getListadoDeNotas(false, $idsolicitud), 0);
					$c2Tbl->addEditar(USUARIO_TIPO_OFICIAL_CRED);
					$c2Tbl->addEliminar(USUARIO_TIPO_OFICIAL_CRED);
					$c2Tbl->setEventKey("var xP=new PersGen();xP.getVerNota");
					
					$HNotas		= $c2Tbl->Show();
					if($c2Tbl->getRowCount()>0){ $xHTabs->addTab("TR.NOTAS", $HNotas); }
	
					$c4Tbl 			= new cTabla($mSQL->getListadoDeCompromisos($idsolicitud, SEGUIMIENTO_ESTADO_PENDIENTE), 0);
					$c4Tbl->OButton("TR.Ver", "var xS = new SegGen(); xS.getDetalleDeCompromiso({clave : " . HP_REPLACE_ID . "})", $oFrm->ic()->VER);
					$c4Tbl->OButton("TR.Editar", "var xS = new SegGen(); xS.setEditarCompromiso({clave : " . HP_REPLACE_ID . "})", $oFrm->ic()->EDITAR);
					$HCompromisos	= $c4Tbl->Show();
					if( $c4Tbl->getRowCount()>0){ $xHTabs->addTab("TR.COMPROMISOS" ,  $HCompromisos); }
	
					$cTbl 				= new cTabla($setSql,0);
					$cTbl->addTool(SYS_DOS);
					$cTbl->setKeyField("idseguimiento_llamadas");
					$oFrm->addHTML( $cTbl->getJSActions(true) );
					$HLlamadas			= $cTbl->Show();
					if($cTbl->getRowCount()>0){ $xHTabs->addTab("TR.LLAMADAS" , $HLlamadas ); }
	
					$c3Tbl 				= new cTabla($setSql3, 0);
					$HNotificaciones 	= $c3Tbl->Show();
					if($c3Tbl->getRowCount()>0){ $xHTabs->addTab("TR.NOTIFICACIONES" ,  $HNotificaciones); }
					
					//Imprime un Explain de porque el credito tiene este estatus
					if($CargarEstado == true){
						$xHTabs->addTab("TR.ESTATUS", $DEstatus );
					}
					$txtValidacion		= $xCred->setVerificarValidez(OUT_HTML);
					
					$xHTabs->addTab("TR.VALIDACION", $xNot->get( $txtValidacion, "idmsgval"));
					//avales
					$sqlavales 	= $mSQL->getListadoDeAvales($idsolicitud, $idsocio);
					$xTblAv		= new cTabla($sqlavales);
					$xTblAv->setFieldReplace("numero_socio", "_X_PERSONA_");
					$xTblAv->OButton("TR.PANEL", "var xP=new PersGen();xP.goToPanel(_X_PERSONA_)", $xTblAv->ODicIcons()->PERSONA);
					//$xTblAv->addEditar();
					$xTblAv->addEliminar();
					
					$HAvales	= $xTblAv->Show("TR.Relacion de Avales");
					if($xTblAv->getRowCount()>0){ $xHTabs->addTab("TR.AVALES", $HAvales); }
					
					$sql_final = $mSQL->getListadoDeGarantiasReales("", $idsolicitud);
					$myTab 		= new cTabla($sql_final);
					$myTab->addEditar();
					$myTab->addEliminar();
					

					$myTab->setKeyField("idcreditos_garantias");
					$HGarantias	= $myTab->Show();
					if($myTab->getRowCount()>0){ $xHTabs->addTab("TR.GARANTIAS", $HGarantias); }	

					
			}
			//==================================================== Historial de Nomina
			if($xCred->getEsNomina() == true){
				$oFrm->OButton("TR.HISTORIAL DE NOMINA", "jsHistorialDeNomina($idsolicitud)", $oFrm->ic()->REPORTE4);
			}
			//==================================================== Otros Datos
			$xTbOD		= new cTabla($mSQL->getListadoDeCreditosOtrosDatos($idsolicitud));
			$xTbOD->addEliminar(USUARIO_TIPO_OFICIAL_CRED);
			$xTbOD->addEditar(USUARIO_TIPO_OFICIAL_CRED);
			$HOtrosDatos	= $xTbOD->Show();
			if($xTbOD->getRowCount()>0){ $xHTabs->addTab("TR.Otros Datos", $HOtrosDatos); }
			$xUser	= new cSystemUser($xCred->getClaveDeUsuario());
			if($xUser->init() == true){
				
				$oFrm->addHElem( $xNot->get("Creado por : " . $xUser->getNombreCompleto(), "idnotcred", $xNot->NOTICE) );
			}
			//$oFrm
			if(getUsuarioActual(SYS_USER_NIVEL)>= USUARIO_TIPO_GERENTE){
				//Castigos
				if($xCred->getEstadoActual() == CREDITO_ESTADO_VENCIDO){
					$oFrm->OButton("TR.Castigos", "jsCastigos($idsolicitud)", "error");
				}
				//============= Tabla de Operaciones
				$sql		= $mSQL->getListadoDeOperaciones("", $idsolicitud);
				$cEdit		= new cTabla($sql);
				$cEdit->addTool(SYS_UNO);
				$cEdit->addTool(SYS_DOS);
				$cEdit->setTdClassByType();
				$cEdit->setKeyField("idoperaciones_mvtos");
				$HOperaciones=$cEdit->Show();
				if($cEdit->getRowCount()>0){ $xHTabs->addTab("TR.Operaciones", $HOperaciones); }
				
				$cMovs		= new cTabla($mSQL->getListadoDeSDPMCredito($idsolicitud));
				$HSDPM		= $cMovs->Show();
				if($cMovs->getRowCount()>0){ $xHTabs->addTab("TR.Historial", $HSDPM); }				
			}
			if(MODO_DEBUG == true){
			
				$xHTabs->addTab("TR.Sistema", $xCred->getMessages(OUT_HTML));
				$oFrm->addLog($xCred->getMessages());
			}			
			$oFrm->addHTML( $xHTabs->get() );
		}

		
	}
	//$oFrm->OButton("TR.PRUEBA", "jsLoadOption()", $oFrm->ic()->TAREA);
	
	//Cargar Controles de Arrendamiento
	if($xCred->getEsArrendamientoPuro() == true){
		//Agregar
		$oFrm->OButton("TR.VER COTIZADOR", "jsGetCotizacionArrendamiento(" . $xCred->getClaveDeOrigen() . ")", $oFrm->ic()->VEHICULO);
		$oFrm->OButton("TR.VER COTIZACION", "jsGetCotizacionArrendamientoRPT(" . $xCred->getClaveDeOrigen() . ")", $oFrm->ic()->REPORTE);
		$oFrm->OButton("TR.VER FLOTA", "jsGetFlota(" . $xCred->getClaveDeOrigen() . ")", $oFrm->ic()->TRUCK);
	}
	
	
	echo $oFrm->get();
?>
<script >
	var siAvales	= "si";
	var idCredito	= <?php echo $idsolicitud; ?>;
	var idSocio		= <?php echo $idsocio; ?>;
	var idRecibo	= <?php echo $idrecibo; ?>;
	var xRec		= new RecGen();
	var ogen		= new Gen();
	var xG			= new Gen();
	var xC			= new CredGen();
	
	function setNoAvales(){ siAvales = (document.getElementById("idNoAvales").checked) ? "no" : "si"; }
	
	function jsGoPanelRecibos(id){ xRec.panel(id); }
	
	
	function printplan(elplan) { ogen.w({ url: "../rpt_formatos/rptplandepagos.php?idrecibo=" + elplan + "&p=" + siAvales, tab:true }); }
	function jsActualizarCredito(){ ogen.w({ url: "../frmcreditos/credito.actualizar.frm.php?credito=" + idCredito, tab : true }); }
	
	function jsRenegociar(){ ogen.w({ url: "../frmcreditos/renegociar.frm.php?credito=" + idCredito, w:800, h:650, tiny : true }); }
	function jsRenovar(){ ogen.w({ url: "../frmcreditos/renovar.frm.php?credito=" + idCredito, w:800, h:650, tiny : true }); }
	
	function jsActualizarCreditoRAW(){ ogen.w({ url: "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=creditos_solicitud&f=numero_solicitud=" + idCredito }); }
	
	function gogarantias(){ ogen.w({ url: "../frmcreditos/frmcreditosgarantias.php?solicitud=" + idCredito }); }
	function goavales() { ogen.w({ url: "../frmcreditos/frmcreditosavales.php?credito=" + idCredito + "&socio=" + idSocio }); }
	function goflujoefvo(){ ogen.w({ url: "../frmcreditos/frmcreditosflujoefvo.php?solicitud=" + idCredito }); }
	
	function printsol() { ogen.w({ url: "../frmcreditos/rptsolicitudcredito1.php?solicitud=" + idCredito }); }
	

	function jsEditarPlan(mPlan){ ogen.w({ url: '../frmcreditos/plan_de_pagos.edicion.frm.php?i=' + mPlan }); }
	function getEstadoDeCuenta(idcredito){ ogen.w({ url: "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + idcredito }); }
	
	function jsRecargar(){ window.location = "creditos.panel.frm.php?idsolicitud=" + idCredito; }

	function regenerarPlanDePagos(){ ogen.w({ url: '../frmcreditos/frmcreditosplandepagos.php?r=1&c=' + idCredito + "&s=" + idSocio }); }
	function addLlamada(mKey){ }
	function addAviso(mKey){ }
	function addCompromiso(mKey){ }
	function jsCastigos(idcredito){ xG.w({ url: '../frmcreditos/castigo_de_cartera.frm.php?credito=' + idcredito, tiny : true, h: 400, w : 600 }); }
	function jsHistorialDeSaldos(idcredito){xG.w({ url: '../rpt_edos_cuenta/historial_de_saldos.rpt.php?credito=' + idcredito, full : true }); }
	function jsHistorialDeNomina(idcredito){xG.w({ url: '../rpt_edos_cuenta/historial_de_nomina.rpt.php?credito=' + idcredito, full : true }); }

	function jsGetCotizacionArrendamiento(id){
		xC.getLeasingCotizador(id);
	}
	function jsGetCotizacionArrendamientoRPT(id){
		xC.getLeasingCotizacion(id);
	}
	function jsGetFlota(id){
		xC.getLeasingActivos(id);
	}

</script>	
	<?php 
}
?>
</body>
<?php
//memprof_dump_callgrind(fopen("/tmp/cachegrind.out." . rand(0, 500), "w"));
//echo $xJs->get();
?>
</html>