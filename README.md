# amoCRM

### Методы AmoAPI

* ```AmoApi::auth($login, $key, $subdomain) ``` - аунтификация в системе, в случае успеха возвращает true иначе false
* ```AmoApi::request($query, $type = 'GET', $params = array()) ``` - отправка запроса, если запрос вернул ошибку возвращает false
* ```AmoApi::getErrorInfo() ``` - выводит на экран сообщение об ошибке и ее код
* ```AmoApi::get_info() ``` - выводит информацию об аккаунте
* ```AmoApi::get_leads($params = []) ``` - загружаем первые 500 сделок
* ```AmoApi::get_contacts($params = []) ``` - загружаем первые 500 контактов
* ```AmoApi::get_companies($params = []) ``` - загружаем первые 500 компаний
* ```AmoApi::get_tasks() ``` - загружаем первые 500 задач
* ```AmoApi::get_<название сущности> ``` - загружаем первые 500 строк сущности
