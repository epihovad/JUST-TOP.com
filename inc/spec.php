<?
function setPriv($mail,$pass)
{
	global $prx;

	unset($_SESSION['user']);

	if($row = getRow("SELECT * FROM {$prx}users WHERE mail='{$mail}' AND pass=md5('{$pass}') AND status=1"))
		$_SESSION['user'] = $row;

	return isset($_SESSION['user']);
}

// ПРАВАЯ КОЛОНКА
function Rcol()
{

	global $prx, $const;

	if(!$const['Rcol']) return;

	gpop();
	//gnew();
	garch();
	banners();
}

// ПОПУЛЯРНЫЕ ТОВАРЫ
function gpop()
{
	?>
	<div class="gpop rblock">
		<h2 class="head">Лучшие предложения</h2>
		<div class="inner">
			Тут слайдер с нашими лучшими предложениями
		</div>
	</div>
	<?
}

// НОВИНКИ
function gnew()
{
	?>
	<div class="gnew rblock">
		<h2 class="head">Новинки</h2>
		<div class="inner"></div>
	</div>
	<?
}

// АРХИВ
function garch()
{
	?>
	<div class="gnew rblock">
		<h2 class="head">Архив продаж</h2>
		<div class="inner">
			Тут популярная товарка из архива
		</div>
	</div>
	<?
}

// БАНЕРЫ
function banners()
{
	?>
	<div class="banners">
		тут будет слайдер с банерами
	</div>
	<?
}

// МЕНЮ (ОСНОВНОЕ)
function main()
{
	global $prx, $mainID;

	$res = sql("SELECT * FROM {$prx}pages WHERE status=1 AND main=1 ORDER BY sort,id");
	if(!$count = @mysql_num_rows($res)) return;

	$url = $_SERVER['REQUEST_URI'];

	while($row = mysql_fetch_assoc($res))
	{
		$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
		$cur = $row['id']==$mainID || ($url=='/' && $link=='/') ? true : false;

		?><div><a id="lnk<?=$row['id']?>" href="<?=$link?>"<?=$cur?' class="cur"':''?>><?=$row['name']?></a></div><?
	}
}

// МЕНЮ (ВЕРХНЕЕ)
function tmain()
{
	global $prx, $mainID;
	
	$res = sql("SELECT * FROM {$prx}pages WHERE status=1 AND tmain=1 ORDER BY sort,id");
	if(!$count = @mysql_num_rows($res)) return;
	
	$url = $_SERVER['REQUEST_URI'];

	?><div class="tmain fleft"><ul><?
	$i=1;
	while($row = mysql_fetch_assoc($res))
	{
		$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
		$cur = $row['id']==$mainID || $row['link'] == $url || ($url=='/' && $link=='/') ? true : false;
		
    ?><li><a href="<?=$link?>" class="tm<?=$row['id']?><?=$cur?' active':''?>"><?=$row['name']?></a></li><?
	}
	?></ul></div><?
}

// МЕНЮ (ФУТЕР)
function bmain()
{
  global $prx, $mainID;

	$url = $_SERVER['REQUEST_URI'];

	?>
	<div class="bmain">
		<div class="row">

			<?
			$find_cur = 0;
			ob_start();
			$res = sql("SELECT * FROM {$prx}pages WHERE id_parent = 12 AND status = 1 AND bmain = 1 ORDER BY sort,id");
			if(mysql_num_rows($res)){
				?><ul><?
				while($row = mysql_fetch_assoc($res)){
					$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
					$cur = $row['id']==$mainID || ($url=='/' && $link=='/') ? true : false;
					if($cur) $find_cur++;
					?><li><a pid="<?=$row['id']?>" href="<?=$link?>"<?=$cur?' class="cur"':''?>><?=$row['name']?></a></li><?
				}
				?></ul><?
			}
			$data = ob_get_clean();
			?>
			<div class="cell b1<?=$find_cur?' cur':''?>">
				<div class="h"><span></span>Информация</div>
				<?=$data?>
			</div>

			<?
			$find_cur = 0;
			ob_start();
			$res = sql("SELECT * FROM {$prx}pages WHERE id_parent = 13 AND status = 1 AND bmain = 1 ORDER BY sort,id");
			if(mysql_num_rows($res)){
				?><ul><?
				while($row = mysql_fetch_assoc($res)){
					$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
					$cur = $row['id']==$mainID || ($url=='/' && $link=='/') ? true : false;
					if($cur) $find_cur++;
					?><li><a pid="<?=$row['id']?>" href="<?=$link?>"<?=$cur?' class="cur"':''?>><?=$row['name']?></a></li><?
				}
				?></ul><?
			}
			$data = ob_get_clean();
			?>
			<div class="cell b2<?=$find_cur?' cur':''?>">
				<div class="h"><span></span>Служба поддержки</div>
				<?=$data?>
			</div>

			<?
			$find_cur = 0;
			ob_start();
			$res = sql("SELECT * FROM {$prx}pages WHERE id_parent = 14 AND status = 1 AND bmain = 1 ORDER BY sort,id");
			if(mysql_num_rows($res)){
				?><ul><?
				while($row = mysql_fetch_assoc($res)){
					$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
					$cur = $row['id']==$mainID || ($url=='/' && $link=='/') ? true : false;
					if($cur) $find_cur++;
					?><li><a pid="<?=$row['id']?>" href="<?=$link?>"<?=$cur?' class="cur"':''?>><?=$row['name']?></a></li><?
				}
				?></ul><?
			}
			$data = ob_get_clean();
			?>
			<div class="cell b3<?=$find_cur?' cur':''?>">
				<div class="h"><span></span>Для Вас</div>
				<?=$data?>
			</div>

			<div class="cell b4">
				<div class="h"><span></span>Личный кабинет</div>
				<ul>
					<li><a pid="lc" href="/">Войти</a></li>
				</ul>
			</div>
		</div>
	</div>
	<?
}

