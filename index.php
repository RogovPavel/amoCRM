<?php

require 'src/AmoAPI.php';

use AmoCRM\AmoAPI;
use AmoCRM\AmoLead;
use AmoCRM\AmoContact;
use AmoCRM\AmoCompany;
use AmoCRM\AmoTask;
use AmoCRM\AmoNote;
use AmoCRM\AmoWebHook;


AmoAPI::auth('pasha-rogov@yandex.ru', '6b3eedab9d878bbeea81370c17832ce16c8e80bf', 'pasharogov');


$lead = new AmoLead();
$lead->getById(6832535);

echo '<pre>';
print_r($lead);
echo '</pre>';

echo '<pre>';
print_r($lead->getArrayFromCustomFields());
echo '</pre>';