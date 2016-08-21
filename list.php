<?php
	$site = "http://" . $_SERVER["HTTP_HOST"] . "/";
	if ($_SERVER["HTTP_REFERER"] == $site . "index_test.php" or $_SERVER["HTTP_REFERER"] == $site) {
		if (isset ($_POST["list"])) {
			$page = trim (htmlspecialchars (stripslashes ($_POST["list"])));
			if (is_numeric ($page)) {
				include ("settings.php");
				if ($page == 1 or $page == 2) {
					$get_list = $mysqli->query ("SELECT * FROM `list` WHERE `cat` = " . $page . ";");
					if ($get_list->num_rows > 0) {
						while ($list_info = $get_list->fetch_assoc ()) {
				?>
							<div class="product product<?=$list_info["id"]?>">
								<div class="img-rarity<?=$list_info["rarity"]?>">
									<img style="margin-left:20px;" src="<?=$list_info["picture"]?>" width="240" alt="">
								</div>
								<?
									$data_name = explode ("|", $list_info["name"]);
								?>
								<div class="title-product"><?=$data_name[0] . "<br />" . $data_name[1]?></div>
								<div class="price-product"><?=$list_info["price"]?> &#1056;</div>
							</div>
				<?
						}
					}
				} else {
					if ($page == 3) {
						?>
						<div class="gr-title"></div>
                    <div class="gr">
                        <div class="gr-block">
                            <img src="images/gr-icon1.png" alt="">
                            <div class="gr-block-title">Надежность</div>
                            <div class="gr-block-text">Идентифицированный счет Яндекс.Деньги
    Полностью идентифицированный счет в Qiwi
    Персональный аттестат Webmoney</div>
                        </div>
                        <div class="gr-block">
                            <img src="images/gr-icon2.png" alt="">
                            <div class="gr-block-title">ОПЕРАТИВНОСТЬ</div>
                            <div class="gr-block-text">Все происходит в автоматическом режиме, буквально через пару минут Вы получите<br> свой товар!</div>
                        </div>
                        <div class="gr-block">
                            <img src="images/gr-icon3.png" alt="">
                            <div class="gr-block-title">Более 8000 покупок</div>
                            <div class="gr-block-text">Ежедневно на сайте совершается более 100 покупок и более 3000 в месяц!</div>
                        </div>
                    </div>
						<?
					} elseif ($page == 4) {
						?>
						<div class="faq">
							<div class="faq-title"></div>
							<?
								$answers = $mysqli->query ("SELECT * FROM `answers`");
								if ($answers->num_rows > 0) {
									while ($req = $answers->fetch_assoc ()) {
										?>
										 <div class="faq-block">
											<span><?=$req["answer"]?></span>
											<?=$req["response"]?>
										</div>
										<?
									}
								}
							?>
						</div>
						<?
					}
				}
			}
		}
	}
?>