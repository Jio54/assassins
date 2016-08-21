<?php
	if (isset ($_POST["sv"], $_POST["get"])) {
		if ($_POST["sv"] != null AND $_POST["get"] != null) {
			include ("settings.php");
			$res = $mysqli->query ("SELECT * FROM `site_settings`;");
			$data = $res->fetch_assoc ();
			if ($_POST["sv"] == "webmoney") {
				echo $data["webmoney"];
			} elseif ($_POST["sv"] == "yandex") {
				echo $data["yandex"];
			} elseif ($_POST["sv"] == "qiwi") {
				echo $data["qiwi"];
			}
		}
	}
?>