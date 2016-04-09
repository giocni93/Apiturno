<?php

$app->post('/galeria',"GaleriaControl:addgaleria");
$app->get('/galeria/{id}',"GaleriaControl:getgaleria");
