<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require '/src/AmoAPI.php';


AmoCRM\AmoAPI::auth('pasha-rogov@yandex.ru', '6b3eedab9d878bbeea81370c17832ce16c8e80bf', 'pasharogov');

$data = AmoCRM\AmoAPI::get_leads();

if ($data) {
    echo '<pre>';
    print_r($data['_embedded']['items'][0]);
    echo '</pre>';
    
    $arr = $data['_embedded']['items'][0];
    $lead = AmoCRM\AmoLead::fromArray($arr);
    
    print_r($lead->getCustomFieldByName(285179));
}
else
    AmoCRM\AmoAPI::getErrorInfo(); 
