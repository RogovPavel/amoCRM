<?php

require 'src/AmoAPI.php';

use AmoCRM\AmoAPI;
use AmoCRM\AmoLead;
use AmoCRM\AmoContact;


AmoAPI::auth('pasha-rogov@yandex.ru', '6b3eedab9d878bbeea81370c17832ce16c8e80bf', 'pasharogov');


$data = AmoAPI::get_contacts(['id' => 18130851]);

/*
 5328103
 5493735
 */


if ($data) {
    
    $c = new AmoContact($data['_embedded']['items'][0]);
    
    print_r($c->leads_id);
    echo '<br>';
    
//    $c->leads_id = ['5328103', '5493735'];
   
    $c->update();
    
    print_r($c->leads_id);
    echo '<br>';
    
    
    echo '<pre>';
    print_r($c);
    echo '</pre>';
}
 else {
    AmoAPI::getErrorInfo();
}

