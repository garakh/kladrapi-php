<?php

namespace Kladr;

/**
 * Класс запроса
 * @property string  $ParentType  Тип родительского объекта для ограничения области поиска (регион, район, город)
 * @property string  $ParentId    Идентификатор родительского объекта
 * @property string  $ContentType Тип искомых объектов (регион, район, город)
 * @property string  $ContentName Название искомого объекта (частично либо полностью)
 * @property string  $Zip         Почтовый индекс
 * @property boolean $OneString   Выполнить поиск по полной записи адреса, одной строкой
 * @property boolean $WithParent  Получить объекты вместе с родителями
 * @property integer $Limit       Ограничение количества возвращаемых объектов
 */
class Query
{
    private $parentType;
    private $parentId;

    private $contentType;
    private $contentName;

    private $zip;

    private $oneString;
    private $withParent;
    private $limit;

    private $typeCode;

    public function __construct()
    {
        $this->parentType  = NULL;
        $this->parentId    = NULL;
        $this->contentType = NULL;
        $this->contentName = NULL;
        $this->zip         = NULL;
        $this->oneString   = NULL;
        $this->withParent  = NULL;
        $this->limit       = NULL;
        $this->typeCode    = NULL;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'ParentType':
                return $this->parentType;
            case 'ParentId':
                return $this->parentId;
            case 'ContentType':
                return $this->contentType;
            case 'ContentName':
                return $this->contentName;
            case 'Zip':
                return $this->zip;
            case 'OneString':
                return $this->oneString;
            case 'WithParent':
                return $this->withParent;
            case 'Limit':
                return $this->limit;
            case 'TypeCode':
                return $this->typeCode;
            default:
                NULL;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'ParentType':
                $this->parentType = $value;
                break;
            case 'ParentId':
                $this->parentId = $value;
                break;
            case 'ContentType':
                $this->contentType = $value;
                break;
            case 'ContentName':
                $this->contentName = $value;
                break;
            case 'Zip':
                $this->zip = $value;
                break;
            case 'OneString':
                $this->oneString = $value;
                break;
            case 'WithParent':
                $this->withParent = $value;
                break;
            case 'Limit':
                $this->limit = $value;
                break;
            case 'TypeCode':
                $this->typeCode = $value;
                break;
        }
    }

    public function __toString()
    {
        $string = '';

        if ($this->parentType && $this->parentId) {
            $string .= $this->parentType . 'Id=' . $this->parentId;
        }

        if ($this->contentName) {
            if (! empty($string)) $string .= '&';
            $string .= 'query=' . urlencode($this->contentName);
        }

        if ($this->contentType) {
            if (! empty($string)) $string .= '&';
            $string .= 'contentType=' . $this->contentType;
        }

        if ($this->zip) {
            if (! empty($string)) $string .= '&';
            $string .= 'zip=' . $this->zip;
        }

        if ($this->oneString) {
            if (! empty($string)) $string .= '&';
            $string .= 'oneString=1';
        }

        if ($this->withParent) {
            if (! empty($string)) $string .= '&';
            $string .= 'withParent=1';
        }

        if ($this->limit) {
            if (! empty($string)) $string .= '&';
            $string .= 'limit=' . $this->limit;
        }

        if ($this->typeCode) {
            if (! empty($string)) $string .= '&';
            $string .= 'typeCode=' . $this->typeCode;
        }

        return $string;
    }
}
