<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="keywords" content="<?=$keywords?>" />
	<meta name="description" content="<?=$description?>" />
  <title><?=$title?></title>

  <link rel="icon" href="/favicon.ico?v=1" type="image/x-icon" />
  <link rel="shortcut icon" href="/favicon.ico?v=1" type="image/x-icon" />

  <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css"/>
  <link rel="stylesheet" href="/css/slider.css?v=1" type="text/css"/>
	<link rel="stylesheet" href="/css/call-btn.css" type="text/css" />
	<link rel="stylesheet" href="/js/jquery/blueimp-gallery/blueimp-gallery.css" type="text/css" />
	<link rel="stylesheet" href="/js/jquery/blueimp-gallery/blueimp-gallery-indicator.css">
  <link rel="stylesheet" href="/css/style.css" type="text/css" />
	<?=$const['css_links']?>
  <link rel="stylesheet" href="/css/media.css" type="text/css" />

  <script src="/js/jquery/jquery-3.1.1.min.js"></script>
	<script src="/js/jquery/jquery-migrate-3.0.0.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <script src="/js/otscript.js?v=3"></script>
	<script src="/js/jquery/blueimp-gallery/blueimp-gallery.js"></script>
	<script src="/js/jquery/blueimp-gallery/blueimp-gallery-indicator.js"></script>

	<script src="/js/jquery/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
	<link rel="stylesheet" href="/js/jquery/arcticmodal/jquery.arcticmodal-0.3.css">
	<link rel="stylesheet" href="/js/jquery/arcticmodal/themes/simple.css">
	<?=$const['js_links']?>
  <meta name="viewport" content="user-scalable=no,width=device-width" />

  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
	<script type="text/javascript" src="/js/utils.js"></script>
	<script type="text/javascript" src="/js/spec.js?v=1"></script>

	<script type="text/javascript" src="/js/jquery/jquery.mousewheel.min.js"></script>
	<script type="text/javascript" src="/inc/advanced/jB/jquery.jB.js"></script>
	<link rel="stylesheet" href="/inc/advanced/jAlert/jAlert.css" type="text/css" />
	<script type="text/javascript" src="/inc/advanced/jAlert/jquery.jAlert.js"></script>

  <?/*
  <script type="text/javascript" src="/js/form.min.js"></script>
  <script type="text/javascript" src="/js/utils.js"></script>
  <script type="text/javascript" src="/inc/advanced/jStars/jquery.jStars.js"></script>*/?>
</head>
<body>

<div id="call-btn"><div></div></div>
<div id="up-btn" class="glyphicon glyphicon-chevron-up"></div>

<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
	<div class="slides"></div>
	<h3 class="title"></h3>
	<a class="prev">‹</a>
	<a class="next">›</a>
	<a class="close">×</a>
	<a class="play-pause"></a>
	<ol class="indicator"></ol>
</div>

<div class="Around">
  <div class="AroundRow">
    <div class="Lcol">
      <div class="lfix">
        <div id="logo"><a href="/"><img src="/img/logo.png" alt=""><div>только лучшее!</div></a></div>
        <div class="main"><?=main()?></div>
				<div class="sz" style="text-align:center; color:#fff;"></div>
      </div>
    </div>
    <div class="Center">
			<?
			//if($index){
				?>
				<a href="/cart.php"><div class="hcart"><span class="cnt"><?=$_SESSION['cart']['quant']?></span></div></a>
				<div class="Header">
          <?=tmain()?>
					<div class="header-phone fleft">
						<div class="glyphicon glyphicon-phone-alt fleft"></div><div class="number fleft">+7 (495) 088-32-84</div>
						<div class="clear"></div>
						<div class="note">каждый день с 8:00 до 20:00</div>
					</div>
				</div>
				<?
			//}
			if($const['Rcol']){
				?>
				<div class="Middle"><?=$content?></div>
				<div class="Rcol"><?=Rcol()?></div>
				<div class="clear"></div>
				<?
			} else {
			  if(!$index){?><div class="clear" style="padding-top:70px;"></div><?}
				echo $content;
			}
			?>
    </div>
  </div>

</div>

<div class="Footer">
	<div class="Subscribe">
		<div class="h">Подпишись на рассылку и будь вкурсе самых горячих предложений</div>
		<input type="text" class="form-control" value="" placeholder="Введите Ваш Email адрес" name="mail"><a href="" rel="nofollow" class="btn">Подписаться</a>
	</div>
  <div class="inFooter">
    <?=bmain()?>
		<div class="sepline"><div></div></div>
		<div class="bottom">
			<div class="row">
				<div class="cell copy">
					<p>Обращаем Ваше внимание на то, что информация, представленная на данном сайте, не является публичной офертой, определяемой положениями ч. 2 ст. 437 ГК Российской Федерации.</p>
					<p>&copy; ООО «ДжастТоп»<br>Все права защищены</p>
				</div>
				<div class="cell us"><a href="/"><img src="/img/logo-footer.png" alt=""><div>только лучшее!</div></a></div>
				<div class="cell counters"><?=counters()?></div>
			</div>
		</div>
  </div>
</div>

<iframe name="ajax" id="ajax"></iframe>

</body>
</html>