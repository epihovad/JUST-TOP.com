<?
$title = $good['name'];
foreach(array('title','keywords','description') as $val)
	if($good[$val]) $$val = $good[$val];

ob_start();
?><link type="text/css" rel="stylesheet" href="/css/goods.css" /><?
$const['css_links'] = ob_get_clean();

ob_start();
?><script src="/js/goods.js"></script><?
$const['js_links'] = ob_get_clean();

ob_start();
?>
	<div class="pd25">

    <h1><?=$good['name']?></h1>

		<div class="clear" style="padding-bottom:40px;"></div>

    <?
    $images = array();
    // промо фото
		$images[] = array(
		  'base' => "/uploads/goods_promo/400x400/{$good['id']}.jpg",
		  'href' => "/uploads/goods_promo/{$good['id']}.jpg",
      'src' => "/uploads/goods_promo/90x90/{$good['id']}.jpg"
    );
    // остальные фото
		$imgs = getImages('goods',$good['id']);
    foreach ($imgs as $im){
			$images[] = array(
				'base' => "/uploads/goods/400x400/{$im}",
			  'href' => "/uploads/goods/{$im}",
        'src' => "/uploads/goods/90x90/{$im}"
      );
    }
    //pre($images);
    ?>

		<div class="gim">
			<div class="gim-chief<?=$good['is_archive']?' archive':''?>"><img src="/uploads/goods_promo/400x400/<?=$good['id']?>.jpg"></div>
			<div class="gim-other">
				<?
				$i=0;
				foreach ($images as $img){
					?><a class="im<?=!$i++?' active':''?>" base="<?=$img['base']?>" href="<?=$img['href']?>" title="<?=$good['name']?>" data-gallery=""><img src="<?=$img['src']?>"></a><?
				}
				?>
			</div>
			<div class="gim-over"></div>
		</div>

		<div class="good-preview content"><?=$good['preview']?></div>

		<div class="good-tocart">

      <?
			$mods = array();
			$r = sql("SELECT * FROM {$prx}mods WHERE id_good = '{$good['id']}' and status = 1 ORDER BY sort, id");
			while ($mod = @mysql_fetch_assoc($r)){
				$mod = array_merge($mod, array(
					'base' => "/uploads/mods/400x400/{$mod['id']}.jpg",
				  'href' => "/uploads/mods/{$mod['id']}.jpg",
          'src' => "/uploads/mods/45x45/{$mod['id']}.jpg")
        );
				$mods[] = $mod;
			}
			//pre($mods);
      ?>

			<div class="lb">Цена:</div>
			<div class="price">
				<div class="price-actual"><?=$mods[0]['price']?> руб</div>
				<div class="price-old"><?=$mods[0]['old_price']?$mods[0]['old_price'].' руб':''?></div>
			</div>
			<div class="arch-note"><span>товар доступен для заказа</span><br>доставка от 15 до 60 дней</div>
			<div class="sep"></div>

			<div class="lb"><?=$good['mods_type']?>:<div><?=$mods[0]['name']?></div></div>
			<div class="gim-mods">
				<?
        $i = sizeof($mods);
				$mods = array_reverse($mods);
				foreach ($mods as $mod){
					?><a class="im<?=!--$i?' active':''?>" rel="nofollow" base="<?=$mod['base']?>" href="<?=$mod['href']?>"
          title="<?=htmlspecialchars($good['name'].' ('.$good['mods_type'].': '.$mod['name'].')')?>" mod_type="<?=htmlspecialchars($mod['name'])?>"
          mod="<?=$mod['id']?>" price="<?=$mod['price']?>" old_price="<?=$mod['old_price']?>"><img src="<?=$mod['src']?>"></a><?
				}
				?>
			</div>
			<div class="gim-mods-over"></div>
			<div class="sep"></div>

			<div class="lb">Количество:</div>
      <?=chQuant()?>
			<div class="sep"></div>

      <a href="" rel="nofollow" class="btn tocart btn-big">Хочу !</a>
      <div class="clear"></div>
      <a href="" rel="nofollow" class="btn one-click btn-mini">Заказ в 1 клик</a>

			<div class="good-stats">
				<div>
					<table>
						<tr><th>Просмотров всего:</th><td>2 744</td></tr>
						<tr><th>Просмотров за месяц:</th><td>682</td></tr>
						<tr><th>Положили в корзину:</th><td>1 189</td></tr>
						<tr><th>Покупок:</th><td>1 231</td></tr>
						<tr><th>Покупок по предоплате:</th><td>817</td></tr>
						<tr><th>Покупок с частичной оплатой:</th><td>274</td></tr>
					</table>
				</div>
			</div>

		</div>

		<div class="clear" style="padding-bottom:20px;"></div>

		<ul class="nav nav-tabs">
			<li class="active"><a href="#good-info" data-toggle="tab"><span class="glyphicon info"></span> Описание</a></li>
			<li><a href="#good-spec" data-toggle="tab"><span class="glyphicon set"></span> Характеристики</a></li>
			<li><a href="#good-video" data-toggle="tab"><span class="glyphicon video"></span> Видео</a></li>
			<li><a href="#good-reviews" data-toggle="tab"><span class="glyphicon rvs"></span> Отзывы (5)</a></li>
      <li><a href="#good-expert" data-toggle="tab"><span class="glyphicon expert"></span> Экспертное мнение</a></li>
		</ul>

		<div class="tab-content">
			<!-- Описание -->
			<div class="tab-pane fade active in content" id="good-info"><?=$good['text1']?></div>
			<!-- Характеристики -->
			<div class="tab-pane fade content" id="good-spec"><?=$good['text2']?></div>
			<!-- Видео -->
			<div class="tab-pane fade content" id="good-video"><?=$good['text3']?></div>
			<!-- Отзывы -->
			<div class="tab-pane fade" id="good-reviews">
				<?=reviews()?>
			</div>
      <!-- Экспертное мнение -->
      <div class="tab-pane fade content" id="good-expert"><?=$good['text4']?></div>
		</div>
	</div>

  <?=navigate_goods_icon($good['id'])?>

<?
$content = ob_get_clean();