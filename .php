<?php
	include ("settings.php");
	$res = $mysqli->query ("SELECT * FROM `visitors` WHERE `ip`='" . $ip . "' AND `time`='" . date ("d.m.y") . "';");
	if ($res->num_rows == 0) {
		$mysqli->query ("INSERT INTO `visitors`(`ip`, `time`) VALUES ('" . $ip . "', '" . date ("d.m.y") . "');");
	}
	if (isset ($_POST["email"], $_POST["trade"], $_POST["text"], $_POST["speed"])) {
		if ($_POST["email"] != null AND $_POST["trade"] != null AND $_POST["text"] != null AND $_POST["speed"] != null) {
			$email = trim(htmlspecialchars(stripslashes($_POST["email"])));
			$text = trim(htmlspecialchars(stripslashes($_POST["text"])));
			if ($text != null and $email != null) {
				$mysqli->query ("INSERT INTO `comments`(`ip`, `text`, `email`, `date`, `negative`) VALUES ('" . $ip . "', '" . $text . "', '" . $email . "', '" . date ("d.m.y") . "', " . $_POST["speed"] . ")");
				header ("Location: index.php");
			}
		}
	}
	$purchases = $mysqli->query ("SELECT * FROM `users_purchases` WHERE `ip` = '" . $ip . "';");
	if ($purchases->num_rows == 0) {
		$num = rand (100, 1000000);
		$get_purchase = $mysqli->query ("INSERT INTO `users_purchases`(`id_purch`, `ip`) VALUES (" . $num . ", '" . $ip . "');");
	} else {
		$ndata = $purchases->fetch_assoc ();
		$num = $ndata["id_purch"];
	}
	$get_settings = $mysqli->query ("SELECT * FROM `site_settings`;");
	$data = $get_settings->fetch_assoc ();
	
	$get_list = $mysqli->query ("SELECT * FROM `list` ORDER BY `id` DESC;");
	
	$payer = $mysqli->query ("SELECT * FROM `site_settings`;");
	$pdata = $payer->fetch_assoc ();
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title><?=$data["name"]?></title>
	<link rel="shortcut icon" href="<?=$data["favicon"]?>" />
	<meta name="description" content="<?=$data["description"]?>" />
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
	<script type="text/javascript">
		function change (service, block) {
			jQuery.ajax({
				url:     "pay.php",
				type:     "POST", 
				dataType: "html", 
				data: "sv=" + service + "&get=<?=substr (md5 (str_shuffle (session_id ())), 0, 15)?>", 
				success: function(response) { 
					document.getElementById(block).innerHTML = response;
				}
			});
		}
		function check (block, email, trade <?
											if ($pdata["auto"] == 0) {
												echo ",number,list";
											}
											?>) {
			jQuery.ajax({
				url:     "ajax1.php",
				type:     "POST", 
				dataType: "html", 
				data: "nts=<?=substr (md5 (str_shuffle (session_id ())), 0, 15)?>&email=" + $("#" + email).val() + "&trade="  + $("#" + trade).val()<?
																																					if ($pdata["auto"] == 0) {
																																						echo "+ '&number='+$('#' + number).val() + '&list='+list";
																																					} ?>, 
				success: function(response) { 
					document.getElementById(block).innerHTML = response;
				}
			});
		}
		jQuery(document).ready(function($){
			<?
			if ($get_list->num_rows > 0) {
				for ($y = 0; $y < $get_list->num_rows; $y++) {
					$gdata = $get_list->fetch_assoc ();
			?>
			$('.product<?=$gdata["id"]?>').click(function(e){
                e.preventDefault();
                $('.overlay<?=$gdata["id"]?>').toggleClass('overlay-block');
            });
            $('.product_<?=$gdata["id"]?>').click(function(e){
                e.preventDefault();
                $('.overlay_<?=$gdata["id"]?>').toggleClass('overlay-block');
            });
			<?
				}
			}
			?>
            $('.none').click(function(e){
                e.preventDefault();
                $('.overlay-block').toggleClass('overlay-block');
            });
        });
        $(function() {
            $( "#speed" ).selectmenu();
        });

	</script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?
	$get_list = $mysqli->query ("SELECT * FROM `list` ORDER BY `id` DESC;");
	if ($get_list->num_rows > 0) {
		for ($y = 0; $y < $get_list->num_rows; $y++) {
		$gdata = $get_list->fetch_assoc ();
?>
        <div class="overlay overlay<?=$gdata["id"]?>" title="">
            <div class="none"></div>
            <div class="popup">
                <div class="popup-top-border">
                    <div class="popup-top">
                        <div class="grad<?=$gdata["rarity"]?>"></div>
                        <div class="popup-top-left">
                            <img style="margin-left:20px;margin-top:40px;" src="<?=$gdata["picture"]?>" width="400px" alt="">
                        </div>
                        <div class="popup-top-right">
						<?
								$type = $gdata["type"];
								if ($type == 1) {
									$type_color = "#b2b2b2";
								} elseif ($type == 2) {
									$type_color = "#cf6a32";
								} elseif ($type == 3) {
									$type_color = "#f2d70a";
								} elseif ($type == 4) {
									$type_color = "#8650ac";
								} elseif ($type == 5) {
									$type_color = "#8650ac";
								}
							?>
                            <div class="popup-product-title"><font color = "<?=$type_color?>"><?=$gdata["name"]?></font></div>
                            <div class="popup-product-bottom-title"><font color = "<?=$type_color?>"><?=$types["type"][$gdata["type"]]?></font>, <?=$types["rarity"][$gdata["rarity"]]?></div>
                            <div class="kind">&#1042;&#1085;&#1077;&#1096;&#1085;&#1080;&#1081; &#1074;&#1080;&#1076;: <?=$types["look"][$gdata["look"]]?></div>
							<?
								if ($gdata["type"] == 2 or $gdata["type"] == 5) {
							?>
							 <div class="blue-text">
                                <span>&#1069;&#1090;&#1086;&#1090; &#1087;&#1088;&#1077;&#1076;&#1084;&#1077;&#1090; &#1080;&#1089;&#1087;&#1086;&#1083;&#1100;&#1079;&#1091;&#1077;&#1090; &#1090;&#1077;&#1093;&#1085;&#1086;&#1083;&#1086;&#1075;&#1080;&#1102; StatTrak&#8482;, &#1082;&#1086;&#1090;&#1086;&#1088;&#1072;&#1103; &#1087;&#1086;&#1084;&#1086;&#1075;&#1072;&#1077;&#1090; &#1086;&#1090;&#1089;&#1083;&#1077;&#1078;&#1080;&#1074;&#1072;&#1090;&#1100; &#1086;&#1087;&#1088;&#1077;&#1076;&#1077;&#1083;&#1077;&#1085;&#1085;&#1099;&#1077; &#1087;&#1091;&#1085;&#1082;&#1090;&#1099; &#1089;&#1090;&#1072;&#1090;&#1080;&#1089;&#1090;&#1080;&#1082;&#1080;, &#1074;&#1099;&#1087;&#1086;&#1083;&#1085;&#1077;&#1085;&#1085;&#1099;&#1077; &#1074;&#1083;&#1072;&#1076;&#1077;&#1083;&#1100;&#1094;&#1077;&#1084;.</span>
                            </div>
							<?
								}
							?>
                            <div class="text"><?=$gdata["description"]?>
                            </div> 
                            <div class="colection"><?=$gdata["collection"]?></div>
                            <!--div class="bottom-text">&#1063;&#1072;&#1089;&#1090;&#1100; &#1074;&#1099;&#1088;&#1091;&#1095;&#1082;&#1080; &#1089; &#1087;&#1088;&#1086;&#1076;&#1072;&#1078;&#1080; &#1082;&#1083;&#1102;&#1095;&#1077;&#1081; &#1073;&#1091;&#1076;&#1077;&#1090; &#1085;&#1072;&#1087;&#1088;&#1072;&#1074;&#1083;&#1077;&#1085;&#1072; &#1074; &#1087;&#1088;&#1080;&#1079;&#1086;&#1074;&#1099;&#1077; &#1092;&#1086;&#1085;&#1076;&#1099; &#1087;&#1088;&#1086;&#1092;&#1077;&#1089;&#1089;&#1080;&#1086;&#1085;&#1072;&#1083;&#1100;&#1085;&#1099;&#1093; &#1090;&#1091;&#1088;&#1085;&#1080;&#1088;&#1086;&#1074; &#1087;&#1086; CS:GO.</div-->
                            <a class="popup-price product_<?=$gdata["id"]?>">
                                <div style="margin-left:4px;"><?=$gdata["price"]?> &#1056;</div> 
                            </a>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="gr-title"></div>
                    <div class="gr">
                        <div class="gr-block">
                            <img src="images/gr-icon1.png" alt="">
                            <div class="gr-block-title">&#1053;&#1072;&#1076;&#1077;&#1078;&#1085;&#1086;&#1089;&#1090;&#1100;</div>
                            <div class="gr-block-text">&#1048;&#1076;&#1077;&#1085;&#1090;&#1080;&#1092;&#1080;&#1094;&#1080;&#1088;&#1086;&#1074;&#1072;&#1085;&#1085;&#1099;&#1081; &#1089;&#1095;&#1077;&#1090; &#1071;&#1085;&#1076;&#1077;&#1082;&#1089;.&#1044;&#1077;&#1085;&#1100;&#1075;&#1080;
    &#1055;&#1086;&#1083;&#1085;&#1086;&#1089;&#1090;&#1100;&#1102; &#1080;&#1076;&#1077;&#1085;&#1090;&#1080;&#1092;&#1080;&#1094;&#1080;&#1088;&#1086;&#1074;&#1072;&#1085;&#1085;&#1099;&#1081; &#1089;&#1095;&#1077;&#1090; &#1074; Qiwi
    &#1055;&#1077;&#1088;&#1089;&#1086;&#1085;&#1072;&#1083;&#1100;&#1085;&#1099;&#1081; &#1072;&#1090;&#1090;&#1077;&#1089;&#1090;&#1072;&#1090; Webmoney</div>
                        </div>
                        <div class="gr-block">
                            <img src="images/gr-icon2.png" alt="">
                            <div class="gr-block-title">&#1054;&#1055;&#1045;&#1056;&#1040;&#1058;&#1048;&#1042;&#1053;&#1054;&#1057;&#1058;&#1068;</div>
                            <div class="gr-block-text">&#1042;&#1089;&#1077; &#1087;&#1088;&#1086;&#1080;&#1089;&#1093;&#1086;&#1076;&#1080;&#1090; &#1074; &#1072;&#1074;&#1090;&#1086;&#1084;&#1072;&#1090;&#1080;&#1095;&#1077;&#1089;&#1082;&#1086;&#1084; &#1088;&#1077;&#1078;&#1080;&#1084;&#1077;, &#1073;&#1091;&#1082;&#1074;&#1072;&#1083;&#1100;&#1085;&#1086; &#1095;&#1077;&#1088;&#1077;&#1079; &#1087;&#1072;&#1088;&#1091; &#1084;&#1080;&#1085;&#1091;&#1090; &#1042;&#1099; &#1087;&#1086;&#1083;&#1091;&#1095;&#1080;&#1090;&#1077;<br> &#1089;&#1074;&#1086;&#1081; &#1090;&#1086;&#1074;&#1072;&#1088;!</div>
                        </div>
                        <div class="gr-block">
                            <img src="images/gr-icon3.png" alt="">
                            <div class="gr-block-title">&#1041;&#1086;&#1083;&#1077;&#1077; 8000 &#1087;&#1086;&#1082;&#1091;&#1087;&#1086;&#1082;</div>
                            <div class="gr-block-text">&#1045;&#1078;&#1077;&#1076;&#1085;&#1077;&#1074;&#1085;&#1086; &#1085;&#1072; &#1089;&#1072;&#1081;&#1090;&#1077; &#1089;&#1086;&#1074;&#1077;&#1088;&#1096;&#1072;&#1077;&#1090;&#1089;&#1103; &#1073;&#1086;&#1083;&#1077;&#1077; 100 &#1087;&#1086;&#1082;&#1091;&#1087;&#1086;&#1082; &#1080; &#1073;&#1086;&#1083;&#1077;&#1077; 3000 &#1074; &#1084;&#1077;&#1089;&#1103;&#1094;!</div>
                        </div>
                    </div>
                    <div class="faq">
                        <div class="faq-title"></div>
                        <div class="faq-block">
                            <span>&#1043;&#1076;&#1077; &#1074;&#1079;&#1103;&#1090;&#1100; &#1089;&#1089;&#1099;&#1083;&#1082;&#1091; &#1085;&#1072; &#1090;&#1088;&#1077;&#1081;&#1076;?</span>
                            &#1040;&#1074;&#1090;&#1086;&#1088;&#1080;&#1079;&#1091;&#1081;&#1090;&#1077;&#1089;&#1100; &#1085;&#1072; &#1089;&#1072;&#1081;&#1090;&#1077; &#1089;&#1090;&#1080;&#1084;&#1072; &#1080; &#1087;&#1077;&#1088;&#1077;&#1081;&#1076;&#1080;&#1090;&#1077; &#1087;&#1086; &#1089;&#1089;&#1099;&#1083;&#1082;&#1077; <a href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url</a>
                        </div>
                        <div class="faq-block">
                            <span>&#1071; &#1079;&#1072;&#1073;&#1099;&#1083; &#1091;&#1082;&#1072;&#1079;&#1072;&#1090;&#1100; &#1087;&#1088;&#1080;&#1084;&#1077;&#1095;&#1072;&#1085;&#1080;&#1077; &#1082; &#1086;&#1087;&#1083;&#1072;&#1090;&#1077;, &#1095;&#1090;&#1086; &#1076;&#1077;&#1083;&#1072;&#1090;&#1100;?</span>
                            &#1057;&#1086;&#1086;&#1073;&#1097;&#1080;&#1090;&#1077; &#1085;&#1072; &#1087;&#1086;&#1095;&#1090;&#1091; <a href="mailto:support@easy-money.ru">support@easy-money.ru</a> &#1089; &#1082;&#1072;&#1082;&#1086;&#1075;&#1086; &#1082;&#1086;&#1096;&#1077;&#1083;&#1100;&#1082;&#1072; &#1086;&#1087;&#1083;&#1072;&#1095;&#1080;&#1074;&#1072;&#1083;&#1080;, &#1076;&#1072;&#1090;&#1091; &#1087;&#1077;&#1088;&#1077;&#1074;&#1086;&#1076;&#1072;, &#1082;&#1072;&#1082;&#1086;&#1081; &#1090;&#1086;&#1074;&#1072;&#1088; &#1093;&#1086;&#1090;&#1077;&#1083;&#1080; &#1082;&#1091;&#1087;&#1080;&#1090;&#1100; &#1080; &#1089;&#1089;&#1099;&#1083;&#1082;&#1091; &#1085;&#1072; &#1090;&#1088;&#1077;&#1081;&#1076; &#1080; &#1084;&#1099; &#1074;&#1088;&#1091;&#1095;&#1085;&#1091;&#1102; &#1086;&#1090;&#1087;&#1088;&#1072;&#1074;&#1080;&#1084; &#1042;&#1072;&#1084; &#1090;&#1086;&#1074;&#1072;&#1088;.
                        </div>
                        <div class="faq-block">
                            <span>&#1071; &#1089;&#1083;&#1091;&#1095;&#1072;&#1081;&#1085;&#1086; &#1086;&#1090;&#1082;&#1083;&#1086;&#1085;&#1080;&#1083; &#1090;&#1088;&#1077;&#1081;&#1076; &#1086;&#1090; &#1073;&#1086;&#1090;&#1072;, &#1095;&#1090;&#1086; &#1076;&#1077;&#1083;&#1072;&#1090;&#1100;?</span>
                            &#1057;&#1086;&#1086;&#1073;&#1097;&#1080;&#1090;&#1077; &#1085;&#1072; &#1087;&#1086;&#1095;&#1090;&#1091; <a href="mailto:support@easy-money.ru">support@easy-money.ru</a> &#1085;&#1086;&#1084;&#1077;&#1088; &#1079;&#1072;&#1082;&#1072;&#1079;&#1072; &#1080; &#1084;&#1099; &#1087;&#1086;&#1074;&#1090;&#1086;&#1088;&#1085;&#1086; &#1086;&#1090;&#1087;&#1088;&#1072;&#1074;&#1080;&#1084; &#1077;&#1075;&#1086; &#1042;&#1072;&#1084;.
                        </div>
                        <div class="faq-block">
                            <span>&#1059; &#1084;&#1077;&#1085;&#1103; &#1086;&#1089;&#1090;&#1072;&#1083;&#1080;&#1089;&#1100; &#1074;&#1086;&#1087;&#1088;&#1086;&#1089;&#1099;, &#1082;&#1072;&#1082; &#1089;&#1074;&#1072;&#1084;&#1080; &#1089;&#1074;&#1103;&#1079;&#1072;&#1090;&#1100;&#1089;&#1103;?</span>
                            &#1055;&#1080;&#1096;&#1080;&#1090;&#1077; &#1085;&#1072; &#1085;&#1072;&#1096;&#1091; &#1087;&#1086;&#1095;&#1090;&#1091; <a href="mailto:support@easy-money.ru">support@easy-money.ru</a> &#1088;&#1072;&#1073;&#1086;&#1090;&#1072;&#1077;&#1084; &#1073;&#1077;&#1079; &#1074;&#1099;&#1093;&#1086;&#1076;&#1085;&#1099;&#1093; &#1089; 9:00 &#1076;&#1086; 22:00 &#1087;&#1086; &#1052;&#1086;&#1089;&#1082;&#1074;&#1077;.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay overlay_<?=$gdata["id"]?>" title="">
            <div class="none"></div>
            <div class="popup">
                <div class="popup-top-border">
                    <div class="popup-top">
                        <div class="grad<?=$gdata["rarity"]?>"></div>
                        <div class="popup-top-left">
                            <img style="margin-left:20px;margin-top:40px;" src="<?=$gdata["picture"]?>" width="400px" alt="">
                        </div>
                        <div class="popup-top-right">
                            <div class="pay">
                                <div class="pay-block">
                                    <div class="pay-left">&#1042;&#1072;&#1096; E-mail:</div>
                                    <input type = "email" id = "email_pay_<?=$gdata["id"]?>" name = "email_pay" placeholder = "easy-weapons@yandex.ru" class="pay-right" />
                                </div>
                                <div class="pay-block">
                                    <div class="pay-left">&#1057;&#1089;&#1099;&#1083;&#1082;&#1072; &#1085;&#1072; &#1090;&#1088;&#1077;&#1081;&#1076;:</div>
                                   <input type = "url" id = "trade_pay_<?=$gdata["id"]?>" name = "trade_pay" placeholder = "https://steamcommunity.com/tradeoffer/new/?partner=527682721&token=eSBqePac" class="pay-right" />
                                </div>
								<?
								if ($pdata["auto"] == 0) {
									?>
									<div class="pay-block">
                                    <div class="pay-left">Номер телефона:</div>
                                    <input type = "text" id = "number_<?=$gdata["id"]?>" name = "number_pay" placeholder = "79999999999" class="pay-right" />
                                </div>
									<?
								}
								?>
								
                                <div class="pay-block pay-block2">
                                    <div class="pay-left">&#1057;&#1087;&#1086;&#1089;&#1086;&#1073; &#1086;&#1087;&#1083;&#1072;&#1090;&#1099;:</div>
                                    <div class="pay-right">
                                        <a onclick = "change('qiwi', 'forpay_<?=$gdata["id"]?>')"><img src="images/qiwi.png" alt=""></a>
										<?
										if ($pdata["auto"] == 1) {
										?>
										<a onclick = "change('yandex', 'forpay_<?=$gdata["id"]?>')"><img src="images/yandex.png" alt=""></a>
                                        <a onclick = "change('webmoney', 'forpay_<?=$gdata["id"]?>')"><img src="images/web.png" alt=""></a>
										<?								
										}
										?>
                                       
                                    </div>
                                </div>
                                <div class="pay-block">
                                    <div class="pay-left"></div>
                                    <div class="pay-right">
                                        <div class="pay-right-descriptuion">
                                            <div><span>&#1058;&#1086;&#1074;&#1072;&#1088;:</span>				<?=$gdata["name"]?></div>
											<div><span>К оплате:</span> <?=$gdata["price"]?> рублей</div> 
                                            <?
												if ($pdata["auto"] == 1) {
													?>
														<div><span>Кошелек для оплаты:</span> <span id = "forpay_<?=$gdata["id"]?>"><?=$pdata["qiwi"]?></span></div> 
														<div><span>Примечание:</span> Заказ №<?=$num?></div> </div>
														<div id = "result_<?=$gdata["id"]?>" class="warning">Внимание! Очень важно чтобы вы переводили деньги с этим примечанием, иначе средства не будут зачислены автоматически.</div>													<?
												} else {
													?>
												</div>
												<div id = "result_<?=$gdata["id"]?>" class="warning">Вам будет выставлен счёт в системе QIWI на указанный номер.</div><br />
													
													<?
												}
											?>
										 <a onclick = "check('result_<?=$gdata["id"]?>', 'email_pay_<?=$gdata["id"]?>', 'trade_pay_<?=$gdata["id"]?>'<?
																																						if ($pdata["auto"] == 0) { 
																																								echo ", 'number_" . $gdata["id"] . "', " . $gdata["id"] . "";
																																						}
																																						?>)" class="green">&#1087;&#1088;&#1086;&#1074;&#1077;&#1088;&#1080;&#1090;&#1100; &#1086;&#1087;&#1083;&#1072;&#1090;&#1091;</a>
                                    </div>
                                    <div class="clear"></div>
									<p>В течении 15 минут (обычно моментально) после оплаты наш бот отправит вам трейд. Его нужно будет принять в течении 120 минут иначе он отменится автоматически.</p>                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
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
                            <div class="gr-block-title">&#1054;&#1055;&#1045;&#1056;&#1040;&#1058;&#1048;&#1042;&#1053;&#1054;&#1057;&#1058;&#1068;</div>
                            <div class="gr-block-text">&#1042;&#1089;&#1077; &#1087;&#1088;&#1086;&#1080;&#1089;&#1093;&#1086;&#1076;&#1080;&#1090; &#1074; &#1072;&#1074;&#1090;&#1086;&#1084;&#1072;&#1090;&#1080;&#1095;&#1077;&#1089;&#1082;&#1086;&#1084; &#1088;&#1077;&#1078;&#1080;&#1084;&#1077;, &#1073;&#1091;&#1082;&#1074;&#1072;&#1083;&#1100;&#1085;&#1086; &#1095;&#1077;&#1088;&#1077;&#1079; &#1087;&#1072;&#1088;&#1091; &#1084;&#1080;&#1085;&#1091;&#1090; &#1042;&#1099; &#1087;&#1086;&#1083;&#1091;&#1095;&#1080;&#1090;&#1077;<br> &#1089;&#1074;&#1086;&#1081; &#1090;&#1086;&#1074;&#1072;&#1088;!</div>
                        </div>
                        <div class="gr-block">
                            <img src="images/gr-icon3.png" alt="">
                            <div class="gr-block-title">&#1041;&#1086;&#1083;&#1077;&#1077; 8000 &#1087;&#1086;&#1082;&#1091;&#1087;&#1086;&#1082;</div>
                            <div class="gr-block-text">&#1045;&#1078;&#1077;&#1076;&#1085;&#1077;&#1074;&#1085;&#1086; &#1085;&#1072; &#1089;&#1072;&#1081;&#1090;&#1077; &#1089;&#1086;&#1074;&#1077;&#1088;&#1096;&#1072;&#1077;&#1090;&#1089;&#1103; &#1073;&#1086;&#1083;&#1077;&#1077; 100 &#1087;&#1086;&#1082;&#1091;&#1087;&#1086;&#1082; &#1080; &#1073;&#1086;&#1083;&#1077;&#1077; 3000 &#1074; &#1084;&#1077;&#1089;&#1103;&#1094;!</div>
                        </div>
                    </div>
                    <div class="faq">
                        <div class="faq-title"></div>
                        <div class="faq-block">
                            <span>&#1043;&#1076;&#1077; &#1074;&#1079;&#1103;&#1090;&#1100; &#1089;&#1089;&#1099;&#1083;&#1082;&#1091; &#1085;&#1072; &#1090;&#1088;&#1077;&#1081;&#1076;?</span>
                            &#1040;&#1074;&#1090;&#1086;&#1088;&#1080;&#1079;&#1091;&#1081;&#1090;&#1077;&#1089;&#1100; &#1085;&#1072; &#1089;&#1072;&#1081;&#1090;&#1077; &#1089;&#1090;&#1080;&#1084;&#1072; &#1080; &#1087;&#1077;&#1088;&#1077;&#1081;&#1076;&#1080;&#1090;&#1077; &#1087;&#1086; &#1089;&#1089;&#1099;&#1083;&#1082;&#1077; <a href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url</a>
                        </div>
                        <div class="faq-block">
                            <span>&#1071; &#1079;&#1072;&#1073;&#1099;&#1083; &#1091;&#1082;&#1072;&#1079;&#1072;&#1090;&#1100; &#1087;&#1088;&#1080;&#1084;&#1077;&#1095;&#1072;&#1085;&#1080;&#1077; &#1082; &#1086;&#1087;&#1083;&#1072;&#1090;&#1077;, &#1095;&#1090;&#1086; &#1076;&#1077;&#1083;&#1072;&#1090;&#1100;?</span>
                            &#1057;&#1086;&#1086;&#1073;&#1097;&#1080;&#1090;&#1077; &#1085;&#1072; &#1087;&#1086;&#1095;&#1090;&#1091; <a href="mailto:support@easy-money.ru">support@easy-money.ru</a> &#1089; &#1082;&#1072;&#1082;&#1086;&#1075;&#1086; &#1082;&#1086;&#1096;&#1077;&#1083;&#1100;&#1082;&#1072; &#1086;&#1087;&#1083;&#1072;&#1095;&#1080;&#1074;&#1072;&#1083;&#1080;, &#1076;&#1072;&#1090;&#1091; &#1087;&#1077;&#1088;&#1077;&#1074;&#1086;&#1076;&#1072;, &#1082;&#1072;&#1082;&#1086;&#1081; &#1090;&#1086;&#1074;&#1072;&#1088; &#1093;&#1086;&#1090;&#1077;&#1083;&#1080; &#1082;&#1091;&#1087;&#1080;&#1090;&#1100; &#1080; &#1089;&#1089;&#1099;&#1083;&#1082;&#1091; &#1085;&#1072; &#1090;&#1088;&#1077;&#1081;&#1076; &#1080; &#1084;&#1099; &#1074;&#1088;&#1091;&#1095;&#1085;&#1091;&#1102; &#1086;&#1090;&#1087;&#1088;&#1072;&#1074;&#1080;&#1084; &#1042;&#1072;&#1084; &#1090;&#1086;&#1074;&#1072;&#1088;.
                        </div>
                        <div class="faq-block">
                            <span>&#1071; &#1089;&#1083;&#1091;&#1095;&#1072;&#1081;&#1085;&#1086; &#1086;&#1090;&#1082;&#1083;&#1086;&#1085;&#1080;&#1083; &#1090;&#1088;&#1077;&#1081;&#1076; &#1086;&#1090; &#1073;&#1086;&#1090;&#1072;, &#1095;&#1090;&#1086; &#1076;&#1077;&#1083;&#1072;&#1090;&#1100;?</span>
                            &#1057;&#1086;&#1086;&#1073;&#1097;&#1080;&#1090;&#1077; &#1085;&#1072; &#1087;&#1086;&#1095;&#1090;&#1091; <a href="mailto:support@easy-money.ru">support@easy-money.ru</a> &#1085;&#1086;&#1084;&#1077;&#1088; &#1079;&#1072;&#1082;&#1072;&#1079;&#1072; &#1080; &#1084;&#1099; &#1087;&#1086;&#1074;&#1090;&#1086;&#1088;&#1085;&#1086; &#1086;&#1090;&#1087;&#1088;&#1072;&#1074;&#1080;&#1084; &#1077;&#1075;&#1086; &#1042;&#1072;&#1084;.
                        </div>
                        <div class="faq-block">
                            <span>&#1059; &#1084;&#1077;&#1085;&#1103; &#1086;&#1089;&#1090;&#1072;&#1083;&#1080;&#1089;&#1100; &#1074;&#1086;&#1087;&#1088;&#1086;&#1089;&#1099;, &#1082;&#1072;&#1082; &#1089;&#1074;&#1072;&#1084;&#1080; &#1089;&#1074;&#1103;&#1079;&#1072;&#1090;&#1100;&#1089;&#1103;?</span>
                            &#1055;&#1080;&#1096;&#1080;&#1090;&#1077; &#1085;&#1072; &#1085;&#1072;&#1096;&#1091; &#1087;&#1086;&#1095;&#1090;&#1091; <a href="mailto:support@easy-money.ru">support@easy-money.ru</a> &#1088;&#1072;&#1073;&#1086;&#1090;&#1072;&#1077;&#1084; &#1073;&#1077;&#1079; &#1074;&#1099;&#1093;&#1086;&#1076;&#1085;&#1099;&#1093; &#1089; 9:00 &#1076;&#1086; 22:00 &#1087;&#1086; &#1052;&#1086;&#1089;&#1082;&#1074;&#1077;.
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?
		}
	}
