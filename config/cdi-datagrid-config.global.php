<?php
//AN EXAMPLE COLUMNS CONFIG

$config = array(
  "columnsConfig" => array(
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

