<?php
$app->post('/servicio',"ServicioControl:postServicios");
$app->get('/servicio',"ServicioControl:getAll");
$app->get('/servicio/activos/{id}',"ServicioControl:getAllservicios");
$app->put('/servicio/{id}',"ServicioControl:updateservicios");
$app->put('/servicio/estado/{id}',"ServicioControl:updateestado");
$app->put('/servicio/estado/desactivar/{id}',"ServicioControl:updateestadodesactivar");