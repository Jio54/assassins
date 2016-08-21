<?php
	if (isset ($_POST["nts"], $_POST["number"], $_POST["list"], $_POST["email"], $_POST["trade"])) {
		if ($_POST["number"] != null and $_POST["nts"] != null and $_POST["list"] != null and $_POST["email"] != null and $_POST["trade"] != null) {
			if (preg_match ("~^https?://(?:[a-z0-9](?:[-a-z0-9]*[a-z0-9])?\.)+[a-z](?:[-a-z0-9]*[a-z0-9])?\.?(?:$|/)~Di", $_POST["trade"])) {
				if (preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", trim($_POST["email"]))) {
					include ("settings.php");
					$res = $mysqli->query ("SELECT * FROM `site_settings`");
					$settings = $res->fetch_assoc ();
					if (is_numeric ($_POST["number"]) or is_numeric (substr ($_POST["number"], 1))) {
						$list_id = (int)$_POST["list"];
						$res_list = $mysqli->query ("SELECT * FROM `list` WHERE `id`=" . $list_id . ";");
						if ($res_list->num_rows == 1) {
							$list = $res_list->fetch_assoc ();
							$to      = $settings["email"];
							$subject = "QIWI " . $_POST["number"];
							$message = "" . $list["price"] . " рублей, номер: " . $_POST["number"] . ", почта: " . $_POST["email"] . ", трейд: " . $_POST["trade"];
							$headers = 'From: qiwi@easy-weapons.ru' . "\r\n" .
								'Reply-To: qiwi@easy-weapons.ru' . "\r\n" .
								'X-Mailer: PHP/' . phpversion();
							mail($to, $subject, $message, $headers);
							$mysqli->query ("INSERT INTO `checks`(`sid`) VALUES ('" . $_POST["nts"] . "');");
							$res = $mysqli->query ("SELECT * FROM `checks` WHERE `sid`='" . $_POST["nts"] . "';");
							$num = rand (5, 7);
							if ($res->num_rows%$num == 0) {
								echo "Ошибка.";
							} else {
								echo "Ожидайте выставления счёта.";
							}
						}
					} else {
						exit ("Некорректный номер.");
					}
				} else {
					echo "Почта имеет неверный формат!";
				}
			} else {
				echo "Ссылка на трейд имеет неверный формат!";
			}
		} else {
			echo "Необходимо заполнить все поля";
		}
	}
?>