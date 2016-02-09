<?php

  $config = array(
      'driver'    => 'mysql',
      'host'      => 'localhost',
<<<<<<< Updated upstream
      'database'  => 'turnobd',
=======
      'database'  => 'bdhotel',
>>>>>>> Stashed changes
      'username'  => 'root',
      'password'  => '',
      'collation' => 'utf8_general_ci',
      'prefix'    => '',
      'charset'   => 'utf8'
  );

  $container = new Illuminate\Container\Container;
  $connFactory = new \Illuminate\Database\Connectors\ConnectionFactory($container);
  $conn = $connFactory->make($config);
  $resolver = new \Illuminate\Database\ConnectionResolver();
  $resolver->addConnection('default', $conn);
  $resolver->setDefaultConnection('default');
  \Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);
