<?php

  $config = array(
      'driver'    => 'mysql',
      'host'      => 'localhost',
      'database'  => 'turnobd',
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

  $capsule = new Illuminate\Database\Capsule\Manager;
  $capsule->addConnection($config);
  $capsule->setEventDispatcher(new Illuminate\Events\Dispatcher(new Illuminate\Container\Container));
  $capsule->setAsGlobal();
  $capsule->bootEloquent();