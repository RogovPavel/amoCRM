<?php

require 'src/AmoAPI.php';

use AmoCRM\AmoAPI;
use AmoCRM\AmoLead;


AmoAPI::auth('pasha-rogov@yandex.ru', '6b3eedab9d878bbeea81370c17832ce16c8e80bf', 'pasharogov');

$leads = AmoAPI::get_leads(['id' => 5520515]);

if ($leads) {
    $l = new AmoLead($leads['_embedded']['items'][0]);
    
    echo '<pre>';
    print_r($leads['_embedded']['items'][0]);
    echo '</pre>';
//    $l->update();
    
    
}
    else    AmoAPI::getErrorInfo();

