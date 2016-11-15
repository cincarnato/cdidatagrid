<?php

//AN EXAMPLE COLUMNS CONFIG

$config = [
    "gridConfigOne" => [
        "doctrineSource" => [
            "entityName" => "\Application\Entity\Test",
            "entityManager" => "Doctrine\ORM\EntityManager"
        ],
        "columnsConfig" => [
            "refe" => [
                "type" => "string",
                "displayName" => "Name"
            ],
            "fecha" => [
                "type" => "date",
                "format" => "Y-m-d"
            ],
        ]
    ]
];

return $config;