function news()
{
  global $prx;

  $limit = (int)set('count_news_column');
  $limit = $limit ? $limit : 3;

  $res = sql("SELECT * FROM {$prx}news WHERE status=1 ORDER BY `date` DESC LIMIT {$limit}");
  if(!@mysql_num_rows($res)) return;

  ?>
  <div id="news">
  <div class="h left"><div class="bk3">Новости</div></div>
  <?
  while($row = mysql_fetch_assoc($res))
  {
    ?>
    <div class="item">
      <div class="line"></div>
      <div class="title"><?=$row['name']?></div>
      <? if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/news/{$row['id']}.jpg")){
        ?><a href="/news/<?=$row['link']?>.htm" rel="nofollow" class="im"><img src="/news/160x-/<?=$row['id']?>.jpg"></a><div class="clear"></div><?
      }?>
      <div class="preview"><?=$row['preview']?></div>
      <a href="/news/<?=$row['link']?>.htm" title="<?=htmlspecialchars($row['name'])?>" class="bk4">Подробнее</a>
    </div>
    <?
  }
  ?></div><?
}

// ОТЗЫВЫ
function reviews($add_img = false)
{
	global $prx;

	?>
	<div class="reviews">

		<div class="review">
			<div class="review-in">
				<? if($add_img){ ?>
					<a href="/goods.php" class="review-good">
						<img src="/uploads/goods/glist1.jpg">
					</a>
				<? } ?>
				<div>
					<div class="ans">
						<div class="smile-rate t1"></div>
						<div class="info">
							<div class="name">Виталий<span>(3 июня 2016 в 21:32)</span></div>
							<div class="star-rate"><div></div></div>
							<div class="txt">Отличная вещь! Давно хотел купить, цена очень вкусная, качество достойное, менеджеры молодцы.</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="ans" style="padding-left:110px;">
						<div class="ava-admin"></div>
						<div class="info">
							<div class="name">Администрация<span>(3 июня 2016 в 22:51)</span></div>
							<div class="txt">Уважаемый Виталий, спасибо за добрые слова в адрес нашего магазина.<br>Мы будем рады снова увидеть Вас на нашем сайте!</div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>

		<div class="review">
			<div class="review-in">
				<? if($add_img){ ?>
					<a href="/goods.php" class="review-good">
						<img src="/uploads/goods/glist2.jpg">
					</a>
				<? } ?>
				<div>
					<div class="ans">
						<div class="smile-rate t2"></div>
						<div class="info">
							<div class="name">Афанасий<span>(3 июня 2016 в 21:32)</span></div>
							<div class="star-rate"><div></div></div>
							<div class="txt">Отличная вещь! Давно хотел купить, цена очень вкусная, качество достойное, менеджеры молодцы.</div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>

		<div class="review">
			<div class="review-in">
				<? if($add_img){ ?>
					<a href="/goods.php" class="review-good">
						<img src="/uploads/goods/glist3.jpg">
					</a>
				<? } ?>
				<div>
					<div class="ans">
						<div class="smile-rate t3"></div>
						<div class="info">
							<div class="name">Недовольный клиент<span>(3 июня 2016 в 21:32)</span></div>
							<div class="star-rate"><div></div></div>
							<div class="txt">Ужасно и мучительно долго пришлось ждать доставку. У меня с Америки товары приходили быстрее.</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="ans" style="padding-left:110px;">
						<div class="ava-admin"></div>
						<div class="info">
							<div class="name">Администрация<span>(3 июня 2016 в 22:51)</span></div>
							<div class="txt">Уважаемый клиент, Вы заранее были предупреждены, когда выбрали товар из нашего <a href="">АРХИВА</a>.<br>
								Да, бывает такое, что доставку товара из данной категории в 10-15% случаев приходится ждать дольше, прежде всего это связано с тем,
								что эта продукция перестала продаваться массово и ... Но ради наших клиентов мы готовы восстановить наши отношения со старыми поставщиками и приобрести
								у них, в основном, по розничной цене (без каких-либо скидок для нас) товар...
							</div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>

	</div>
	<?
}

