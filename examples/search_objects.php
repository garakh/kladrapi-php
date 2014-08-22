<?php
include '../kladr.php';

use Kladr\Api;
use Kladr\Query;
use Kladr\Object;
use Kladr\ObjectType;

$token = '51dfe5d42fb2b43e3300006e';
$key = '86a2c2a06f1b2451a87d05512cc2c3edfdf41969';

$type = '';
$name = '';

$arResult = array();

if (isset($_POST['submit'])) {
	$type = $_POST['type'];
	$name = $_POST['name'];

	$api = new Api($token, $key);

	$query              = new Query();
	$query->ContentType = $type;
	$query->ContentName = $name;
	$query->Limit       = 50;

	$arResult = $api->QueryToObjects($query);
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>Поиск объектов</title>

	<style>
		.container {
			width: 800px;
			margin: 0 auto;
		}

		h1 {
			text-align: center;
		}

		form {
			width: 300px;
			margin: 0 auto;
		}

		label {
			width: 80px;
			display: inline-block;
		}

		select, input {
			width: 200px;
		}

		[type="submit"] {
			display: block;
			margin-left: 83px;
		}

		table {
			width: 100%;
			margin-top: 30px;
			border: 1px solid black;
			border-spacing: 0;
			border-collapse: collapse;
		}

		td, th {
			border: 1px solid black;
			padding: 3px;
		}
	</style>
</head>
<body>
<div class="container">
	<h1>Поиск объектов</h1>

	<form method="post">
		<label for="type">Тип</label>
		<select id="type" name="type">
			<option
				value="<?php echo ObjectType::Region ?>" <?php echo($type == ObjectType::Region ? 'selected' : '') ?>>
				Регион
			</option>
			<option
				value="<?php echo ObjectType::District ?>" <?php echo($type == ObjectType::District ? 'selected' : '') ?>>
				Район
			</option>
			<option value="<?php echo ObjectType::City ?>" <?php echo($type == ObjectType::City ? 'selected' : '') ?>>
				Населённый пункт
			</option>
			<option
				value="<?php echo ObjectType::Street ?>" <?php echo($type == ObjectType::Street ? 'selected' : '') ?>>
				Улица
			</option>
			<option
				value="<?php echo ObjectType::Building ?>" <?php echo($type == ObjectType::Building ? 'selected' : '') ?>>
				Строение
			</option>
		</select>
		<br>
		<label for="name">Название</label>
		<input id="name" type="text" name="name" value="<?php echo $name ?>">
		<br>
		<input type="submit" name="submit" value="Поиск">
	</form>
	<?php if (count($arResult) > 0): ?>
		<table>
			<tr>
				<th>Название</th>
				<th>Подпись</th>
				<th>Подпись коротко</th>
				<th>Почтовый индекс</th>
			</tr>
			<?php foreach ($arResult as $obItem): ?>
				<tr>
					<td><?php echo $obItem->Name ?></td>
					<td><?php echo $obItem->Type ?></td>
					<td><?php echo $obItem->TypeShort ?></td>
					<td><?php echo $obItem->Zip ?></td>
				</tr>
			<?php endforeach ?>
		</table>
	<?php endif ?>
</div>
</body>
</html>