?>
        <div class="main">
            <div class="header">
                <div class="header-block">
                    <div class="aka"></div>
                    <div class="fleshka"></div>
                    <div class="smoke"></div>
                    <div class="molotov"></div>
                    <div class="line-title-block"> 
                        <div class="line-title">&#1058;&#1086;&#1083;&#1100;&#1082;&#1086; &#1095;&#1090;&#1086; &#1082;&#1091;&#1087;&#1080;&#1083;&#1080;</div>
                    </div>   
                    <div class="clear"></div>
                </div>
            </div>
            <div class="line">
                <div id = "lenta" class="line-block"> </div>
            </div>
		  <div class="content">
			<?
				$get_list = $mysqli->query ("SELECT * FROM `random`;");
				if ($get_list->num_rows > 0) {
					while ($list_info = $get_list->fetch_assoc ()) {
						$get_list_info = $mysqli->query ("SELECT * FROM `list` WHERE `id`=" . $list_info["list_id"] . ";");
						$data = $get_list_info->fetch_assoc ();
			?>
						<div class="product product<?=$data["id"]?>">
							<div class="img-rarity<?=$data["rarity"]?>">
								<img style="margin-left:20px;" src="<?=$data["picture"]?>" width="240" alt="">
							</div>
							<?
								$data_name = explode ("|", $data["name"]);
							?>
							<div class="title-product"><?=$data_name[0] . "<br />" . $data_name[1]?></div>
							<div class="price-product"><?=$data["price"]?> &#1056;</div>
						</div>
			<?
					}
				}
			?>
			</div>
            <div class="review">
