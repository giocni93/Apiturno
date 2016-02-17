<?php
 $app->get('/sector', "SectorControl:getAll");

 $app->post('/sector',"SectorControl:postSector");

 $app->get('/sector/{id}/empresas', "SectorControl:getSectorEmpresas");
