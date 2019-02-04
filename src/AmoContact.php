<?php

namespace AmoCRM;

class AmoContact extends AmoObject {
    
    const URL = '/api/v2/contacts';

    public $company;
    public $leads;
    public $leads_id = [];
    public $closest_task_at;
    public $customers;
    
    function __construct($data) {
        parent::__construct($data);
        
        if (count($this->leads))
            $this->leads_id = array_column($this->leads, 'id');
    }
    
    public function addLead($params) {
        if (!is_array($params))
            $params = [$params];
        
        $this->leads_id = array_merge($this->leads_id, $params);
    }
}

