<?php
$app->post('/servicioempleado', "ServiciosEmpleadoControl:guardarserviciosempleado");
$app->get('/servicioempleado/{id}', "ServiciosEmpleadoControl:servicioxempleado");