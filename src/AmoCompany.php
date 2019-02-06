<?php

namespace AmoCRM;

class AmoCompany extends AmoObject {
    
    const URL = '/api/v2/companies';

    public $contacts;
    public $leads;
    public $closest_task_at;
    public $customers;
    
    function __construct($data) {
        parent::__construct($data);
    }
    
    function getParams() {
        $params = [
            'closest_task_at' => $this->closest_task_at,
        ];

        if (count($this->leads))
            $params['leads_id'] = $this->leads['id'];
        
        if (count($this->contacts))
            $params['contacts_id'] = $this->contacts['id'];
        
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
    
    function addContacts($contacts) {
        if (!is_array($contacts))
            $contacts = [$contacts];
        
        if (isset($this->contacts['id']))
            $this->contacts['id'] = array_merge($this->contacts['id'], $contacts);
        else 
            $this->contacts['id'] = $contacts;
    }
}

