<?php

namespace AmoCRM;

class AmoLead extends AmoObject {
    
    const URL = '/api/v2/leads';

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
}

