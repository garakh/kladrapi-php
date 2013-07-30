<?php
include '../kladr/autoload.php';

$api = new Kladr\Api('51dfe5d42fb2b43e3300006e', '86a2c2a06f1b2451a87d05512cc2c3edfdf41969');

$query = new Kladr\Query();
$query->ContentName = 'Арх';
$query->ContentType = Kladr\ObjectType::City;
$query->WithParent = true;
$query->Limit = 2;

$arResult = $api->QueryToArray($query);

print '<pre>';
print var_dump($arResult);
print '</pre>';