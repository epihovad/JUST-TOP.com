<?
require('inc/common.php');
ob_start();

$r = sql("SELECT * FROM {$prx}slider WHERE status=1 ORDER BY sort,id");
$slider = array();
while ($arr = @mysql_fetch_assoc($r)){
  $slider[] = $arr;
}
if(sizeof($slider)){
  ?>
	<div class="vmgroup ot-single">

    <div class="vmproduct slide">
      <div id="productslide95" class="carousel slide noconflict productslide carousel-fade" data-interval="5000" data-ride="carousel">
        <div class="carousel-inner">
					<?
					$i=0;
					foreach ($slider as $slide){
						?>
            <div class="item<?=!$i++?' active':''?>">
              <div class="vmproduct">
                <div class="vmproduct_img">
                  <div class="vmproduct_img_i">
                    <a href="<?=$slide['link']?>" title="<?=$slide['name']?>">
                      <img src="/slider/right/<?=$slide['id']?>.jpg" alt="<?=$slide['name']?>" class="featuredProductImage"/>
                    </a>
                  </div>
                </div>
                <div class="vmproduct_bg" style="background-image: url(/slider/left/<?=$slide['id']?>.jpg);">
                  <div class="spacer">
                    <h3 class="vmproduct_name">
                      <a href="<?=$slide['link']?>"><?=$slide['name']?></a>
                    </h3>
                    <div class="product-s-desc"><?=$slide['note']?></div>
                    <div class="PricesalesPrice vm-display vm-price-value">
                      <span class="vm-price-desc"></span><span class="PricesalesPrice"><?=number_format($slide['price'],0,',',' ')?> руб.</span>
                    </div>
                  </div>
                </div>
                <div class="clear"></div>
              </div>
            </div>
						<?
					}
					?>
        </div>

        <div class="carousel-nav">
          <a class="carousel-control control-box left" href="#productslide95" data-slide="prev">
            <i class="glyphicon glyphicon-menu-left"></i>
          </a>
          <ol class="carousel-indicators">
						<?
						$i=0;
						foreach ($slider as $slide){
							?>
              <li data-target="#productslide95" data-slide-to="<?=$i++?>" class="li_img active">
                <img src="/slider/right/<?=$slide['id']?>.jpg"/>
              </li>
							<?
						}
						?>
          </ol>
          <a class="carousel-control control-box right" href="#productslide95" data-slide="next">
            <i class="glyphicon glyphicon-menu-right"></i>
          </a>
          <div class="carousel-indicator-over"></div>
        </div>

      </div>
    </div>

		<div class="sep clear"></div>
	</div>
<?}?>

	<h1 class="catalog">Лучшие предложения на сегодня</h1>
	<div class="pd25">
		<div class="glist">
			<?
			$count = 4;
			for ($i = 0; $i <= $count; $i++) {
				$l = $i % 2 == 0;

				if ($l) { ?><div class="row"><? }
				?>
				<div class="good <?=$l?'l':'r'?> col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<a href="/goods.php" class="good-in">
						<div class="name">Народная экшн-камера SJCAM SJ4000 WiFi</div>
						<div class="price">
							<div class="price-actual">2990 руб</div>
							<div class="price-old">3690 руб</div>
						</div>
						<div class="clear pre-img"></div>
						<div class="adv fright">
							<div><span></span>Full HD видео уровня GoPro</div>
							<div><span></span>17 креплений и аквабокс в комплекте</div>
							<div><span></span>Камеру можно использовать как регистратор</div>
							<div><span></span>Цветной LCD дисплей</div>
							<div><span></span>Низкая цена за высокое качество</div>
						</div>
						<div class="img">
							<img src="/uploads/goods/glist<?=$i+1?>.jpg">
						</div>
						<div class="clear"></div>
						<div class="corn"><div></div></div>
						<div class="more">Подробнее</div>
					</a>
				</div>
				<?
				if (!$l || $i == $count) { ?></div><? }
			}
			?>
		</div>
	</div>

	<h1>Наши архив</h1>
	<div class="alist">
		<?
		$count = 9;
		for ($i = 0; $i <= $count; $i++) {
			?>
			<div class="good">
				<a href="/goods.php" class="good-in">
					<div class="ar">
						<div class="img">
							<img src="/uploads/goods/glist-arch.jpg" style="width:200px">
						</div>
						<div class="name">Народная экшн-камера SJCAM SJ4000 WiFi</div>
						<div class="price">2990 руб</div>
					</div>
				</a>
			</div>
			<?
		}
		?>
		<div class="clear"></div>
	</div>

	<h1>Отзывы наших клиентов</h1>
	<div class="pd25" style="padding-top:40px; padding-bottom:40px;">
		<?=reviews(true)?>
	</div>
<?
$content = ob_get_clean();
require('tpl/tpl.php');