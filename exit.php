<?php
	//при выходе из сеанса пользователя - удаляем куки
	setcookie("user_id", "", 0, "/");
	//перенаправляем браузер на страничку авторизации
	header("Location: /");
?>