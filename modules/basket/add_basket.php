<?php

include $_SERVER['DOCUMENT_ROOT'].'/configs/db.php'; //поключаем БД к странице
/*
	При добавлении товара в корзину, НЕОБХОДИМО:
1. Получить товар, по которому кликнул пользователь
2. Добавить в массив товаров выбранный пользователем продукт
3. Добавить массив в куки

4. Перебрать прошлый массив
	4.1 Преобразовать JSON с куки в массив
	4.2 Добавить новый элемент в массив
	4.3 Преобразовать массив в правильный json
	4.4 Добавить в куки
*/

//если существует POST-запрос и метод запроса - POST
if (isset($_POST) and $_SERVER["REQUEST_METHOD"]=="POST") {

	//получаем при помощи sql запроса товар, по которому кликнул пользователь -переменная $product
	$sql = "SELECT * FROM items WHERE item_id = ".$_POST['id'];
	$result = $conn->query($sql);
	$product = mysqli_fetch_assoc($result);

	//если существует кука с массивом товаров в корзине, то
	if (isset($_COOKIE['basket'])) {
		$basket = json_decode($_COOKIE['basket'], true); //добавляем в переменную $basket куку в формате JSON в виде массива (параметр true)

		/*
		1. Пройтись повсему массиву корзины
		2. Проверить, есть ли совпадения по id
		3. Если воспадения есть - увеличить количество товара на 1
		*/

		$issetProduct = 0; //Переменная, в которой записывается показатель наличия товара в корзине
		for($i=0; $i<count($basket['basket']); $i++) {
			if ($basket['basket'][$i]['product_id'] == $product['item_id']) {
				$basket['basket'][$i]['count']++;
				$issetProduct=1;
			}
		}

		if ($issetProduct!=1) {
			$basket['basket'][]=[
				"product_id" => $product['item_id'],
				"count"=>1
			];
		}

		
		} else {  //если куки не существует, то есть корзина пуста
			//$basket = []; //создаём пустой массив товаров корзины
			$basket = ['basket'=> [
				["product_id" => $product['item_id'], //добавляем в массив $basket id выбранного пользователем товара
				"count"=>1] //количество делаем 1
			] ];
	}

	//преобразуем массив c выбранными товарами в формат JSON 
	$jsonProduct = json_encode($basket);

	setcookie("basket", "", 0, "/");//удаляем куку, если что-то осталось у нас в памяти

	//сохраняем втечение 1 часа куку "basket" хранение товара в корзине, "/" - кука будет доступна на всех страницах сайта
	setcookie("basket", $jsonProduct, time() + 60*60, "/");

	echo $jsonProduct;

}
?>