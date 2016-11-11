<?php
//AN EXAMPLE COLUMNS CONFIG

$config = array(
  "columns" => array(
      "aString" => [
          "type" => "string",
          "displayName" => "NewName"
      ],
      "aBoolean" => [
          "type" => "boolean",
          "valueWhenTrue" => "YES",
          "valueWhenFalse" => "NO"
          
      ]
      
  )  
);

return $config;