function navigate_goods_icon($id)
{
  global $prx;

  $q = "SELECT 	g1.id, g1.sort, g1.url, g1.link, g1.name
        FROM (
          SELECT id, sort, url, link, name
          FROM {$prx}goods
          WHERE status = 1 AND id < {$id}
          ORDER BY sort DESC, id DESC
          LIMIT 1
        ) g1
        UNION ALL
        SELECT g2.id, g2.sort, g2.url, g2.link, g2.name
        FROM
        (
          SELECT id, sort, url, link, name
          FROM {$prx}goods
          WHERE status = 1 and id <> {$id}
          ORDER BY sort DESC, id DESC
          LIMIT 1
        ) g2	
        ORDER BY sort, id
        LIMIT 1";
  $prev = getRow($q);

	$q = "SELECT 	g1.id, g1.sort, g1.url, g1.link, g1.name
        FROM (
          SELECT id, sort, url, link, name
          FROM {$prx}goods
          WHERE status = 1 AND id > {$id}
          ORDER BY sort, id
          LIMIT 1
        ) g1
        UNION ALL
        SELECT g2.id, g2.sort, g2.url, g2.link, g2.name
        FROM
        (
          SELECT id, sort, url, link, name
          FROM {$prx}goods
          WHERE status = 1 AND id <> {$id}
          ORDER BY sort, id
          LIMIT 1
        ) g2	
        ORDER BY sort DESC, id DESC
        LIMIT 1";
	$next = getRow($q);

	?>
  <div id="nav-goods">
    <?
    if($prev){
			?>
      <a href="<?=$prev['url']?><?=$prev['link']?>.htm" class="prev" title="<?=htmlspecialchars($prev['name'])?>">
        <span><i></i></span><img src="/uploads/goods/90x90/<?=$prev['id']?>.jpg">
      </a>
      <?
    }
    if($next){
      ?>
      <a href="<?=$next['url']?><?=$next['link']?>.htm" class="next" title="<?=htmlspecialchars($next['name'])?>">
        <img src="/uploads/goods/90x90/<?=$next['id']?>.jpg"><span><i></i></span>
      </a>
      <?
    }
    ?>
		<div class="clear"></div>
	</div>
  <?
}

// СЧЕТЧИКИ (ФУТЕР)
function counters()
{
	global $prx;

	$res = sql("SELECT html FROM {$prx}counters WHERE status=1 ORDER BY sort,id");
	while($row = @mysql_fetch_assoc($res))
		echo "&nbsp;{$row['html']}&nbsp;";
}

// ПОЛЕ ДЛЯ ВВОДА КОЛ-ВА
function chQuant($name='quant',$quant=1)
{
	?>
	<div class="input-group">
		<span class="input-group-btn">
			<button type="button" class="btn btn-default btn-number"<?=$quant<=1?' disabled="disabled"':''?> data-type="minus">
				<span class="glyphicon glyphicon-minus"></span>
			</button>
		</span>
		<input type="text" name="<?=$name?>" class="form-control input-number" value="<?=$quant?>" min="1" max="99">
		<span class="input-group-btn">
			<button type="button" class="btn btn-default btn-number"<?=$quant>=99?' disabled="disabled"':''?> data-type="plus">
				<span class="glyphicon glyphicon-plus"></span>
			</button>
		</span>
	</div>
	<?
}

