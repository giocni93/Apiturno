<?php
  //VARIABLES GLOBALES
  $rutaServidor = "http://localhost/PROYECTOS/ProyectoSlim/BuscaloApi/";

  function fechaHoraActual(){
    $tz_object = new DateTimeZone('America/Bogota');
    $datetime = new DateTime();
    $datetime->setTimezone($tz_object);
    return $datetime->format('Y-m-d h:i:s');
  }

  function formatearFecha($lista){
      foreach ($lista as $item) {
          $fecha = $item['fecha'];
          $item['hora'] = date("g:i a", strtotime($fecha));
          $item['fecha'] = date("d-m-Y", strtotime($fecha));
      }
  }

  function enviarNotificacion($array,$payload) {
      $apiKey = 'AIzaSyC1TNQb7IC15-PJLHbiGugkGylIQ6rXjZ4';
      $headers = array('Content-Type:application/json',"Authorization:key=$apiKey");

      $data = array(
          'data' => $payload,
          'registration_ids' => $array
      );

      $ch = curl_init();
      curl_setopt ($ch, CURLOPT_ENCODING, 'gzip');
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      $res = curl_exec($ch);
      curl_close($ch);
      return $res;
  }

  function enviarNotificacionIos($idPush,$notification) {
      $apiKey = 'AIzaSyC1TNQb7IC15-PJLHbiGugkGylIQ6rXjZ4';
      $headers = array('Content-Type:application/json',"Authorization:key=$apiKey");

      $data = array(
          'to' => $idPush,
          'content_available' => true,
          'notification' => $notification
      );

      $ch = curl_init();
      curl_setopt ($ch, CURLOPT_ENCODING, 'gzip');
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_URL, "https://gcm-http.googleapis.com/gcm/send");
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      $res = curl_exec($ch);
      curl_close($ch);
      return $res;
  }
