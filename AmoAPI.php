<?php

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
    
    private static function setDefaultCurlOpt($curl) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); 
        curl_setopt($curl, CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    }
    
    public static function getErrorInfo() {
        echo 'Ошибка: '. (isset(self::$errorCodes[self::$errorCode]) ? self::$errorCodes[self::$errorCode] : 'Неизвестная ошибка') . PHP_EOL . 'Код ошибки: ' . self::$errorCode;
    }
    
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
            $response = json_decode($out, true);
            $response = $response['response'];
            
            if(isset($response['auth']))
                return true;
            else
                return false;
        }
    }
    
    public static function request($query) {
        
        $curl = curl_init();
        self::setDefaultCurlOpt($curl);
        curl_setopt($curl, CURLOPT_URL, self::$url . $query);
        
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        echo 'CODE: ' . $code . ';<br/>';
        
        if ($code != 200 && $code != 204) {
            self::$errorCode = $code;
            return false;
        } else {
            $response = json_decode($out, true);
            return $response;            
        }
    }
    
    public static function get_leads() {
        return self::request('/api/v2/leads');
    }
    
    public static function get_contacts() {
        return self::request('/api/v2/contacts');
    }
    
    public static function get_companies() {
        return self::request('/api/v2/companies');
    }
    
    public static function get_customers() {
        return self::request('/api/v2/customers');
    }
    
    public static function get_transactions() {
        return self::request('/api/v2/transactions');
    }
    
    public static function get_customers_periods() {
        return self::request('/api/v2/customers_periods');
    }
    
    public static function get_tasks() {
        return self::request('/api/v2/tasks');
    }
    
    public static function get_notes() {
        return self::request('/api/v2/notes?type=lead');
    }
    
    public static function get_incoming_leads() {
        return self::request('/api/v2/incoming_leads');
    }
    
    public static function get_incoming_leads_summary() {
        return self::request('/api/v2/incoming_leads/summary');
    }
    
    public static function get_pipelines() {
        return self::request('/api/v2/pipelines');
    }
    
    public static function get_webhooks() {
        return self::request('/api/v2/webhooks');
    }
    
    public static function get_widgets() {
        return self::request('/api/v2/widgets/list');
    }
}

AmoAPI::auth('pasha-rogov@yandex.ru', '6b3eedab9d878bbeea81370c17832ce16c8e80bf', 'pasharogov');

//$data = AmoAPI::request('/api/v2/account');
$data = AmoAPI::get_widgets();

if ($data)
    print_r($data);
else
    AmoAPI::getErrorInfo(); 