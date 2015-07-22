<?php

namespace Kladr;

/**
 * Контроллер для доступа к сервису
 * @property-read string $Error Последняя ошибка
 */
class Api
{
    private $token;
    private $key;
	private $domain;

    private $error;

    /**
     * @param string $token
     * @param string $key
     */
    public function __construct($token, $key = '', $domain = false)
    {
        $this->token = $token;
        $this->key   = $key;
        $this->error = NULL;
		
		$this->domain = 'http://kladr-api.ru/';
		if($domain)
			$this->domain = $domain;		
    }

    private function GetURL(Query $query)
    {
        if (empty($this->token)) {
            $this->error = 'Токен не может быть пустым';
            return FALSE;
        }

        if (empty($query)) {
            $this->error = 'Объект запроса не может быть пустым';
            return FALSE;
        }

        return $this->domain . 'api.php?' . $query . '&token=' . $this->token;
    }

    /**
     * Возвращает результат запроса к сервису
     * @param \Kladr\Query $query Объект запроса
     * @param bool         $assoc Вернуть ответ в виде ассоциативного массива
     * @return bool|mixed
     */
    public function QueryToJson(Query $query, $assoc = FALSE)
    {
        $url = $this->GetURL($query);
        if (! $url) return FALSE;

        $context = stream_context_create(array('http' => array('header' => 'Connection: close\r\n')));

        $result = file_get_contents($url, FALSE, $context);

        if (preg_match('/Error: (.*)/', $result, $matches)) {
            $this->error = $matches[1];
            return FALSE;
        }

        return json_decode($result, $assoc);
    }

    /**
     * Возвращает результат запроса к сервису в виде массива
     * @param \Kladr\Query $query Объект запроса
     * @return array
     */
    public function QueryToArray(Query $query)
    {
        $arr = $this->QueryToJson($query, TRUE);
        return $arr['result'];
    }

    /**
     * Возвращает результат запроса к сервису в виде массива объектов
     * @param \Kladr\Query $query Объект запроса
     * @return \Kladr\Object[]
     */
    public function QueryToObjects(Query $query)
    {
        $obResult = $this->QueryToJson($query);
        if (! $obResult) return array();

        if (isset($obResult->searchContext->oneString)) {
            $this->error = 'Возвращение результата в виде объектов при ' .
                'поиске по всему адресу (одной строкой) невозможен';

            return array();
        }

        $arObjects = array();
        foreach ($obResult->result as $obObject) {
            $arObjects[] = new Object($obObject);
        }

        return $arObjects;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'Error':
                return $this->error;
        }
    }
}