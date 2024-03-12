<?php

require_once(__DIR__.'/../require.php');

$a = new \cx_appengine\string_builder('UwU');
$a->push("\n");
echo($a);

$b = new \cx_appengine\string_builder(['QwQ', 'OwO', 'EwE']);
$b->push("\n");
echo($b);

$c = new \cx_appengine\string_builder('OwO/UwU   /XD');
echo(var_dump($c->split('/')));
echo(var_dump($c->split('/', false)));

$d = new \cx_appengine\string_builder('OwO UwU <<EwE>> QwQ');
echo($d->divide('<<')->left."\n");
echo($d->divide('<<')->right."\n");
echo($d->divide('<<')->right->divide('>>')->right."\n");

$e = new \cx_appengine\string_builder('OwO');
echo($e->push(' stop')->push_start('Start '));

?>
