<?php

namespace AmoCRM;

require 'AmoObject.php';
require 'AmoLead.php';
require 'AmoContact.php';
require 'AmoCompany.php';

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
        503 => 'Service unavailable',
        
        // Ошибки возникающие при работе с сделками
        213 => 'Добавление сделок: пустой массив',
        214 => 'Добавление/Обновление сделок: пустой запрос',
        215 => 'Добавление/Обновление сделок: неверный запрашиваемый метод',
        216 => 'Обновление сделок: пустой массив',
        217 => 'Обновление сделок: требуются параметры "id", "updated_at", "status_id", "name"',
        240 => 'Добавление/Обновление сделок: неверный параметр "id" дополнительного поля',
        
        
        // Ошибки возникающие при работе с контактами
        201 => 'Добавление контактов: пустой массив',
        202 => 'Добавление контактов: нет прав',
        203 => 'Добавление контактов: системная ошибка при работе с дополнительными полями',
        204 => 'Добавление контактов: дополнительное поле не найдено',
        205 => 'Добавление контактов: контакт не создан',
        206 => 'Добавление/Обновление контактов: пустой запрос',
        207 => 'Добавление/Обновление контактов: неверный запрашиваемый метод',
        208 => 'Обновление контактов: пустой массив',
        209 => 'Обновление контактов: требуются параметры "id" и "updated_at"',
        210 => 'Обновление контактов: системная ошибка при работе с дополнительными полями',
        211 => 'Обновление контактов: дополнительное поле не найдено',
        212 => 'Обновление контактов: контакт не обновлён',
        219 => 'Список контактов: ошибка поиска, повторите запрос позднее',
        
        330 => 'Количество привязанных контактов слишком большое',
    );
    
    private static $errorCode = [];
    
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
        foreach (self::$errorCode as $key => $value) 
            echo 'Ошибка: '. (isset(self::$errorCodes[$value]) ? self::$errorCodes[$value] : 'Неизвестная ошибка') . PHP_EOL . 'Код ошибки: ' . $value;
        
        echo (count(self::$errorCode) ? '' : 'По запросу данных не найдено');
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
            
            $response = json_decode($out, true);
            $response = $response['response'];
            
            if(isset($response['auth']))
                return true;
            else
                return false;
        }
    }
    
    // Метод для отправки запроса API
    public static function request($query, $type = 'GET', $params = array()) {
        
        $curl = curl_init();
        self::setDefaultCurlOpt($curl);
                
        if ($type == 'POST') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        
        if ($type == 'GET') {
            $query .= (count($params) ? '?' . http_build_query($params) : '');
        }
        
        curl_setopt($curl, CURLOPT_URL, self::$url . $query);
        
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($code != 200 && $code != 204) {
            self::$errorCode[] = $code;
            return false;
        } else {
            $response = json_decode($out, true);
            
            if (isset($response['_embedded']['errors'])) {
                if (isset($response['_embedded']['errors']['update']))
                    self::$errorCode = array_merge(self::$errorCode, array_column($response['_embedded']['errors']['update'], 'code'));
                if (isset($response['_embedded']['errors']['add']))
                    self::$errorCode = array_merge(self::$errorCode, array_column($response['_embedded']['errors']['add'], 'code'));
                
                return false;
            }
            else    
                return $response;            
        }
    }
    // Получаем информацию по аккаунту
    public static function get_info() {
        return self::request('/api/v2/account', 'GET', ['with' => 'custom_fields,users,pipelines,groups,note_types,task_types']);
    }
    // Загружаем сделки
    public static function get_leads($params = []) {
        return self::request(AmoLead::URL, 'GET', $params);
    }
    // Загружаем контакты
    public static function get_contacts($params = []) {
        return self::request(AmoContact::URL, 'GET', $params);
    }
    // Загружаем компании
    public static function get_companies($params = []) {
        return self::request(AmoCompany::URL, 'GET', $params);
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
    // Добаыление AmoObject в систему
    public static function addObjects($url, $params) {
        if (!is_array($params))
            $params = ['add' => [$params]];
        else
            $params = ['add' => $params];
        
        return AmoAPI::request($url, 'POST', $params);
    }
    
    // Добавление сделки
    public static function addLead($lead) {
        // Формируем массив входных параметров для запроса
        return self::addObjects(AmoLead::URL, $lead);
    }
    // Редактирование сделки
    public static function updateLead($lead) {
        // Формируем массив входных параметров для запроса
        return $lead->update();
    }
}




