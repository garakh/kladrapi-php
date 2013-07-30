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

/**
 * Объект КЛАДР
 * @property-read string $Id Идентификатор объекта
 * @property-read string $Name Название объекта
 * @property-read string $Zip Почтовый индекс объекта
 * @property-read string $Type Тип объекта полностью (область, район)
 * @property-read string $TypeShort Тип объекта коротко (обл, р-н)
 * @property-read Object[] $Parents Массив родительских объектов
 */
class Object {
    private $id;
    private $name;
    private $zip;
    private $type;
    private $typeShort;
    private $arParents;
    
    public function __construct($obObject) {
        $this->id = $obObject->id;
        $this->name = $obObject->name;
        $this->zip = $obObject->zip;
        $this->type = $obObject->type;
        $this->typeShort = $obObject->typeShort;
        
        $this->arParents = array();
        
        if(isset($obObject->parents)){
            foreach($obObject->parents as $obParent){
                $this->arParents[] = new Object($obParent);
            }
        }
    }
    
    public function __get($name) {
        switch($name){
            case 'Id': return $this->id;
            case 'Name': return $this->name;
            case 'Zip': return $this->zip;
            case 'Type': return $this->type;
            case 'TypeShort': return $this->typeShort;
            case 'Parents': return $this->arParents;
        }
    }
}

/**
 * Перечисление типов объектов
 */
class ObjectType {
    /**
     * Регион
     */
    const Region = 'region';
    
    /**
     * Район
     */
    const District = 'district';
    
    /**
     * Населённый пункт
     */
    const City = 'city';
    
    /**
     * Улица
     */
    const Street = 'street';
    
    /**
     * Строение
     */
    const Building = 'building';
}

/**
 * Класс запроса
 * @property string $ParentType Тип родительского объекта для ограничения области поиска (регион, район, город)
 * @property string $ParentId Идентификатор родительского объекта
 * @property string $ContentType Тип искомых объектов (регион, район, город)
 * @property string $ContentName Название искомого объекта (частично либо полностью)
 * @property boolean $WithParent Получить объекты вместе с родителями
 * @property integer $Limit Ограничение количества возвращаемых объектов
 */
class Query {
    private $parentType;
    private $parentId;
    
    private $contentType;
    private $contentName;
    
    private $withParent;
    private $limit;
    
    public function __construct($parentType=null, $parentId=null, $contentType=null, $contentName=null, $withParent=null, $limit=null) {
        $this->parentType = $parentType;
        $this->parentId = $parentId;
        $this->contentType = $contentType;
        $this->contentName = $contentName;
        $this->withParent = $withParent;
        $this->limit = $limit;
    }
    
    public function __get($name) {
        switch($name){
            case 'ParentType': return $this->parentType;
            case 'ParentId': return $this->parentId;
            case 'ContentType': return $this->contentType;
            case 'ContentName': return $this->contentName;
            case 'WithParent': return $this->withParent;
            case 'Limit': return $this->limit;
            default: null;
        }
    }
    
    public function __set($name, $value) {
        switch($name){
            case 'ParentType': $this->parentType = $value; break;
            case 'ParentId': $this->parentId = $value; break;
            case 'ContentType': $this->contentType = $value; break;
            case 'ContentName': $this->contentName = $value; break;
            case 'WithParent': $this->withParent = $value; break;
            case 'Limit': $this->limit = $value; break;
        }
    }
    
    public function __toString() {
        $string = '';
        
        if($this->parentType && $this->parentId){
            $string .= $this->parentType . 'Id=' . $this->parentId;
        }
        
        if($this->contentName){
            if(!empty($string)) $string .= '&';
            $string .= 'query=' . urlencode($this->contentName); 
        }
        
        if($this->contentType){
            if(!empty($string)) $string .= '&';
            $string .= 'contentType=' . $this->contentType;
        }
        
        if($this->withParent){
            if(!empty($string)) $string .= '&';
            $string .= 'withParent=1';
        }
        
        if($this->limit){
            if(!empty($string)) $string .= '&';
            $string .= 'limit=' . $this->limit;
        }
        
        return $string;
    }
}