<div class="review-title-vid"></div><br>
<center><iframe src="//vk.com/video_ext.php?oid=-37010444&id=456239017&hash=a4ffd12c28cefadc&hd=2" width="1200" height="675"  frameborder="0"></iframe></center><br>
<div class="review-title-vk"></div><br>
<center><script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>

<!-- VK Widget -->
<div id="vk_groups"></div>
<script type="text/javascript">
VK.Widgets.Group("vk_groups", {mode: 0, width: "1200", height: "400", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 37010444);
</script></center><br>
                <div class="review-title"></div>
                <center><a href="https://vk.com/topic-37010444_30004740"><img src="/images/otziv_vk.png" alt=""></a></center>
				<?
					$query = ["SELECT * FROM `comments` WHERE `ip` = '" . $ip . "' ORDER BY `id` DESC", "SELECT * FROM `fake_comments` ORDER BY `id` DESC"];
					for ($q = 0; $q < 2; $q++) {
						$comments = $mysqli->query ($query[$q]);
						if ($comments->num_rows > 0) {
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
							}
						}
					}
				?>
                <form action = "" method = "POST" class="add-review">
                    <div class="add-review-top">
                        <div class="add-review-top-block" style="width: 310px;">
                            &#1042;&#1072;&#1096; E-mail:
                            <input type="email" autocomplete = "off" name = "email" placeholder="easy-weapons@yandex.ru"  style="width: 210px;" required>
                        </div>
                        <div class="add-review-top-block" style="width: 820px;">
                            &#1057;&#1089;&#1099;&#1083;&#1082;&#1072; &#1085;&#1072; &#1090;&#1088;&#1077;&#1081;&#1076;:
                            <input type="url" autocomplete = "off" name = "trade" placeholder="https://steamcommunity.com/tradeoffer/new/?partner=527682721&token=eSBqePac" style="width: 685px;" required>
                        </div>
                    </div>
                    <textarea name = "text" placeholder="&#1054;&#1089;&#1090;&#1072;&#1074;&#1080;&#1090;&#1100; &#1086;&#1090;&#1079;&#1099;&#1074; &#1086; &#1089;&#1072;&#1081;&#1090;&#1077;"></textarea>
                    <div class="type">
                        <span>&#1058;&#1080;&#1087; &#1086;&#1090;&#1079;&#1099;&#1074;&#1072;:</span>
                        <select name="speed" id="speed">
							<option value = "1">&#1054;&#1090;&#1088;&#1080;&#1094;&#1072;&#1090;&#1077;&#1083;&#1100;&#1085;&#1099;&#1081;</option>
							<option value = "2">&#1055;&#1086;&#1083;&#1086;&#1078;&#1080;&#1090;&#1077;&#1083;&#1100;&#1085;&#1099;&#1081;</option>
                        </select>
                        <button type = "submit">&#1054;&#1089;&#1090;&#1072;&#1074;&#1080;&#1090;&#1100; &#1086;&#1090;&#1079;&#1099;&#1074;</button>
                    </div>
                </form>
            </div>
            <div class="bottom-site">
<div id="counter"><!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='//www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t25.6;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: &#1087;&#1086;&#1082;&#1072;&#1079;&#1072;&#1085;&#1086; &#1095;&#1080;&#1089;&#1083;&#1086; &#1087;&#1086;&#1089;&#1077;&#1090;&#1080;&#1090;&#1077;&#1083;&#1077;&#1081; &#1079;&#1072;"+
" &#1089;&#1077;&#1075;&#1086;&#1076;&#1085;&#1103;' "+
"border='0' width='88' height='15'><\/a>")
//--></script><!--/LiveInternet--></div>
</div>
        </div>
		<script>
			setInterval(function() {
				$("#lenta").load("lenta.ajax.php");
			}, 1000);
		</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-70103395-2', 'auto');
  ga('send', 'pageview');

</script>

<!— BEGIN JIVOSITE CODE {literal} —> 
<script type='text/javascript'> 
(function(){ var widget_id = '9B20CnUoLY'; 
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script> 
<!— {/literal} END JIVOSITE CODE —>
</body>
