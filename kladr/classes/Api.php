<?php
namespace Kladr;

/**
 * Контроллер для доступа к сервису
 * @property-read string $Error Последняя ошибка
 */
class Api {
    private $token;
    private $key;
    
    private $error;
    
    public function __construct($token, $key) {
        $this->token = $token;
        $this->key = $key;
        $this->error = null;
    }
    
    private function GetURL(Query $query){
        if(empty($this->token)){
            $this->error = 'Токен не может быть пустым';
            return false;
        }
        
        if(empty($this->key)){
            $this->error = 'Ключ не может быть пустым';
            return false;
        }
        
        if(empty($query)){
            $this->error = 'Объект запроса не может быть пустым';
            return false;
        }        
        
        return 'http://kladr-api.ru/api.php?' . $query . '&token=' . $this->token . '&key=' . $this->key;
    }
    
    /**
     * Возвращает результат запроса к сервису в виде объекта Json
     * @param \Kladr\Query $query Объект запроса
     * @return mixed|boolean
     */
    public function QueryToJson(Query $query){
        $url = $this->GetURL($query);
        if(!$url) return false;        
        
        $result = file_get_contents($url);
        
        if(preg_match('/Error: (.*)/', $result, $matches)){
            $this->error = $matches[1];
            return false;
        }
        
        return json_decode($result);
    }
    
    /**
     * Возвращает результат запроса к сервису в виде массива
     * @param \Kladr\Query $query Объект запроса
     * @return array
     */
    public function QueryToArray(Query $query){       
        $obResult = $this->QueryToJson($query);        
        if(!$obResult) return array();
        
        $arResult = array();        
        foreach($obResult->result as $obObject){
            $arObject = array(
                'id' => $obObject->id,
                'name' => $obObject->name,
                'zip' => $obObject->zip,
                'type' => $obObject->type,
                'typeShort' => $obObject->typeShort,
            );
            
            if(isset($obObject->parents)){
                $arObject['parents'] = array();
                foreach($obObject->parents as $arParent){
                     $arObject['parents'][] = array(
                        'id' => $arParent->id,
                        'name' => $arParent->name,
                        'zip' => $arParent->zip,
                        'type' => $arParent->type,
                        'typeShort' => $arParent->typeShort,
                    );
                }
            }
            
            $arResult[] = $arObject;
        }
        
        return $arResult;
    }
    
    /**
     * Возвращает результат запроса к сервису в виде массива объектов
     * @param \Kladr\Query $query Объект запроса
     * @return Object[]
     */
    public function QueryToObjects(Query $query){
        $obResult = $this->QueryToJson($query);        
        if(!$obResult) return array();
        
        $arObjects = array();        
        foreach($obResult->result as $obObject){
            $arObjects[] = new Object($obObject);
        }
        
        return $arObjects;
    }
    
    public function __get($name) {
        switch($name){
            case 'Error': return $this->error;
        }
    }
}
