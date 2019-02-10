<?php

namespace AmoCRM;

class AmoObject {
    
    const URL = '';
    
    const CONTACT_TYPE = 1;
    const LEAD_TYPE = 2;
    const COMPANY_TYPE = 3;
    const TASK_TYPE = 4;
    const CUSTOMER_TYPE = 12;
    
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
    
    // Заполняет модель значениями из массива
    function fill($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key))
                $this->$key = $value;
        }
    }
    
    function __construct($data) {
        // Присваиваем полям значения
        $this->fill($data);
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
            'updated_at' => isset($this->updated_at) ? $this->updated_at : time(),
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
            if (is_array($value))
                $field = [
                    'id' => $key,
                    'values' => $value
                ];
            else
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
    
    // Функция заполняет модель по $id
    public function getById($id) {
        $res = AmoAPI::request($this::URL, 'GET', ['id' => $id]);
        if ($res)
            $this->fill($res['_embedded']['items'][0]);
        
        return $res;
    }
    
    private function getElementType() {
        switch ($this::URL) {
            case '/api/v2/contacts':
                return AmoObject::CONTACT_TYPE;
                break;
            case '/api/v2/leads':
                return AmoObject::LEAD_TYPE;
                break;
            case '/api/v2/companies':
                return AmoObject::COMPANY_TYPE;
                break;
            case '/api/v2/tasks':
                return AmoObject::TASK_TYPE;
                break;
            case '/api/v2/customers':
                return AmoObject::CUSTOMER_TYPE;
                break;
            default:
                return false;
                break;
        }
    }
    
    // Добавялем примечание
    public function addNote($data = []) {
        // Формируем примечание
        if (!isset($data['element_id'])) {
            if (is_null($this->id))
                return false;
            else
                $data['element_id'] = $this->id;
        }
        
        if (!isset($data['element_type']))
            $data['element_type'] = $this->getElementType();
            
        if (!isset($data['note_type']))
            $data['note_type'] = AmoNote::COMMON_NOTETYPE;
        
        $note = new AmoNote($data);
        
        echo '<pre>';
        print_r($note->getParams());
        echo '</pre>';
        
        return $note->save();
    }
    // Добавление задачи
    public function addTask($data) {
        // Формируем задачу
        if (!isset($data['element_id'])) {
            if (is_null($this->id))
                return false;
            else
                $data['element_id'] = $this->id;
        }
        
        if (!isset($data['element_type']))
            $data['element_type'] = $this->getElementType();
            
        if (!isset($data['task_type']))
            $data['task_type'] = AmoTask::CALL_TASKTYPE;
        
        $task = new AmoTask($data);
        return $task->save();
    }
    
    // Получаем список кастомных полей ключ => значение
    public function getArrayFromCustomFields() {
        $result = [];
        foreach ($this->custom_fields as $key => $value) {
            if (count($value['values']) > 1)
                foreach ($value['values'] as $key2 => $value2) {
                    $result[$value['id']][] = isset($value2['enum']) ? $value2['enum'] : $value2['value'];
                }
            else
                $result[$value['id']] = isset($value['values'][0]['enum']) ? $value['values'][0]['enum'] : $value['values'][0]['value'];
        }
        return $result;
    }
}
