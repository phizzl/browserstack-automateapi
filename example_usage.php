<?php

use Phizzl\Browserstack\Api\AutomateApiClient;

require_once __DIR__ . '/vendor/autoload.php';

$api = new AutomateApiClient();
$api->setUsername('myuser1');
$api->setKey('8374nfdrehf378dnksa');

echo '<pre>';
var_dump($api->getStatus());
echo "--------------------\n";
var_dump($api->getProjects());
echo "--------------------\n";
var_dump($api->getBuilds());
echo "--------------------\n";