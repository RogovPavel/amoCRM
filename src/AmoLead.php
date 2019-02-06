<?php

namespace AmoCRM;

class AmoLead extends AmoObject {
    
    const URL = '/api/v2/leads';

    public $is_deleted;
    public $main_contact;
    public $company;
    public $closed_at;
    public $closest_task_at;
    public $contacts;
    public $status_id;
    public $sale;
    public $pipeline;
            
    
    function __construct($data) {
        parent::__construct($data);
    }
    
    function getParams() {
        $params = [
            'is_deleted' => $this->is_deleted,
            'closed_at' => $this->closed_at,
            'closest_task_at' => $this->closest_task_at,
            'status_id' => $this->status_id,
            'sale' => $this->sale,
            'pipeline_id' => $this->pipeline['id']
        ];

        if (count($this->company))
            $params['company_id'] = $this->company['id'];
        
        if (count($this->contacts))
            $params['contacts_id'] = $this->contacts['id'];
        
        return array_merge(parent::getParams(), $params);
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

