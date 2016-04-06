<?php 
$app->get('/ingreso',"IngresoControl:getingreso");
$app->get('/ingreso/empresa/{idempresa}/{fechainicial}/{fechafinal}',"IngresoControl:contablidadempresa");
$app->get('/contabilidad/sector/{idSector}/{fechainicial}/{fechafinal}',"IngresoControl:contabilidadsector");
$app->get('/contabilidad/sectores/{idSector}',"IngresoControl:contabilidadsectores");
$app->get('/contabilidad/sucursal/{idsucursal}/{fechainicial}/{fechafinal}',"IngresoControl:contasucursales");
$app->get('/contabilidad/empleado/{idempleado}/{fechainicial}/{fechafinal}',"IngresoControl:contabilidadempleado");
