<?php

namespace AmoCRM;

class AmoContact extends AmoObject {
    
    const URL = '/api/v2/contacts';

    public $company;
    public $leads;
    public $customers;
    public $closest_task_at;
    
    
    function __construct($data) {
        parent::__construct($data);
    }
    
    function getParams() {
        $params = [
            'closest_task_at' => $this->closest_task_at
        ];
        
        if (count($this->company))
            $params['company_id'] = $this->company['id'];
        
        if (count($this->leads))
            $params['leads_id'] = $this->leads['id'];
        
        if (count($this->customers))
            $params['customers_id'] = $this->customers['id'];
        
        return array_merge(parent::getParams(), $params);
    }
    
    public function addLead($leads) {
        if (!is_array($leads))
            $leads = [$leads];
        
        if (isset($this->leads['id']))
            $this->leads['id'] = array_merge($this->contacts['id'], $leads);
        else 
            $this->leads['id'] = $leads;
    }
}

