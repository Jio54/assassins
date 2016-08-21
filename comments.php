<?php
	$site = "http://" . $_SERVER["HTTP_HOST"] . "/";
	
	if ($_SERVER["HTTP_REFERER"] == $site . "index.php" or $_SERVER["HTTP_REFERER"] == $site) {
		if (isset ($_POST["page"])) {
			$page = trim (htmlspecialchars (stripslashes ($_POST["page"])));
			if (is_numeric ($page)) {
				if ($page > 0) {
					include ("settings.php");
					$res = $mysqli->query ("SELECT * FROM `site_settings`;");
					$settings = $res->fetch_assoc ();
					if ($page == 1) {
						$list = 0;
					} else {
						$list = $page*$settings["comments"];
					}
					$all = 0;
					$all_ct = 0;
					if ($page == 1) {
						$query = ["SELECT * FROM `comments` WHERE `ip` = '" . $ip . "' ORDER BY `id` DESC;", "SELECT * FROM `fake_comments` ORDER BY `id` DESC LIMIT " . $list . ", " . $settings["comments"] . ";"];
					} else {
						$query = ["SELECT * FROM `fake_comments` ORDER BY `id` DESC LIMIT " . $list . ", " . $settings["comments"] . ";"];
					}
					for ($q = 0; $q < count ($query); $q++) {
						$comments = $mysqli->query ($query[$q]);
						if ($comments->num_rows > 0) {
							$all += $comments->num_rows;
							for ($i = 0; $i < $comments->num_rows; $i++) {
								$cdata = $comments->fetch_assoc ();
?>
								<div class="like <?=($cdata["negative"] == 1) ? "like-no" : false?>">
									<div class="ava">
										<img src="images/ava.png" alt="">
									</div>
									<div class="review-text">
<?
										$ctext = explode ("@", $cdata["email"]);
										$nums = ceil (strlen ($ctext[0])/2);
										$mail = substr ($ctext[0], 0, $nums);
										for ($x = 0; $x < $nums; $x++) {
											$mail .= "*";
										}
?>
										<div><?=$mail . "@" . $ctext[1]?></div>
										<div><?=$cdata["text"]?></div>
									</div>
								</div>
<?								
								$all_ct++;
								if ($all_ct == $settings["comments"]) {
									break 2;
								}
							}
						}
					}
				}
			}
		}
	}
?>