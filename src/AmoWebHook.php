<?php

namespace AmoCRM;

class AmoWebHook {
    const URL = '/api/v2/webhooks';
    
    public $data = [];
    
    function __construct($data) {
        if (isset($data['leads']))
            $this->data['leads'] = $data['leads'];
    }
    
    // Функция проверяет наличие хука
    public static function checkHook($data = []) {
        if (count($data) > 0)
            return isset($data['account']) ? true : false;
        else
            return isset($_POST['account']) ? true : false;
    }
    
    // Возвращаем массив AmoObjects add/update
    public function getAmoObjectsFromHook() {
        $result = [];
        
        foreach ($this->data as $key => $value) {
            foreach ($value['add'] as $key2 => $value2) {
                switch ($key) {
                    case 'leads':
                        $result['add'][] = new AmoLead($value2);
                        break;
                    default :
                        break;;
                }
                
            }    
        }
        
        return $result;
    }
}



