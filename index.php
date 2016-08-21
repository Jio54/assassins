<?php
	include ("settings.php");
	$res = $mysqli->query ("SELECT * FROM `visitors` WHERE `ip`='" . $ip . "' AND `time`='" . date ("d.m.y") . "';");
	if ($res->num_rows == 0) {
		$mysqli->query ("INSERT INTO `visitors`(`ip`, `time`) VALUES ('" . $ip . "', '" . date ("d.m.y") . "');");
	}
	if (isset ($_POST["email"], $_POST["trade"], $_POST["text"], $_POST["speed"])) {
		if ($_POST["email"] != null AND $_POST["trade"] != null AND $_POST["text"] != null AND $_POST["speed"] != null) {
			$email = trim (htmlspecialchars (stripslashes ($_POST["email"])));
			$text = trim (htmlspecialchars (stripslashes ($_POST["text"])));
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
	<style>
		img { 
			pointer-events: none; 
		}
	</style>
	<script type="text/javascript">
		function comments (page) {
			jQuery.ajax({
				url:     "comments.php",
				type:     "POST", 
				dataType: "html", 
				data: "page=" + page,
				success: function(response) { 
					document.getElementById("pcomments").innerHTML = response;
				}
			});
		} 
		function user (id, id2) {
			document.getElementById(id).style.display = 'block';
			document.getElementById(id2).style.display = 'none';
		}
		var openDialog = function(trade, number, price) {
			var link = $("#" + trade).val();
			if (link == "") {
				alert ("Не указана ссылка на трейд!");
			} else {
				var top = encodeURIComponent(link);
				var win = window.open("https://w.qiwi.com/payment/form.action?extra['account']=<?=$pdata["qiwi"]?>&amountInteger=" + price + "&amountFraction=0&extra['comment']=Заказ №" + number + " " + top + "&provider=99");
			}
			
		}
		function qiwipay (result, number, list, email, trade, button) {
			var top = encodeURIComponent($("#" + trade).val());
			jQuery.ajax({
				url:     "qiwi.php",
				type:     "POST", 
				dataType: "html", 
				data: "nts=<?=substr (md5 (str_shuffle (session_id ())), 0, 15)?>&number=" + $("#" + number).val() + "&list="  + list + "&email=" + $("#" + email).val() + "&trade="  + top,
				success: function(response) { 
					if (response == "Ожидайте выставления счёта.") {
						document.getElementById(button).disabled = true; 
					}
					document.getElementById(result).innerHTML = response;
				}
			});
		} 
		function change (service, block, info, ans) {
			if (service == "qiwi") {
				document.getElementById(info).style.display = 'none';
				document.getElementById(ans).style.display = 'block';
			} else {
				document.getElementById(info).style.display = 'block';
				document.getElementById(ans).style.display = 'none';
			}
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
		function check (block, email, trade) {
			jQuery.ajax({
				url:     "ajax.php",
				type:     "POST", 
				dataType: "html", 
				data: "nts=<?=substr (md5 (str_shuffle (session_id ())), 0, 15)?>&email=" + $("#" + email).val() + "&trade="  + $("#" + trade).val(), 
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
		function show (type) {
			if (type == 1) {
				document.getElementById("showweapon").style.display = 'block';
				document.getElementById("showcase").style.display = 'none';
				document.getElementById("showfaq").style.display = 'none';
				document.getElementById("showgarant").style.display = 'none';
			} else if (type == 2) {
				document.getElementById("showweapon").style.display = 'none';
				document.getElementById("showcase").style.display = 'block';
				document.getElementById("showfaq").style.display = 'none';
				document.getElementById("showgarant").style.display = 'none';
			} else if (type == 3) {
				document.getElementById("showweapon").style.display = 'none';
				document.getElementById("showcase").style.display = 'none';
				document.getElementById("showfaq").style.display = 'block';
				document.getElementById("showgarant").style.display = 'none';
			} else {
				document.getElementById("showweapon").style.display = 'none';
				document.getElementById("showcase").style.display = 'none';
				document.getElementById("showfaq").style.display = 'none';
				document.getElementById("showgarant").style.display = 'block';
			}
		}
	</script>
    <link rel="stylesheet" href="css/style.css">
</head>
<div class="content">
<ul class="header-menu">
<li class="header-menu__item"><a target="_blank" onclick = "show ('1')" class="header-menu__link"><span class="header-icon weapons-icon"></span>&#1054;&#1088;&#1091;&#1078;&#1080;&#1077;</a></li>
<li class="header-menu__item"><a target="_blank" onclick = "show ('2')" class="header-menu__link"><span class="header-icon case-icon"></span>&#1050;&#1077;&#1081;&#1089;&#1099;</a></li>
<li class="header-menu__item"><a class="header-menu__link" onclick = "show ('4')"><span class="header-icon shield-icon"></span>&#1043;&#1072;&#1088;&#1072;&#1085;&#1090;&#1080;&#1080;</a></li>
<li class="header-menu__item"><a class="header-menu__link " onclick = "show ('3')"><span class="header-icon faq-icon"></span>F.A.Q.</a></li>
<li class="header-menu__item"><a target="_blank" target="_blank"href="http://vk.com/club37010444" target="_blank" class="header-menu__link"><span class="header-icon vk-icon"></span>&#1052;&#1099; &#1042;&#1050;&#1086;&#1085;&#1090;&#1072;&#1082;&#1090;&#1077;</a></li>
</ul></div>
<body>
<?

	$get_list = $mysqli->query ("SELECT * FROM `list`;");
		if ($get_list->num_rows > 0) {
			while ($gdata = $get_list->fetch_assoc ()) {
				
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
                            <a class="popup-price product_<?=$gdata["id"]?>">
                                <div style="margin-left:4px;"><?=$gdata["price"]?> &#1056;</div> 
                            </a>
                            <div class="clear"></div>
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
                                <div class="pay-block pay-block2">
                                    <div class="pay-left">&#1057;&#1087;&#1086;&#1089;&#1086;&#1073; &#1086;&#1087;&#1083;&#1072;&#1090;&#1099;:</div>
                                    <div class="pay-right">
										<?
											$hm = ["qiwi", "yandex", "webmoney"];
											for ($t = 0; $t < 3; $t++) {
												if ($pdata[$hm[$t]] != "Временно не доступен") {
													$pic = $hm[$t];
													if ($hm[$t] == "webmoney") {
														$pic = "web";
													}
													?>
													<a onclick = "change('<?=$hm[$t]?>', 'forpay_<?=$gdata["id"]?>', 'block_<?=$gdata["id"]?>', 'ans_<?=$gdata["id"]?>')"><img src="images/<?=$pic?>.png" alt=""></a>
													<?
												}
											}
										?>
                                    </div>
                                </div>
                                <div class="pay-block">
                                    <div class="pay-left"></div>
									<div id = "ans_<?=$gdata["id"]?>" class="pay-right" style = "display: block">
										 <div id = "auto_<?=$gdata["id"]?>" style = "display: block" class="pay-right">
												<div class="pay-right-descriptuion">
													<div><span>&#1058;&#1086;&#1074;&#1072;&#1088;:</span>				<?=$gdata["name"]?></div>
													<div><span>&#1050; &#1086;&#1087;&#1083;&#1072;&#1090;&#1077;:</span>				<?=$gdata["price"]?> &#1088;&#1091;&#1073;&#1083;&#1077;&#1081;</div>
													<div><span>&#1055;&#1088;&#1080;&#1084;&#1077;&#1095;&#1072;&#1085;&#1080;&#1077;:</span>			&#1047;&#1072;&#1082;&#1072;&#1079; &#8470;<?=$num?></div>
													<div><span>Номер вашего Qiwi:</span> <input type = "text" id = "number_<?=$gdata["id"]?>" name = "number" placeholder = "79680435765"></div>
												</div>
												<div id = "res_<?=$gdata["id"]?>" class="warning">В течении некоторого времени Вам будет выставлен счёт в системе QIWI.</div><br/>
												<button onclick = "qiwipay('res_<?=$gdata["id"]?>', 'number_<?=$gdata["id"]?>', '<?=$gdata["id"]?>','email_pay_<?=$gdata["id"]?>', 'trade_pay_<?=$gdata["id"]?>', 'button_<?=$gdata["id"]?>')" id = "button_<?=$gdata["id"]?>" class="green">Запросить счёт</button>
												<a onclick = "openDialog('trade_pay_<?=$gdata["id"]?>', '<?=$num?>', '<?=$gdata["price"]?>')" class="ref">Оплатить самому</a>
										 </div>
                                     </div>
									<div id = "block_<?=$gdata["id"]?>" class="pay-right" style = "display: none">
                                        <div class="pay-right-descriptuion">
                                            <div><span>&#1058;&#1086;&#1074;&#1072;&#1088;:</span>				<?=$gdata["name"]?></div>
                                            <div><span>&#1050; &#1086;&#1087;&#1083;&#1072;&#1090;&#1077;:</span>				<?=$gdata["price"]?> &#1088;&#1091;&#1073;&#1083;&#1077;&#1081;</div>
                                            <div><span>&#1050;&#1086;&#1096;&#1077;&#1083;&#1077;&#1082; &#1076;&#1083;&#1103; &#1086;&#1087;&#1083;&#1072;&#1090;&#1099;:</span>		<span id = "forpay_<?=$gdata["id"]?>"><?=$pdata["qiwi"]?></span></div>
                                            <div><span>&#1055;&#1088;&#1080;&#1084;&#1077;&#1095;&#1072;&#1085;&#1080;&#1077;:</span>			&#1047;&#1072;&#1082;&#1072;&#1079; &#8470;<?=$num?></div>
                                        </div>
                                        <div id = "result_<?=$gdata["id"]?>" class="warning">&#1042;&#1085;&#1080;&#1084;&#1072;&#1085;&#1080;&#1077;! &#1054;&#1095;&#1077;&#1085;&#1100; &#1074;&#1072;&#1078;&#1085;&#1086; &#1095;&#1090;&#1086;&#1073;&#1099; &#1074;&#1099; &#1087;&#1077;&#1088;&#1077;&#1074;&#1086;&#1076;&#1080;&#1083;&#1080; &#1076;&#1077;&#1085;&#1100;&#1075;&#1080; &#1089; &#1101;&#1090;&#1080;&#1084; &#1087;&#1088;&#1080;&#1084;&#1077;&#1095;&#1072;&#1085;&#1080;&#1077;&#1084;, &#1080;&#1085;&#1072;&#1095;&#1077; &#1089;&#1088;&#1077;&#1076;&#1089;&#1090;&#1074;&#1072; &#1085;&#1077; &#1073;&#1091;&#1076;&#1091;&#1090; &#1079;&#1072;&#1095;&#1080;&#1089;&#1083;&#1077;&#1085;&#1099; &#1072;&#1074;&#1090;&#1086;&#1084;&#1072;&#1090;&#1080;&#1095;&#1077;&#1089;&#1082;&#1080;.</div>
                                        <a onclick = "check('result_<?=$gdata["id"]?>', 'email_pay_<?=$gdata["id"]?>', 'trade_pay_<?=$gdata["id"]?>')" class="green">&#1087;&#1088;&#1086;&#1074;&#1077;&#1088;&#1080;&#1090;&#1100; &#1086;&#1087;&#1083;&#1072;&#1090;&#1091;</a>
                                    </div>
									
                                    <div class="clear"></div>
                                    <p>&#1042; &#1090;&#1077;&#1095;&#1077;&#1085;&#1080;&#1080; 15 &#1084;&#1080;&#1085;&#1091;&#1090; (&#1086;&#1073;&#1099;&#1095;&#1085;&#1086; &#1084;&#1086;&#1084;&#1077;&#1085;&#1090;&#1072;&#1083;&#1100;&#1085;&#1086;) &#1087;&#1086;&#1089;&#1083;&#1077; &#1086;&#1087;&#1083;&#1072;&#1090;&#1099; &#1085;&#1072;&#1096; &#1073;&#1086;&#1090; &#1086;&#1090;&#1087;&#1088;&#1072;&#1074;&#1080;&#1090; &#1074;&#1072;&#1084; &#1090;&#1088;&#1077;&#1081;&#1076;. &#1045;&#1075;&#1086; &#1085;&#1091;&#1078;&#1085;&#1086; &#1073;&#1091;&#1076;&#1077;&#1090; &#1087;&#1088;&#1080;&#1085;&#1103;&#1090;&#1100; &#1074; &#1090;&#1077;&#1095;&#1077;&#1085;&#1080;&#1080; 120 &#1084;&#1080;&#1085;&#1091;&#1090; &#1080;&#1085;&#1072;&#1095;&#1077; &#1086;&#1085; &#1086;&#1090;&#1084;&#1077;&#1085;&#1080;&#1090;&#1089;&#1103; &#1072;&#1074;&#1090;&#1086;&#1084;&#1072;&#1090;&#1080;&#1095;&#1077;&#1089;&#1082;&#1080;.</p>
                                </div>
                            </div>
                            <div class="clear"></div>
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
                        <div class="line-title">&#1051;&#1077;&#1085;&#1090;&#1072; &#1087;&#1086;&#1082;&#1091;&#1087;&#1086;&#1082;</div>
                    </div>   
                    <div class="clear"></div>
                </div>
            </div>
            <div class="line">
                <div id = "lenta" class="line-block"> </div>
            </div>
		  <div id = "showweapon" class="content" style = "display: block">
			<?
				$get_list = $mysqli->query ("SELECT * FROM `list` WHERE `cat` = 1;");
				if ($get_list->num_rows > 0) {
					while ($list_info = $get_list->fetch_assoc ()) {
			?>
						<div class="product product<?=$list_info["id"]?>">
							<div class="img-rarity<?=$list_info["rarity"]?>">
								<img style="margin-left:20px;" src="<?=$list_info["picture"]?>" width="240" alt="">
								<?
										if ($list_info["hot"] == 1) {
											?>
											<img class="top" src="http://design.forpost97.ru/hit.png" width="50" alt="">
											<?
										}
									?>
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
			?>
			</div>
			<div id = "showfaq" class="content" style = "display: none">
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
			</div>
			<div id = "showcase" class="content" style = "display: none">
				<?
				$get_list = $mysqli->query ("SELECT * FROM `list` WHERE `cat` = 2;");
					if ($get_list->num_rows > 0) {
						while ($list_info = $get_list->fetch_assoc ()) {
				?>
							<div class="product product<?=$list_info["id"]?>">
								<div class="img-rarity<?=$list_info["rarity"]?>">
									<img style="margin-left:20px;" src="<?=$list_info["picture"]?>" width="240" alt="">
									<?
										if ($list_info["hot"] == 1) {
											?>
											<img class="top" src="http://design.forpost97.ru/hit.png" width="50" alt="">
											<?
										}
									?>
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
				?>
			</div>
			<div id = "showgarant" class="content" style = "display: none">
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
			</div>
			
            <div class="review">
			
<div class="review-title-vid"></div><br>
<center><iframe src="//vk.com/video_ext.php?oid=-37010444&id=456239017&hash=a4ffd12c28cefadc&hd=2" width="1200" height="675"  frameborder="0"></iframe></center><br>
<div class="review-title-vk"></div><br>
<center><script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>
<div id="vk_groups"></div>
<script type="text/javascript">
VK.Widgets.Group("vk_groups", {mode: 0, width: "1200", height: "400", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 37010444);
</script></center><br>
                <div class="review-title"></div>
                <center><a href="https://vk.com/topic-37010444_30004740"><img src="/images/otziv_vk.png" alt=""></a></center>
				<div id = "pcomments">
				<?
					$all = 0;
					$all_ct = 0;
					$query = ["SELECT * FROM `comments` WHERE `ip` = '" . $ip . "' ORDER BY `id` DESC;", "SELECT * FROM `fake_comments` ORDER BY `id` DESC;"];
					for ($q = 0; $q < 2; $q++) {
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
				<?			$all_ct++;
								if ($all_ct == $pdata["comments"]) {
									break 2;
								}
							}
						}
					}
				?>
				</div>
			<br>
			<?
				if ($all > 0) {
					?>
					<div class="catPages1" align="center" id="pagesBlock1" style="clear:both;"> 
			<?
					$show = ceil ($all/$pdata["comments"]);
					if ($show > 10) {
						$show = 10;
					}
					for ($o = 1; $o <= $show; $o++) {
						?>
						<a onclick = "comments ('<?=$o?>')" class="swchItemA"><span><?=$o?></span></a> 
						<?
					}
					?>
					</div> 
					<?
				}
			?>
			<br>
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
				$("#lenta").load("lenta.php");
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


<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter33581444 = new Ya.Metrika({
                    id:33581444,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/33581444" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- BEGIN cloudim code {literal} -->
<script type="text/javascript" charset="utf-8">document.write(unescape("%3Cdiv id='cloudim_widget'%3E%3Cscript src='//static.cloudim.ru/js/chat.js' type='text/javascript'%3E%3C/script%3E%3C/div%3E"));</script> <div id="cloudim_cr" style="position:absolute; left:-9999px;"><a target="_blank" href="http://cloudim.ru/">Cloudim</a> - &#1086;&#1085;&#1083;&#1072;&#1081;&#1085; &#1082;&#1086;&#1085;&#1089;&#1091;&#1083;&#1100;&#1090;&#1072;&#1085;&#1090; &#1076;&#1083;&#1103; &#1089;&#1072;&#1081;&#1090;&#1072; &#1073;&#1077;&#1089;&#1087;&#1083;&#1072;&#1090;&#1085;&#1086;.</div>
<script type="text/javascript" charset="utf-8">
Cloudim.Chat.init({uid:21423});
</script>
<!-- {/literal} END cloudim code -->

