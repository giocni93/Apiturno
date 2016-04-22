<?php

$app->post('/galeria',"GaleriaControl:addgaleria");
$app->get('/galeria/{id}',"GaleriaControl:getgaleria");
$app->delete('/galeria/{id}',"GaleriaControl:eliminargaleria");
