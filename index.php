<?php

require '/src/AmoAPI.php';

use AmoCRM\AmoAPI;
use AmoCRM\AmoLead;


AmoAPI::auth('pasha-rogov@yandex.ru', '6b3eedab9d878bbeea81370c17832ce16c8e80bf', 'pasharogov');

// Создаем новую сделку
$lead = new AmoLead('Сделка по карандашам', 7500);
// Сохраняем ее в системе
$res = AmoAPI::addLead($lead);

if ($res) {
    // Выгружаем ее из системы
    $url = $res['_embedded']['items'][0]['_links']['self']['href'];
    $data = AmoAPI::request($url);
    
    //Выводим ее до редактирования
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
    // Делаем сделку из массива  и редактируем ее
    $lead = AmoLead::fromArray($data['_embedded']['items'][0]);
    $lead->name = 'Сделка по карандашам';
    $lead->sale = 12000;
    AmoAPI::updateLead($lead);
    
    //Выводим ее после редактирования
    $data = AmoAPI::request($url);
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
}



