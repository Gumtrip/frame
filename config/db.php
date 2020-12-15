<?php
return [
    'class' => '\sf\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=sf',
    'username' => 'homestead',
    'password' => 'secret',
    'options' => [
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
    ],
];