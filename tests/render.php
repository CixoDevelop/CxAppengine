<?php

require_once(__DIR__.'/../require.php');

$templates_directory = new \cx_appengine\directory(__DIR__.'/templates/', 'html');
$templates = new \cx_appengine\templates($templates_directory);

echo($templates->prepare('index')->render([
    'UwU' => 'TEST',
    'A' => ['B' => 'C'],
    'D' => 'XD'
]));

?>
