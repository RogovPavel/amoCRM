<?php

namespace AmoCRM;

class AmoLead extends AmoObject {
    
    public $sale;
    
    function __construct($name, $sale) {
        parent::__construct($name);
        
        $this->sale = $sale;
    }
    
    // Создаем сделку из массива
    public static function fromArray($array) {
        $lead = new self($array['name'], $array['sale']);
        foreach ($array as $key => $value) {
            if (property_exists($lead, $key))
                $lead->$key = $value;
        }
        return $lead;
    }
    // Формируем из сделки массив для дальнейшей передачи в API
    public function toArray() {
        return (array)$this;        
    }
    
}

