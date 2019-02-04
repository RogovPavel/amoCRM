<?php

namespace AmoCRM;

class AmoObject {
    
    const URL = '';
    
    public $id;
    public $name;
    public $responsible_user_id;
    public $created_by;
    public $created_at;
    public $updated_by;
    public $updated_at;
    public $account_id;
    public $custom_fields;
    public $tags;
    public $group_id;
    
    function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key))
                $this->$key = $value;
        }
        $this->tags = array_column($this->tags, 'name');
    }
    // Обновляем объект в Amo CRM
    public function update() {
        return AmoAPI::request($this::URL, 'POST', [
            'update' => [(array)$this]
        ]);
    }
    
    // Получаем кастомные поля по их ИД
    public function getCustomFieldByName($params) {
        if (!is_array($params))
            $params = [$params];
        
        return array_intersect_key($this->custom_fields, array_intersect(array_column($this->custom_fields, 'id'), $params));
    }
    // Устанавливаем значение кастомнуму полю
    public function setCustomFieldByName($params) {
        foreach ($params as $key => $value) {
            $idx = array_search($key, array_column($this->custom_fields, 'id'));
            if ($idx !== false)
                $this->custom_fields[$idx]['values'] = [['value' => $value]];
            else
                $this->custom_fields[] = ['id' => $key, 'values' => [['value' => $value]]];
        }
        
        return $this->custom_fields[$i];
    }
    // Добавляем тег
    public function addTag($params) {
        if (!is_array($params))
            $params = [$params];
        
        $this->tags = array_merge($this->tags, $params);
    }
    // Удаляем тег
    public function delTag($params) {
        if (!is_array($params))
            $params = [$params];
        
        $this->tags = array_diff($this->tags, $params);
    }
}
