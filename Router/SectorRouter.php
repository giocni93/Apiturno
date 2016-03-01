<?php
 $app->get('/sector', "SectorControl:getAll");

 $app->post('/sector',"SectorControl:postSector");

 $app->get('/sector/{id}/posicion/{latitud}/{longitud}/empresas', "SectorControl:getSectorEmpresas");

$app->put('/sector/{id}',"SectorControl:updatesector");

$app->put('/sector/estado/{id}',"SectorControl:updateestado");

$app->put('/sector/estado/desactivar/{id}',"SectorControl:updateestadodesactivar");

$app->get('/sector/maxid',"SectorControl:maxIdSector");

$app->put('/logosector/{id}', "SectorControl:putfotoservidor");

