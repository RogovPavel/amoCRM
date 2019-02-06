<?php

require 'src/AmoAPI.php';

use AmoCRM\AmoAPI;
use AmoCRM\AmoLead;
use AmoCRM\AmoContact;
use AmoCRM\AmoCompany;
use AmoCRM\AmoTask;
use AmoCRM\AmoNote;


AmoAPI::auth('pasha-rogov@yandex.ru', '6b3eedab9d878bbeea81370c17832ce16c8e80bf', 'pasharogov');


if (count($_POST)) {
    $data = serialize($_POST);
    file_put_contents('log.txt', $data);
}

$data = file_get_contents('log.txt');
$array = unserialize($data);

echo '<pre>';
print_r($array);
echo '</pre>';


