<?php
  class DB {

    public function set(string $key, string $value)
    {
      $file = fopen('db.json', 'r');
      $data = json_decode(fread($file, filesize('db.json')), true);
      fclose($file);
      $data[$key] = $value;
      $file = fopen('db.json', 'w');
      $json = json_encode($data);
      //$arr = json_decode(file_get_contents('db.json'), true);
      //if($arr == null) $arr = array();
      //$arr = array_merge($arr, array($key => $value));
      fwrite($file, $json);
      fclose($file);
      return $value;
    }

    public function get(string $key)
    {
      $file = fopen('db.json', 'r');
      $json = json_decode(fread($file, filesize('db.json')), true);
      fclose($file);
      return $json[$key];
    }
  }
