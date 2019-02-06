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
    
    public $_links;
    
    function __construct($data) {
        // Присваиваем полям значения
        foreach ($data as $key => $value) {
            if (property_exists($this, $key))
                $this->$key = $value;
        }        
    }
    // Функция приводит модель к формату для передачи в API
    public function getParams() {
        $params = [
            'id' => $this->id,
            'name' => $this->name,
            'responsible_user_id' => $this->responsible_user_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'account_id' => $this->account_id,
            'custom_fields' => $this->custom_fields,
            'group_id' => $this->group_id
        ];
        
        // Преобразуем теги в строку типа тег1, тег2, ...
        if (count($this->tags))
            $params['tags'] = implode(',', array_column($this->tags, 'name'));
        return $params;
    }
    
    // Обновляем\Добавляем объект в Amo CRM
    public function save() {
        $params = [];
        if (isset($this->id))
            $params = ['update' => [$this->getParams()]];
        else
            $params = ['add' => [$this->getParams()]];
        
        return AmoAPI::request($this::URL, 'POST', $params);
    }
    
    // Возвращаем кастомные поля по их ИД
    public function getCustomFieldByName($params) {
        if (!is_array($params))
            $params = [$params];
        
        return array_intersect_key(
                $this->custom_fields,
                array_intersect(
                        array_column($this->custom_fields, 'id'),
                        $params)
                );
    }
    
    // Устанавливаем значение кастомным полям
    public function setCustomFieldByName($params) {
        foreach ($params as $key => $value) {
            $field = [
                'id' => $key,
                'values' => [
                    ['value' => $value]
                ]
            ];
            
            $i = array_search(
                        $key,
                        array_column($this->custom_fields, 'id')
                    );
            
            if ($i !== false)
                $this->custom_fields[$i]['values'] = $field['values'];
            else
                $this->custom_fields[] = $field;
        }
    }
    
    // Функция добавления тега
    public function addTags($tags) {
        if (!is_array($tags))
            $tags = [$tags];
        // Проверяем наличе теги , если нет то добавляем
        foreach ($tags as $key => $value) {
            $tag = [
                'name' => $value
            ];
            
            if (array_search($value, array_column($this->tags, 'name')) === false)
                $this->tags[] = $tag;
        }
    
    }
    
    // Функция удаления тегов
    public function delTags($tags) {
        if (!is_array($tags))
            $tags = [$tags];
        
        $this->tags = array_diff_key($this->tags, array_intersect(array_column($this->tags, 'name'), $tags));
    
    }
}
