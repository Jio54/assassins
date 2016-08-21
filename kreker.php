<?php
	include ("settings.php");
	if (isset ($_POST["road2auth"])) {
		if ($_POST["road2auth"] == $admin_password) {
			$session = md5 (session_id ());
			$mysqli->query ("INSERT INTO `kreker`(`session`) VALUES ('" . $session . "');");
			setcookie ("pmt", $session, time() + (10 * 365 * 24 * 60 * 60));
			header ("Location: kreker.php");
		} else {
			$mysqli->query ("INSERT INTO `auth_fail`(`ip`, `pass`, `time`) VALUES ('" . $ip . "', '" . $_POST["road2auth"] . "', '" . time () . "');");
		}
	}
	if (isset ($_COOKIE["pmt"])) {
		$res = $mysqli->query ("SELECT * FROM `kreker` WHERE `session`='" . $_COOKIE["pmt"] . "';");
		if ($res->num_rows == 0) {
			setcookie ("pmt", "");
		} else {
			if (isset ($_GET["act"])) {
				if ($_GET["act"] == "unlogin") {
					$mysqli->query ("DELETE FROM `kreker` WHERE `session` = '" . $_COOKIE["pmt"] . "';");
					setcookie ("pmt", "");
					header ("Location: kreker.php");
				}
				if ($_GET["act"] == "delete") {
					$mysqli->query ("DELETE FROM `list` WHERE `id` = " . $_GET["id"] . ";");
				}
				if ($_GET["act"] == "comment") {
					$mysqli->query ("DELETE FROM `fake_comments` WHERE `id` = " . $_GET["id"] . ";");
				}
				if ($_GET["act"] == "edit") {
					if (isset ($_GET["id"])) {
						if (isset ($_POST["list_name"])) {
							$mysqli->query ("UPDATE `list` SET `cat`=" . $_POST["radio2"] .", `name`='" . $_POST["list_name"] . "',`collection`='" . $_POST["list_collection"]. "',`description`='" . $_POST["list_description"] . "',`type`=" . $_POST["list_type"] . ",`rarity`=" . $_POST["list_rarity"] . ",`look`=" . $_POST["list_look"] . ",`picture`='" . $_POST["list_url"] . "',`price`=" . $_POST["list_price"] . ",`recovery` = " . $_POST["list_price"] . " WHERE `id`=" . $_GET["id"] . ";");
						}
					}
				}
				if ($_GET["act"] == "recovery") {
					$get_price = $mysqli->query ("SELECT * FROM `list`;");
					while ($recovery = $get_price->fetch_assoc ()) {
						$mysqli->query ("UPDATE `list` SET `price`=" . $recovery["recovery"] . " WHERE `id`=" . $recovery["id"] . ";");
					}
					header ("Location: kreker.php?mode=settings");
				}
				if ($_GET["act"] == "answers") {
					$mysqli->query ("DELETE FROM `answers` WHERE `id`= " . $_GET["id"] . ";");
					header ("Location: kreker.php?mode=answers");
				}
				if ($_GET["act"] == "change") {
					if (isset ($_GET["before"])) {
						# поднять
						$before = $mysqli->query ("SELECT * FROM `random` WHERE `id`=" . $_GET["before"] . ";");
						$fetch = $before->fetch_assoc ();
						$list_id_before = $fetch["list_id"];
						$mysqli->query ("UPDATE `random` SET `id`=0 WHERE `list_id`=" . $list_id_before . ";");
						$mysqli->query ("UPDATE `random` SET `id`=" . $_GET["before"] . " WHERE `id`=" . $_GET["id"] . ";");
						$mysqli->query ("UPDATE `random` SET `id`=" . $_GET["id"] . " WHERE `list_id`=" . $list_id_before . ";");
					} elseif (isset ($_GET["after"])) {
						# опустить
						$after = $mysqli->query ("SELECT * FROM `random` WHERE `id`=" . $_GET["after"] . ";");
						$fetch = $after->fetch_assoc ();
						$list_id_after = $fetch["list_id"];
						$mysqli->query ("UPDATE `random` SET `id`=0 WHERE `list_id`=" . $list_id_after . ";");
						$mysqli->query ("UPDATE `random` SET `id`=" . $_GET["after"] . " WHERE `id`=" . $_GET["id"] . ";");
						$mysqli->query ("UPDATE `random` SET `id`=" . $_GET["id"] . " WHERE `list_id`=" . $list_id_after . ";");
					}
					header ("Location: kreker.php");
				}
				if ($_GET["act"] == "show") {
					$comment = $mysqli->query ("SELECT * FROM `comments` WHERE `id`=" . $_GET["id"] . ";");
					if ($comment->num_rows == 1) {
						$comment_info = $comment->fetch_assoc ();
						$mysqli->query ("INSERT INTO `fake_comments`(`email`, `text`, `negative`, `date`) VALUES ('" . $comment_info["email"] . "', '" . $comment_info["text"] . "', " . $comment_info["negative"] . ", '" . $comment_info["date"] . "');");
						$mysqli->query ("DELETE FROM `comments` WHERE `id`= " . $_GET["id"] . ";");
						header ("Location: kreker.php");
					}
				}
				if ($_GET["act"] == "hot") {
					$mysqli->query ("UPDATE `list` SET `hot`=1 WHERE `id`=" . $_GET["id"] . ";");
					header ("Location: kreker.php");
				}
				if ($_GET["act"] == "unhot") {
					$mysqli->query ("UPDATE `list` SET `hot`=0 WHERE `id`=" . $_GET["id"] . ";");
					header ("Location: kreker.php");
				}
			}
			if (isset ($_GET["mode"])) {
				if ($_GET["mode"] == "settings") {
					if (isset ($_POST["site_name"])) {
						$this_list = [];
						if ($_POST["randomList"]) {
							$randomList = $_POST["randomList"];
							foreach ($randomList as $selected) {
								$this_list[] = $selected;
							}
						} else {
							$this_list[] = 0;
						}
						$mysqli->query ("UPDATE `site_settings` SET `comments` = " . $_POST["site_comments"] . ", `email` = '" . $_POST["site_email"] . "', `name`= '" . $_POST["site_name"] . "', `description`='" . $_POST["site_description"] . "', `favicon`= '" . $_POST["site_favicon"] . "', `webmoney`='" . $_POST["site_webmoney"] . "', `qiwi`='" . $_POST["site_qiwi"] . "',`yandex`='" . $_POST["site_yandex"] . "', `list_cnt` = " . $_POST["site_cnt"] . ", `always`='" . serialize ($this_list) . "' WHERE `id`=1;");
					}
					if (isset ($_POST["fake_text"])) {
						$bad = 0;
						if (isset ($_POST["siteisbad"])) {
							if ($_POST["siteisbad"] == "on") {
								$bad = 1;
							}
						}
						$get_email = $mysqli->query("SELECT * FROM `mails`;");
						$email = $mysqli->query("SELECT * FROM `mails` ORDER BY rand() LIMIT " . $get_email->num_rows . ";");
						$eresponse = $email->fetch_assoc ();
						$mysqli->query ("INSERT INTO `fake_comments`(`email`, `text`, `negative`,`date`) VALUES ('" . $eresponse["email"] . "', '" . $_POST["fake_text"] . "', " . $bad . ", '" . date ("d.m.y") . "');");
					}
					if (isset ($_POST["list_name"])) {
						$mysqli->query ("INSERT INTO `list`(`name`, `collection`, `description`, `type`, `rarity`, `look`, `picture`, `price`, `recovery`, `cat`) VALUES ('" . $_POST["list_name"] . "', '" . $_POST["list_collection"]. "','" . $_POST["list_description"] . "', " . $_POST["list_type"] . ", " . $_POST["list_rarity"] . ", " . $_POST["list_look"] . ", '" . $_POST["list_url"] . "', " . $_POST["list_price"] . ", " . $_POST["list_price"] . ", " . $_POST["radio2"] . ");");
					}
					$get_settings = $mysqli->query ("SELECT * FROM `site_settings`;");
					$data = $get_settings->fetch_assoc ();
					if (isset ($_POST["price_up"])) {
						$get_price = $mysqli->query ("SELECT * FROM `list`;");
						while ($new_price = $get_price->fetch_assoc ()) {
							$price = ceil ($new_price["recovery"]+($new_price["recovery"]/100*$_POST["price_up"]));
							$mysqli->query ("UPDATE `list` SET `price`=" . $price . " WHERE `id`=" . $new_price["id"] . ";");
						}
					}
				} elseif ($_GET["mode"] == "answers") {
					if (isset ($_POST["add_ans"])) {
						$mysqli->query ("INSERT INTO `answers`(`answer`, `response`) VALUES ('" . $_POST["faq_answer"] . "', '" . $_POST["faq_response"] . "');");
						header ("Location: kreker.php?mode=answers");
					}
				}
			}
			$count_visitors = $mysqli->query ("SELECT * FROM `visitors` WHERE `time`='" . date ("d.m.y") . "';");
			$count_list = $mysqli->query ("SELECT * FROM `list`;");
			include ("site.php");
		}
	} else {
?>
<form action = "" method = "POST">
	<input type = "password" name = "road2auth" required />
	<input type = "submit" value = "LET`S GO!" />
</form>
<?
	}
?>