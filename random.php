<?php
	include ("settings.php");
	$mysqli->query ("TRUNCATE TABLE `random`;");
	$settings = $mysqli->query ("SELECT * FROM `site_settings`");
	$data = $settings->fetch_assoc ();
	$always = unserialize ($data["always"]);
	$list = "";
	for ($k = 0; $k < count ($always); $k++) {
		$mysqli->query ("INSERT INTO `random`(`list_id`) VALUES (" . $always[$k] . ");");
		if ($k < count ($always)-1) {
			$list .= "`id` != " . $always[$k] . " AND ";
		} elseif ($k == count ($always)-1) {
			$list .= "`id` != " . $always[$k];
		}
	}
	$res = $mysqli->query ("SELECT * FROM `list` WHERE " . $list . " AND `cat` = 1 ORDER BY RAND() LIMIT " . $data["list_cnt"] . ";");
	while ($row = $res->fetch_assoc ()) {
		$mysqli->query ("INSERT INTO `random`(`list_id`) VALUES (" . $row["id"] . ");");
	}
	$mysqli->query ("TRUNCATE TABLE `purchases`");
?>