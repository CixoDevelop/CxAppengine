<?php

require_once(__DIR__.'/../require.php');

function test(\cx_appengine\validator $validator, ?string $content) {
    $validator_type = $validator->get_type();
    $type = gettype($content);

    if ($validator->validate($content)) {
        $new_content = $validator->parse($content);
        $new_type = gettype($new_content);

        echo("$type: '$content' is validated as $validator_type!\n");
        echo("After validation content is $new_type: $new_content\n");
    } else {
        echo("$type: '$content' is not validated as $validator_type!\n");
    }

    echo("\n");
}

$email = new \cx_appengine\validator('?email');
$phone = new \cx_appengine\validator('phone');
$bool = new \cx_appengine\validator('bool');
$int = new \cx_appengine\validator('int');
$numeric = new \cx_appengine\validator('numeric');

test($email, "cixo@laptop.com");
test($email, "");
test($email, null);

test($phone, '123456789');
test($phone, '+1123456789');
test($phone, '+100123456789');
test($phone, '+48123456789');
test($phone, '+123456789');
test($phone, '23456789');

test($bool, 'true');
test($bool, 'false');
test($bool, 'xd');

test($int, '10');
test($int, '-20');
test($int, '2.55');
test($int, 'uwu');


test($numeric, '-10');
test($numeric, '5.555');
test($numeric, '1.1');
test($numeric, 'xd');

?>