// Подарки (в корзине)
function cartGift()
{
	?>
  <tr class="gift">
    <td class="c1">4</td>
    <td class="c2"><a href=""><img src="/goods/45x45/1.jpg"></a></td>
    <td class="c3"><span>Подарок!</span> Монопод</td>
    <td class="c4">650 <span class="rub">руб</span></td>
    <td class="c5">1</td>
    <td class="c6">650 <span class="rub">руб</span></td>
    <td class="c7">650 <span class="rub">руб</span></td>
    <td class="c8">0 <span class="rub">руб</span></td>
    <td class="cell-fake"></td>
  </tr>
	<?
}

// Страницы навигации
// show_navigate_pages(количество страниц,текущая,'ссылка = ?topic=news&page=')
function show_navigate_pages()
{
	global $count_obj,$count_obj_on_page,$kol_str,$cur_page,$dopURL;
	$x = $kol_str; $p = $cur_page;
	if($x<2) return;
	
	preg_match('/(&page=[0-9]+)/',$_SERVER['REQUEST_URI'],$h);
	$link = str_replace($h[1],'',$_SERVER['REQUEST_URI']);
	
	?><div id="navPages"><div class="pages"><?
	if($p!=1)
	{
		?><a class="bk4" href="<?=$link?>&page=<?=($p-1)?><?=$dopURL?>" title="предыдущая">Назад</a><?
	}  
	if($x<4)
	{
		for($i=1;$i<=$x;$i++)
		{
			if($i==$p) echo '<b class="bk4">'.$i.'</b>';
			else echo get_href($link,$i);
		}
	}
	if($x==4)
	{
		if($p==1) 		echo '<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 1
		if($p==2) 		echo get_href($link,1).'<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 2
		if(($p-1)==2) echo get_href($link,1).'<span>...</span>'.get_href($link,$p-1).'<b class="bk4">'.$p.'</b>'.get_href($link,$x);// 3
		if($p==$x) 		echo get_href($link,1).'<span>...</span>'.get_href($link,$x-1).'<b class="bk4">'.$p.'</b>';// 4
	}
	if($x>4)
	{
		if($p==1) 					echo '<b class="bk4">1</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 1
		elseif($p==2) 			echo get_href($link,1).'<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 2
		elseif(($p-1)==2) 	echo get_href($link,1).'<span>...</span>'.get_href($link,$p-1).'<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 3
		elseif(($x-$p)==1) 	echo get_href($link,1).'<span>...</span>'.get_href($link,$p-1).'<b class="bk4">'.$p.'</b>'.get_href($link,$x);// 4
		elseif($p==$x) 			echo get_href($link,1).'<span>...</span>'.get_href($link,$x-1).'<b class="bk4">'.$p.'</b>';// 5
		else								echo get_href($link,1).'<span>...</span>'.get_href($link,$p-1).'<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);
	}
	if($p<$x)
	{
		?><a class="bk4" href="<?=$link?>&page=<?=($p+1)?>" title="следующая">Вперед</a><?
	}	
	$start = $count_obj_on_page*$p-$count_obj_on_page;
	$end = $count_obj_on_page+$start;
  $end = $end>$count_obj?$count_obj:$end;
	?></div><div class="info">Показано с <?=$start+1?> по <?=$end?> из <?=$count_obj?> (<?=$x?> <?=num2str($x,'страница')?>)</div></div><?
}
function get_href($link,$page)
{
	global $dopURL;
	ob_start();
		?><a class="bk4" href="<?=$link?>&page=<?=$page?><?=$dopURL?>"><?=$page?></a><?
	return ob_get_clean();
}

function num2str($count,$txt='товар')
{
	$pat = array( 'товар'=>array('товар','товара','товаров'),
                'страница'=>array('страница','страницы','страниц')
  );
	
	$count = $count%100;
  if($count>19) $count = $count%10;
  switch($count)
	{
    case 1:  return($pat[$txt][0]);
    case 2: case 3: case 4:  return($pat[$txt][1]);
    default: return($pat[$txt][2]);
  }
}

// ВЫВОД ALERT ОБ ОШИБКЕ (и прерывание выполнения)
function jAlert($text,$method='',$type='',$func='',$prm='',$exit=true)
{
	$method = $method ? $method : 'show';
	$type = $type ? $type : 'alert';
	$prm = $prm ? $prm : '{}';
	?><script>
		top.jQuery(document).jAlert('<?=$method?>','<?=$type?>','<?=$text?>',function(){<?=$func?>},<?=$prm?>);
		top.jQuery('#ajax').attr('src','/inc/none.htm');
	</script><?
  if($exit) exit;
}