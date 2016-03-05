<?php

$app->post('/calificacion/cliente', "CalificacionClienteControl:post");
$app->get('/cliente/{idCliente}/calificacion', "CalificacionClienteControl:promedio");