<?php
namespace Kladr;

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