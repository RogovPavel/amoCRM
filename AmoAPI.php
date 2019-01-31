<?php

class AmoObject {
    
    public $id;
    public $name;
    public $responsible_user_id;
    public $created_by;
    public $created_at;
    public $updated_at;
    public $account_id;
    public $custom_fields;
    
    function __construct($name) {
        $this->name = $name;
    }
}

class AmoLead extends AmoObject {
    
    public $sale;
    
    public $customFileds;
    
    function __construct($name, $sale = null) {
        parent::__construct($name);
        
        $this->sale = $sale;
    }
    
    // Создаем сделку из массива
    public static function fromArray($array) {
        $lead = new self($array['name'], $array['sale']);
        foreach ($array as $key => $value) {
            if (property_exists($lead, $key))
                $lead->$key = $value;
        }
        return $lead;
    }
    // Формируем из сделки массив для дальнейшей передачи в API
    public function toArray() {
        return (array)$this;        
    }
}

class AmoAPI {
    
    private static $url;
    
    private static $errorCodes = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable'
    );
    
    private static $errorCode;
    
    // Устанавливаем default настройки для cURL 
    private static function setDefaultCurlOpt($curl) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt'); 
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    }
    
    // Выводим на экран последнюю ошибку
    public static function getErrorInfo() {
        echo 'Ошибка: '. (isset(self::$errorCodes[self::$errorCode]) ? self::$errorCodes[self::$errorCode] : 'Неизвестная ошибка') . PHP_EOL . 'Код ошибки: ' . self::$errorCode;
    }
    
    // Метод авторизации в АМО СРМ, возвращаемый ключ сессии сохраняется в корневом каталоге cookie.txt
    public static function auth($login, $key, $subdomain) {
        
        self::$url = 'https://'.$subdomain.'.amocrm.ru';
        
        $user = array(
            'USER_LOGIN' => $login,
            'USER_HASH' => $key
        );
        
        $link = self::$url . '/private/api/auth.php?type=json';
        
        $curl = curl_init();
        self::setDefaultCurlOpt($curl);
        
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($user));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($code != 200 && $code != 204) {
            self::$errorCode = $code;
            return false;
        } else {
//            echo print_r($response);
            
            $response = json_decode($out, true);
            $response = $response['response'];
            
            if(isset($response['auth']))
                return true;
            else
                return false;
        }
    }
    
    // Метод для отправки запроса API
    public static function request($query, $type = 'GET', $params = NULL) {
        
        $curl = curl_init();
        self::setDefaultCurlOpt($curl);
        curl_setopt($curl, CURLOPT_URL, self::$url . $query);
        
        if ($type == 'POST') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }
        
        if ($params != NULL) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        
        echo self::$url . $query  . '<br/>';
        
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($code != 200 && $code != 204) {
            self::$errorCode = $code;
            return false;
        } else {
            $response = json_decode($out, true);
            return $response;            
        }
    }
    
    // Загружаем сделки
    public static function get_leads() {
        return self::request('/api/v2/leads');
    }
    // Загружаем контакты
    public static function get_contacts() {
        return self::request('/api/v2/contacts');
    }
    // Загружаем компании
    public static function get_companies() {
        return self::request('/api/v2/companies');
    }
    // Загружаем задачи
    public static function get_tasks() {
        return self::request('/api/v2/tasks');
    }
    // Загружаем контакты
    public static function get_notes() {
        return self::request('/api/v2/notes?type=lead');
    }
    // Загружаем неразобранные сделки
    public static function get_incoming_leads() {
        return self::request('/api/v2/incoming_leads');
    }
    // Загружаем статистику неразобранные сделки
    public static function get_incoming_leads_summary() {
        return self::request('/api/v2/incoming_leads/summary');
    }
    // Загружаем форонки продаж
    public static function get_pipelines() {
        return self::request('/api/v2/pipelines');
    }
    // Загружаем WebHooks
    public static function get_webhooks() {
        return self::request('/api/v2/webhooks');
    }
    // Загружаем Виджеты
    public static function get_widgets() {
        return self::request('/api/v2/widgets/list');
    }
    // Добавление сделки
    public static function addLead($lead) {
        // Формируем массив входных параметров для запроса
        $params = array(
            'add' => array(
                $lead->toArray()
            )
        );
        
        return self::request('/api/v2/leads/', 'POST', $params);
    }
    // Добавление сделки
    public static function updateLead($lead) {
        // Формируем массив входных параметров для запроса
        $params = array(
            'update' => array(
                $lead->toArray()
            )
        );
        
        return self::request('/api/v2/leads/', 'POST', $params);
    }
}

AmoAPI::auth('pasha-rogov@yandex.ru', '6b3eedab9d878bbeea81370c17832ce16c8e80bf', 'pasharogov');

//$lead = new AmoLead('Сделка по продаже!!!!', 3000);
////
//$data = AmoAPI::addLead($lead);
////
//if ($data) {
//    echo '<pre>';
//    print_r($data['_embedded']['items'][0]);
//    echo '</pre>';
//}
//else
//    AmoAPI::getErrorInfo(); 

$data = AmoAPI::get_leads();

if ($data) {
    echo '<pre>';
    print_r($data['_embedded']['items'][0]);
    echo '</pre>';
    $lead = AmoLead::fromArray($data['_embedded']['items'][0]);
    echo '<pre>';
    
//    $lead->sale = 700;
    AmoAPI::updateLead($lead);
    
    print_r($lead->toArray());
    echo '</pre>';
    
}
else
    AmoAPI::getErrorInfo(); 





