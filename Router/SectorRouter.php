<?php
 $app->get('/sector', "SectorControl:getAll");
 $app->post('/sector',"SectorControl:postSector");