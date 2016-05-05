<?php
$app->get('/sectorempresa/{id}',"SectorEmpresaControl:serviciosempresa");
$app->get('/sector/empresa/{id}',"SectorEmpresaControl:sectorxempresa");
$app->get('/reporte/sector/{idsector}/{fechainicial}/{fechafinal}',"SectorEmpresaControl:reportesector");
$app->get('/contabilidad/sectorempresa/{idSector}/{fechainicial}/{fechafinal}',"SectorEmpresaControl:contasector");
$app->get('/aplicareserva/{idSucursal}',"SectorEmpresaControl:aplicaReserva");
