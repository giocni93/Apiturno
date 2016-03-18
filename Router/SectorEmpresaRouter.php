<?php
$app->get('/sectorempresa/{id}',"SectorEmpresaControl:serviciosempresa");
$app->get('/sector/empresa/{id}',"SectorEmpresaControl:sectorxempresa");
$app->get('/reporte/sector/{idsector}/{fechainicial}/{fechafinal}',"SectorEmpresaControl:reportesector");
