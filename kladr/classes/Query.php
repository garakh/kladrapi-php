<?php
namespace Kladr;

class Query {
    private $contextType;
    private $contextId;
    
    private $contentType;
    private $contentName;
    
    private $withParent;
    private $limit;
    
    public function __construct($contextType=null, $contextId=null, $contentType=null, $contentName=null, $withParent=null, $limit=null) {
        $this->contextType = $contextType;
        $this->contextId = $contextId;
        $this->contentType = $contentType;
        $this->contentName = $contentName;
        $this->withParent = $withParent;
        $this->limit = $limit;
    }
    
    public function __get($name) {
        switch($name){
            case 'ContextType': return $this->contextType;
            case 'ContextId': return $this->contextId;
            case 'ContentType': return $this->contentType;
            case 'ContentName': return $this->contentName;
            case 'WithParent': return $this->withParent;
            case 'Limit': return $this->limit;
            default: null;
        }
    }
    
    public function __set($name, $value) {
        switch($name){
            case 'ContextType': $this->contextType = $value; break;
            case 'ContextId': $this->contextId = $value; break;
            case 'ContentType': $this->contentType = $value; break;
            case 'ContentName': $this->contentName = $value; break;
            case 'WithParent': $this->withParent = $value; break;
            case 'Limit': $this->limit = $value; break;
        }
    }
    
    public function __toString() {
        $string = '';
        
        if($this->contextType && $this->contextId){
            $string .= $this->contextType . 'Id=' . $this->contextId;
        }
        
        if($this->contentName){
            if(!empty($string)) $string .= '&';
            $string .= 'query' . $this->contentName; 
        }
        
        if($this->contentType){
            if(!empty($string)) $string .= '&';
            $string .= 'contentType' . $this->contentType;
        }
        
        if($this->withParent){
            if(!empty($string)) $string .= '&';
            $string .= 'withParent' . $this->withParent;
        }
        
        if($this->limit){
            if(!empty($string)) $string .= '&';
            $string .= 'limit' . $this->limit;
        }
        
        return $string;
    }
}