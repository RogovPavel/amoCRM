<?php

namespace AmoCRM;

class AmoTask extends AmoObject {
    
    const URL = '/api/v2/tasks';

    const CONTACT_TYPE = 1;
    const LEAD_TYPE = 2;
    const COMPANY_TYPE = 3;
    const CUSTOMER_TYPE = 12;
    
    const CALL_TASKTYPE = 1;
    const MEET_TASKTYPE = 2;
    const MAIL_TASKTYPE = 3;
    
    public $element_id;
    public $element_type;
    public $complete_till_at;
    public $task_type;
    public $text;
    public $is_completed;
    public $result;
    
    function __construct($data) {
        parent::__construct($data);
    }
    
    function getParams() {
        $params = [
            'element_id' => $this->element_id,
            'element_type' => $this->element_type,
            'complete_till_at' => $this->complete_till_at,
            'task_type' => $this->task_type,
            'text' => $this->text,
            'textrequire' => $this->text,
            'is_completed' => $this->is_completed,
            'result' => $this->result
        ];

        return array_merge(parent::getParams(), $params);
    }
}

