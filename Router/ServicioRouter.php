<?php
$app->post('/servicio',"ServicioControl:postServicios");
$app->get('/servicio',"ServicioControl:getAll");
$app->put('/servicio/{id}',"ServicioControl:updateservicios");
$app->put('/servicio/estado/{id}',"ServicioControl:updateestado");
$app->put('/servicio/estado/desactivar/{id}',"ServicioControl:updateestadodesactivar");
$app->get('/sucursal/{idSucursal}/servicio',"ServicioControl:getServiciosBySucursal");
$app->get('/sector/{idSector}/servicios',"ServicioControl:getServiciosBySector");
