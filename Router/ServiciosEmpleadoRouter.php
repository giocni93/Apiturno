<?php
$app->post('/servicioempleado', "ServiciosEmpleadoControl:guardarserviciosempleado");
$app->get('/servicioempleado/{id}', "ServiciosEmpleadoControl:servicioxempleado");
$app->delete('/servicioempleado/delete/{idempleado}',"ServiciosEmpleadoControl:eliminarservicios");