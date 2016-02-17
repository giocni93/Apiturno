<?php
$app->post('/servicio',"ServicioControl:postServicios");
$app->get('/servicio',"ServicioControl:getAll");