<?php

namespace AmoCRM;

class AmoObject {
    
    public $id;
    public $name;
    public $responsible_user_id;
    public $created_by;
    public $created_at;
    public $updated_at;
    public $account_id;
    public $custom_fields;
    
    function __construct($name) {
        $this->name = $name;
    }
    
    // Получаем кастомное поле по его ИД
    public function getCustomFieldByName($id) {
        $i = array_search($id, array_column($this->custom_fields, 'id'));
        return $this->custom_fields[$i];
    }
}
