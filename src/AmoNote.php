<?php

namespace AmoCRM;

class AmoNote extends AmoObject {
    
    const URL = '/api/v2/notes';

    const LEAD_CREATED_NOTETYPE = 1;
    const CONTACT_CREATED_NOTETYPE = 2;
    const LEAD_STATUS_CHANGED_NOTETYPE = 3;
    const COMMON_NOTETYPE = 4;
    const COMPANY_CREATED_NOTETYPE = 12;
    const TASK_RESULT_NOTETYPE = 13;
    const SYSTEM_NOTETYPE = 25;
    const SMS_IN_NOTETYPE = 102;
    const SMS_OUT_NOTETYPE = 103;
    
    const CONTACT_TYPE = 1;
    const LEAD_TYPE = 2;
    const COMPANY_TYPE = 3;
    const CUSTOMER_TYPE = 12;
    
    public $is_editable;
    public $element_id;
    public $element_type;
    public $text;
    public $note_type;
    public $result;
    
    function __construct($data) {
        parent::__construct($data);
    }
    
    function getParams() {
        $params = [
            'is_editable' => $this->is_editable,
            'element_id' => $this->element_id,
            'element_type' => $this->element_type,
            'text' => $this->text,
            'note_type' => $this->note_type,
            'result' => $this->result
        ];
        
        if (    $this->note_type == $this::SYSTEM_NOTETYPE ||
                $this->note_type == $this::SMS_IN_NOTETYPE ||
                $this->note_type == $this::SMS_OUT_NOTETYPE
            ) {
            $params['params'] = ['text' => $this->text];
        }

        return array_merge(parent::getParams(), $params);
    }
}

