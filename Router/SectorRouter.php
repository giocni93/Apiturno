<?php
 $app->get('/sector', "SectorControl:getAll");

 $app->post('/sector',"SectorControl:postSector");

 $app->get('/sector/{id}/posicion/{latitud}/{longitud}/empresas', "SectorControl:getSectorEmpresas");
