<?
	$site = "http://" . $_SERVER["HTTP_HOST"] . "/";
	if ($_SERVER["HTTP_REFERER"] == $site . "index.php" or $_SERVER["HTTP_REFERER"] == $site) {
		include ("settings.php");
		if (time ()%14 == 1) {
			$number = rand (0, 6);
			if ($number > 5) {
				$get_list = $mysqli->query ("SELECT * FROM `list` WHERE `cat` = 1;");
				$get = $mysqli->query("SELECT * FROM `list` WHERE `cat` = 1 ORDER BY rand() LIMIT " . $get_list->num_rows . ";");
			} else {
				$get_list = $mysqli->query ("SELECT * FROM `list` WHERE `cat` = 2;");
				$get = $mysqli->query("SELECT * FROM `list` WHERE `cat` = 2 ORDER BY rand() LIMIT " . $get_list->num_rows . ";");
			}
			if ($get_list->num_rows > 0) {
				$response = $get->fetch_assoc ();
				$id = $response["id"];
				$get_email = $mysqli->query("SELECT * FROM `mails`;");
				$email = $mysqli->query("SELECT * FROM `mails` ORDER BY rand() LIMIT " . $get_email->num_rows . ";");
				$eresponse = $email->fetch_assoc ();
				$mysqli->query ("INSERT INTO `purchases`(`list_id`, `email`) VALUES (" . $id . ", '" . $eresponse["email"] . "');");
			}
		}
		
		$get_purchases = $mysqli->query ("SELECT * FROM `purchases` ORDER BY `id` DESC LIMIT 0, 10;");
		if ($get_purchases->num_rows > 0) {
			for ($t = 0; $t < $get_purchases->num_rows; $t++) {
				$purchase_data = $get_purchases->fetch_assoc ();
				$get_list_id = $mysqli->query ("SELECT * FROM `list` WHERE `id`=" . $purchase_data["list_id"] . ";");
				if ($get_list_id->num_rows > 0) {
					$list_data = $get_list_id->fetch_assoc ();
					$email = explode ("@", $purchase_data["email"]);
					$num_str = ceil (strlen ($email[0])/2);
					$mail = substr ($email[0], 0, $num_str);
					for ($x = 0; $x < $num_str; $x++) {
						$mail .= "*";
					}
					$mail = $mail . "@" . $email[1];
	?>
		<div class="line-block-product">
			<a>
				<div class="line-grad<?=$list_data["rarity"]?>"></div>
				<img style="margin-top:-8px;" src="<?=$list_data["picture"]?>" width="139px" alt="">
			</a>
			<div class="email">
				<span><?=$list_data["name"]?></span>
				&#1055;&#1086;&#1082;&#1091;&#1087;&#1072;&#1090;&#1077;&#1083;&#1100;: <?=$mail?>
			</div>
		</div>
	<?
				}
			}
		}
	}
?>