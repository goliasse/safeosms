Sistema de Administración Financiera y Estadística. SAFE Open Source Microfinance System.
Version 	: 2015.01.01
Codename 	: Crystel
Date		: 2015-01-22

S.A.F.E. is a "Open Source Microfinance Suite" created for help to the operational needs from the Microfinance institutions, developer with Web 2.0 technologies. 
S.A.F.E. OSMS es una alternativa  de Software para Microfinancieras(Cooperativas de Ahorro y Prestamo, SOFIPOS, SOFOLES y Similes) de Uso y Distribución Libre, pensado para ser fácil, rápido, económico y fiable.



Luis Balam.
http://www.opencorebanking.com/

================================== FEAUTURES =========================================

- AML/FT System.
- Cashier modules.
- Loans Modules.
- Saving Module.
- Reports.
- Security.
- Form editor.
- Accounting Module.
- Antimoney Laundering Module.
- English Version.
- have much...

================================== VERSION NOTES ======================================

	- This version not include script of update from 2012.04 version.
	- This software is advanced system... with multiple variables.
	- The hardest of this software is the configuration, no the installation.
	- See change section.

================================== FILES  =============================================

safe-osms.sql.tar.gz				Database Spanish version.
safe-osms-en.sql.tar.gz				Database English version(Partial traduction).
core.config.os.lin.inc.php_DIST		Database access configuration file (rename to core.config.os.lin.inc.php and copy on $ROOT_PATH/core folder).
contabilidad.sql.tar.gz				Contabilidad Demo spanish
xx.functions.sql					Database functions script (run after restore database, before views)
xx.vistas.sql						Database views script (run after restore database)
htdocs.tar.gz						Source code of System.

How to Install (Spanish) :	http://wiki.opencorebanking.com/doku.php?id=instalacion_y_actualizacion_del_sistema

================================== CONTACT  =============================================

Email me 		: admin@opencorebanking.com
				: patadejaguar@gmail.com
Website			: http://www.opencorebanking.com/
Comercial Site	: http://www.sipakal.com

Demo Site		: https	://demo.sipakal.com/
				: User 	: root
				: Pass	: root

Buy support		: http://www.sipakal.com/

================================== CHANGES ======================================
-------------------------------------- v12 ----------------------------------------
- Se agrega pre-forma de Balance General.
- Se agrega pre-forma de Estado de Resultados.
- Se universaliza el recibo de pagos/egresos.
- Se termina el reporte de operaciones relevantes.
- Se termina el reporte de operaciones preocupantes.
- Se termina el reporte de operaciones inusuales.
- Se agrega soporte de facturacion con http://facturacionmoderna.com de proveedor.
- Se mejora el esquema de pre-polizas.
- Se agrega alerta en Desembolsos de credito.
- Se refactoriza el reporte de Movimientos de Auxiliares (Contable).

-------------------------------------- v02 ----------------------------------------
- Se agregó el core.region.inc.php que contendrá las reglas para evaluar IDs de cada país.
- la function getRFC (ID Fiscal) ahora con default value, evaluate value y raw value.
- Se corrige y mejora avales.
- Se corrige y mejora Familiares.


- New register for Persons.
- New register for Legal Entities.
- New register for Informal groups.
- Check AML process for better detection.

- Nuevo registro para Personas Naturales.
- Nuevo registro para Personas Fisicas.
- Nuevo regitro para Grupos Informales.
- Nuevas prestaciones en el panel de control de personas.
- Mejoras en proceso de PLD/FT para mejor detección de Operaciones Preocupantes.
- Nueva Alta de Captación.
- Nuevo registro de Domicilio.
- Nuevo Registro de Actividades económicas.
- Nuevo corte de caja.
- Nuevo catalogo de Monedas.
- Nuevo Catalogo de régimen Fiscal.
- Nuevo registro de crédito.
- Nuevo Desembolso de créditos.
- Nuevo registro de Depositos a la Vista.
- Nuevo registro de Cuentas de captación.
- Nuevo registro del catalogo de tasa, sectorizado por tipo de producto y sub-producto.
- Nueva carga de documentación por FTP.
- Nuevo registro de Multas.
- Nuevo registro de Comisiones.
- Evaluacion de Reglas de Negocio al Castigar Credito.
- Evaluacion de Reglas de Negocio al Cancelar Credito.
- Evaluacion de Reglas de Negocio al Eliminar Persona.
- Nueva Funcionalidad de Castigo de Creditos.
- Registro de Usuarios/beneficiarios con ID distinto al RFC/CURP.
- Reduccion de alertas de email para recibos.
- Se Agregaron Datos Bancarios a los Creditos.
- Se Agrega confirmar eliminar en recibos.
- Mejora en las ubicaciones Locales, como estado, Municipio, etc.
- Nuevos reportes.
- Mejoras en actualización Vinculacion de Personas, Sucursales, Usuarios y Empresas.
- Mejoras al core de AML/PLD.
- Mejoras en la consulta SDN/OFACs.
- Ahora se utiliza Memcached.

- Mejoras en Módulo de AML.
- Soporte Reporte 24 Horas.
- Se agrega Lista de Parcialidades al menu de Credito.
- Soporte para Consultas en Litas negras impresas o por mail.
- Correccion en el Reporte de Círculo de Crédito.

- División de consultar para PEP.
- capacidad de Carga de datos Abiertos IFAI. para Posibles PEPs.
- Agregar Consulta de PEPS.- reporte de consulta.
- agregar personas morales en la lista sdn
- formato mejorado de consulta SDN.
- mejoras en domicilio de personas en SDN.
 

--- Bugs

- Correción Eliminar recibo en Bancos.
- Correción de Traspaso a Despedidos.
- Corte de caja no se envía en ceros.
- Cambios en algunos equivalentes de idioma.
- Mejoras en los cambios de creditos.
- Mejora en el Sistema de Mensajes.
- Se corrige el cierre de día de colocación.
- Fix english words.
- Se corrigen Varios bugs.


