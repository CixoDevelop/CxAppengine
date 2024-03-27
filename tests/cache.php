<?php

require_once('../sources/cache.php');

file_put_contents('./test.txt', 'UwU');

$cache = new \cx_appengine\cache('./test.txt');

if ($cache->read() == 'UwU') {
    echo("File reading work...\n");
} else {
    echo("File reading not work...\n");
    echo("Content: ".$cache->read()."\n");
}

file_put_contents('test.txt', 'QwQ');

if ($cache->read() == 'UwU') {
    echo("Cache work...\n");
} else {
    echo("Cache not work...\n");
    echo("Content: ".$cache->read()."\n");
}

?>
