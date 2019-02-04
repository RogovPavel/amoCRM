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
}

