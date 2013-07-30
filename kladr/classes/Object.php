<?php
namespace Kladr;

class Object {
    private $id;
    private $name;
    private $zip;
    private $type;
    private $typeShort;
    private $arParents;
    
    public function __construct($arObject) {
        $this->id = $arObject['id'];
        $this->name = $arObject['name'];
        $this->zip = $arObject['zip'];
        $this->type = $arObject['type'];
        $this->typeShort = $arObject['typeShort'];
        
        $this->arParents = array();
        
        if(isset($arObject['parents'])){
            foreach($arObject['parents'] as $arParent){
                $this->arParents[] = new Object($arParent);
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
