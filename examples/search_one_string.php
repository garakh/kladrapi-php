<?
include '../kladr.php';

// Инициализация api, в качестве параметров указываем токен и ключ для доступа к сервису
$api = new Kladr\Api('51dfe5d42fb2b43e3300006e', '86a2c2a06f1b2451a87d05512cc2c3edfdf41969');

// Формирование запроса
$query              = new Kladr\Query();
$query->ContentName = 'москва, ленинский пр, 1';

$query->OneString = TRUE;
$query->Limit     = 5;

// Получение данных в виде ассоциативного массива
$arResult = $api->QueryToArray($query);

print '<pre>';
var_dump($arResult);
print '</pre>';