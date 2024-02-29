<?php

require_once('../sources/template.php');

$template = new \cx_appengine\template('./');

echo(var_dump($template->string_split('A.b.c', '.')));

$t_a = $template->render_content('<< a.b.c >> UwU << OwO >>', [
    'a' => ['b' => ['c' => 'QwQ' ]],
    'OwO' => 'TwT'
]);

if ($t_a === 'QwQ UwU TwT') {
    echo("Render content work...\n");
} else {
    echo("Render content not work...\n");
    echo("DUMP:\n");
    echo($t_a);
    echo("\n");
}

file_put_contents('./test_view.html', '<< a >> UwU << c >>');

$t_b = $template->render('test_view', [
    'a' => 'b',
    'c' => 'd'
]);

if ($t_b === 'b UwU d') {
    echo("Render content work...\n");
} else {
    echo("Render content not work...\n");
    echo("DUMP:\n");
    echo($t_b);
    echo("\n");
}

?>
