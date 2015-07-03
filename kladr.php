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
	public function __construct($token, $key, $domain = false)
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

/**
 * Объект КЛАДР
 * @property-read string          $Id          Идентификатор объекта
 * @property-read string          $Name        Название объекта
 * @property-read string          $Zip         Почтовый индекс объекта
 * @property-read string          $Type        Тип объекта полностью (область, район)
 * @property-read string          $TypeShort   Тип объекта коротко (обл, р-н)
 * @property-read string          $ContentType Тип объекта из перечисления ObjectType
 * @property-read string          $Okato       ОКАТО объекта
 * @property-read \Kladr\Object[] $Parents     Массив родительских объектов
 */
class Object
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
				$this->arParents[] = new Object($obParent);
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

/**
 * Перечисление типов объектов
 */
class ObjectType
{
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

		return $string;
	}
}
