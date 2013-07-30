<?php
namespace Kladr;

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
