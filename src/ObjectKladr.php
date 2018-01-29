<?php

namespace Kladr;

/**
 * Объект КЛАДР
 * @property-read string               $Id          Идентификатор объекта
 * @property-read string               $Name        Название объекта
 * @property-read string               $Zip         Почтовый индекс объекта
 * @property-read string               $Type        Тип объекта полностью (область, район)
 * @property-read string               $TypeShort   Тип объекта коротко (обл, р-н)
 * @property-read string               $ContentType Тип объекта из перечисления ObjectType
 * @property-read string               $Okato       ОКАТО объекта
 * @property-read \Kladr\ObjectKladr[] $Parents     Массив родительских объектов
 */
class ObjectKladr
{
    private $id;
    private $name;
    private $zip;
    private $type;
    private $typeShort;
    private $okato;
    private $contentType;
    private $arParents;

    /**
     * @param $obObject
     */
    public function __construct($obObject)
    {
        $this->id          = $obObject->id;
        $this->name        = $obObject->name;
        $this->zip         = $obObject->zip;
        $this->type        = $obObject->type;
        $this->typeShort   = $obObject->typeShort;
        $this->okato       = $obObject->okato;
        $this->contentType = $obObject->contentType;

        $this->arParents = array();

        if (isset($obObject->parents)) {
            foreach ($obObject->parents as $obParent) {
                $this->arParents[] = new ObjectKladr($obParent);
            }
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'Id':
                return $this->id;
            case 'Name':
                return $this->name;
            case 'Zip':
                return $this->zip;
            case 'Type':
                return $this->type;
            case 'TypeShort':
                return $this->typeShort;
            case 'Okato':
                return $this->okato;
            case 'ContentType':
                return $this->contentType;
            case 'Parents':
                return $this->arParents;
        }
    }
}