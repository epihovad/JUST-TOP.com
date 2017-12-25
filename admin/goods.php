<?
require('inc/common.php');

$rubric = 'Продукция';
$tbl = 'goods';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'saveall':
			updateSitemap();
			?><script>alert('Данные успешно сохранены');top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$value)
				$$key = clean($value);
      //pre($_POST); exit;

			if(!$name) errorAlert('необходимо указать Наименование!');
      if(!$id_catalog) errorAlert('необходимо указать принадлежность к каталогу!');

			$updateLink = false;
			$where = $id ? " AND id<>{$id}" : '';

			if(getField("SELECT id FROM {$prx}{$tbl} WHERE name = '{$name}'{$where}"))
				errorAlert('Товар с таким Наименованием уже существует');
			//
			if(getField("SELECT articul FROM {$prx}{$tbl} WHERE articul = '{$articul}'{$where}"))
				errorAlert('Товар с таким Артикулом уже существует');
			//
			if($link)
			{
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link = '{$link}'{$where}"))
					$updateLink = true;
			}
			else
			{
				$link = makeUrl($name);
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link = '{$link}'{$where}"))
					$updateLink = true;
			}

			// полная ссылка на товар
			$rb = gtv('catalog','*',$id_catalog);
			$url = getCatUrl($rb,false);

			$set = "id_catalog='{$id_catalog}',
							name='{$name}',
							url='{$url}',
							articul='{$articul}',
							matrix='{$matrix}',
							advantages=".($advantages ? "'{$advantages}'" : 'NULL').",
							preview=".($preview ? "'{$preview}'" : 'NULL').",
							text1=".($text1 ? "'{$text1}'" : 'NULL').",
							text2=".($text2 ? "'{$text2}'" : 'NULL').",
							text3=".($text3 ? "'{$text3}'" : 'NULL').",
							text4=".($text4 ? "'{$text4}'" : 'NULL').",
							mods_type=".($mods_type ? "'{$mods_type}'" : 'NULL').",
							is_new='{$is_new}',
							is_hit='{$is_hit}',
							is_archive='{$is_archive}',
							status='{$status}',
							h1=".($h1 ? "'{$h1}'" : 'NULL').",
							title=".($title ? "'{$title}'" : 'NULL').",
							keywords=".($keywords ? "'{$keywords}'" : 'NULL').",
							description=".($description ? "'{$description}'" : 'NULL');
			if(!$updateLink)
				$set .= ",link='{$link}'";

			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			// загружаем картинки
			foreach (array('goods_promo','goods','goods_arch') as $dir){

				if(sizeof((array)$_FILES[$dir]['name']))
				{
					foreach($_FILES[$dir]['name'] as $num=>$null)
					{
						if(!$_FILES[$dir]['name'][$num]) continue;

						// формируем имя картинки
						if($new_name = get_pic_name($id, $dir))
						{
							remove_img($new_name, $dir);

							$path = $_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$new_name}";
							@move_uploaded_file($_FILES[$dir]['tmp_name'][$num],$path);
							@chmod($path,0644);
							resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$new_name}",array('400','400'),$_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/400x400/{$new_name}",1,'');
							resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$new_name}",array('90','90'),$_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/90x90/{$new_name}",1,'');
							resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$new_name}",array('45','45'),$_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/45x45/{$new_name}",1,'');
						}
					}
				}

			}

			// загружаем модификации
			$mods = array();
			foreach ($_POST['mods']['articul'] as $n => $articul){
				$mods[] = array(
					'id' => (int)$_POST['mods']['id'][$n],
					'articul' => clean($articul),
					'name' => clean($_POST['mods']['name'][$n]),
					'price' => (int)$_POST['mods']['price'][$n],
					'old_price' => (int)$_POST['mods']['old_price'][$n],
					'sort' => (int)$_POST['mods']['sort'][$n],
					'status' => (int)$_POST['mods']['status'][$n]
				);
			}
			//pre($mods); exit;
      if(sizeof($mods)) {
				$ids_mods = '';
				foreach ($mods as $n => $mod){
					$set = "id_good = '{$id}',
                  articul = '{$mod['articul']}',
                  name = '{$mod['name']}',
                  price = '{$mod['price']}',
                  old_price = '{$mod['old_price']}',
                  sort = '".($mod['sort'] > 0 && $mod['sort'] <= 99 ? $mod['sort'] : 99)."',
                  status = '{$mod['status']}'";
					if(!$id_mod = update('mods', $set, $mod['id']))
						errorAlert('Во время сохранения данных произошла ошибка.');
					$ids_mods .= ($ids_mods ? ',' : '').$id_mod;
					//
					if(!$_FILES['mods']['name'][$n]) continue;
					$new_name = $id_mod.'.jpg';
					remove_img($id_mod, 'mods');
					$path = $_SERVER['DOCUMENT_ROOT']."/uploads/mods/$new_name";
					@move_uploaded_file($_FILES['mods']['tmp_name'][$n],$path);
					@chmod($path,0644);
					resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/mods/{$new_name}",array('400','400'),$_SERVER['DOCUMENT_ROOT']."/uploads/mods/400x400/{$new_name}",1,'');
					resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/mods/{$new_name}",array('45','45'),$_SERVER['DOCUMENT_ROOT']."/uploads/mods/45x45/{$new_name}",1,'');
				}
				// мочим удалённые модели
				$r = sql("SELECT id FROM {$prx}mods WHERE NOT id IN ($ids_mods)");
				while ($arr = @mysql_fetch_assoc($r)){
					remove_img($arr['id'].'.jpg', 'mods');
					update('mods','',$arr['id']);
				}
      }

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- обновление статуса
		case 'is_new':
		case 'is_hit':
    case 'is_archive':
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- удаление одной записи
		case 'del':
      //
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $k=>$v)
      {
        //
      }
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
    // ----------------- удаление изображения
    case 'img_del':
      $id = (int)$_GET['id'];
    	$dir = $_GET['dir'];
			$fname = $_GET['fname'];
      remove_img($fname,$dir);
      ?><script>top.location.href = '<?=$script?>?red=<?=$id?>'</script><?
      break;
	}
	exit;
}
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	$id = (int)$_GET['red'];
	
	$rubric .= ' &raquo; '.($id ? 'Редактирование' : 'Добавление');
	$page_title .= ' :: '.$rubric;
	
	$good = gtv($tbl,'*',$id);
	
	ob_start();

	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <tr>
      <th class="tab_red_th"></th>
      <th>Подчинение</th>
      <td><?=dllTree("SELECT * FROM {$prx}catalog ORDER BY sort,id", 'name="id_catalog" style="width:100%"', $good['id_catalog']?$good['id_catalog']:(int)@$_SESSION['ss']['catalog'], array('0'=>'без подчинения'))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Наименование</th>
      <td><?=show_pole('text','name',htmlspecialchars($good['name']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"><?=help('Артикул должен быть уникальным')?></th>
      <th>Артикул</th>
      <td><?=show_pole('text','articul',htmlspecialchars($good['articul']))?></td>
    </tr>
		<tr>
			<th class="tab_red_th"><?=help('ссылка формируется автоматически,<br>значение данного поля можно изменить')?></th>
			<th>Ссылка</th>
			<td><?=show_pole('text','link',$good['link'])?></td>
		</tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Матрица</th>
      <td><?=dllEnum($tbl,'matrix',"name='matrix' style='width:100%'",$good['matrix'])?></td>
    </tr>
		<?=show_tr_images($id,'Промо фото','Основное фото товара (крупный план),<br>для слайдера и для страницы товара',0,'goods_promo','goods_promo')?>
		<?=show_tr_images($id,'Фото','',$count=7,'goods','goods')?>
		<?=show_tr_images($id,'Фото для архива','Фото в стиле Сепия',0,'goods_arch','goods_arch')?>
		<tr>
			<th class="tab_red_th"><?=help('Каждый пункт с новой строки')?></th>
			<th>Основные преимущества</th>
			<td><?=show_pole('textarea','advantages',$good['advantages'],0,7)?></td>
		</tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>Краткое описание</th>
			<td><?=showFck('preview',$good['preview'],'basic','100%',10)?></td>
		</tr>
		<tr>
      <th class="tab_red_th"></th>
      <th>Описание</th>
      <td><?=showFck('text1',$good['text1'])?></td>
    </tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>Характеристики</th>
			<td><?=showFck('text2',$good['text2'])?></td>
		</tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>Видео</th>
			<td><?=showFck('text3',$good['text3'])?></td>
		</tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Экспертное мнение</th>
      <td><?=showFck('text4',$good['text4'])?></td>
    </tr>
		<tr>
			<th class="tab_red_th"><?=help('Тип модификаций (цвет, размер, вид...)')?></th>
			<th>Тип модификаций</th>
			<td><?=show_pole('text','mods_type',htmlspecialchars($good['mods_type']))?></td>
		</tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>Модификации</th>
			<td>
				<style>
					.mods input { width:100%;}
					.mods .mod-number { width:20px;}
					.mods .mod-articul { width:150px;}
					.mods .mod-name { width:250px;}
					.mods .mod-price { width:110px;}
					.mods .mod-price input { text-align:center;}
          .mods .mod-sort { width:60px;}
          .mods .mod-sort input { text-align:center;}
          .mods .mod-status { width:45px;}
          .mods .mod-add { width:20px;}
				</style>
        <script src="js/goods.js"></script>
				<table class="subtab mods">
					<thead>
					<tr>
						<th class="mod-number" style="width:20px;">№</th>
						<th class="mod-articul">Артикул <?=help('артикул модели должен быть уникальным в рамках товара')?></th>
						<th class="mod-name">Наименование</th>
						<th class="mod-photo">Фото</th>
						<th class="mod-add-photo">Загрузка фото</th>
						<th class="mod-price">Цена, руб.</th>
						<th class="mod-price">Старая цена, руб.</th>
            <th class="mod-sort">Порядок</th>
            <th class="mod-status">Статус</th>
						<th class="mod-add"><img src="img/add.png" title="добавить модификацию"></th>
					</tr>
					</thead>
					<tbody>
          <?
          $r = sql("SELECT * FROM {$prx}mods WHERE id_good='{$good['id']} ORDER BY sort'");
          $i=1;
          while ($mod = mysql_fetch_assoc($r)){
            ?>
            <tr>
              <th class="mod-number"><span><?=$i++?></span><input type="hidden" name="mods[id][]" value="<?=$mod['id']?>"></th>
              <td class="mod-articul"><input type="text" value="<?=htmlspecialchars($mod['articul'])?>" name="mods[articul][]"></td>
              <td class="mod-name"><input type="text" value="<?=htmlspecialchars($mod['name'])?>" name="mods[name][]"></td>
              <td class="mod-photo">
                <?
                if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/mods/45x45/{$mod['id']}.jpg")){
                  ?><a href="/uploads/mods/<?=$mod['id']?>.jpg" class="highslide" onclick="return hs.expand(this)"><img src="/uploads/mods/45x45/<?=$mod['id']?>.jpg" width="16"></a><?
                }
                ?>
                <a href="?action=mods_del&id=<?=$good['id']?>&mod=<?=$mod['id']?>" target="ajax" title="удалить фото"><img src="img/del.png"></a>
              </td>
              <td class="mod-add-photo"><input type="file" name="mods[]"></td>
              <td class="mod-price"><input type="text" value="<?=$mod['price']?>" name="mods[price][]"></td>
              <td class="mod-price"><input type="text" value="<?=$mod['old_price']?>" name="mods[old_price][]"></td>
              <td class="mod-sort"><input type="text" value="<?=$mod['sort']?>" name="mods[sort][]"></td>
              <td class="mod-status"><input type="checkbox" value="1" name="mods[status][]"<?=$mod['status']==1?' checked':''?>></td>
              <td class="mod-del"><img src="img/del.png" title="удалить модель"></td>
            </tr>
            <?
          }
          ?>
					</tbody>
				</table>
			</td>
		</tr>
    <tr>
      <th class="tab_red_th"><?=help('Отображение товара в рубрике каталога «Новинки»')?></th>
      <th>Новинка</th>
      <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_new"',isset($good['is_new'])?$good['is_new']:0)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"><?=help('Отображение товара в рубрике каталога «Хиты продаж»')?></th>
      <th>Хит продаж</th>
      <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_hit"',isset($good['is_hit'])?$good['is_hit']:0)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Архивный товар</th>
      <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_archive"',isset($good['is_archive'])?$good['is_archive']:0)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Статус</th>
      <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($good['status'])?$good['status']:1)?></td>
    </tr>
		<tr>
			<th class="tab_red_th"><?=help('используется вместо названия в &lt;h1&gt;')?></th>
			<th>Заголовок</th>
			<td><?=show_pole('text','h1',htmlspecialchars($good['h1']))?></td>
		</tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>title</th>
			<td><?=show_pole('text','title',htmlspecialchars($good['title']))?></td>
		</tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>keywords</th>
			<td><?=show_pole('text','keywords',htmlspecialchars($good['keywords']))?></td>
		</tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>description</th>
			<td><?=show_pole('textarea','description',$good['description'])?></td>
		</tr>
		<tr>
			<th class="tab_red_th"></th>
			<th></th>
			<td align="center">
				<input type="submit" value="<?=($id?'Сохранить':'Добавить')?>" class="but1" onclick="loader(true)" />&nbsp;
				<input type="button" value="Отмена" class="but1" onclick="location.href='<?=$script?>'" />
			</td>
		</tr>
  </table>
  </form>
  <?
	$content = ob_get_clean();
}
// -----------------ПРОСМОТР-------------------
else
{
  $cur_page = $_SESSION['ss']['page'] ? $_SESSION['ss']['page'] : 1;
  $sitemap = isset($_SESSION['ss']['sitemap']);
  $f_catalog = (int)@$_SESSION['ss']['catalog'];
	$f_matrix = @$_SESSION['ss']['matrix'];
  $f_context = stripslashes($_SESSION['ss']['context']);

  $where = '';
  if($f_catalog)
  {
    $ids = getIdChilds("SELECT * FROM {$prx}catalog",$f_catalog,false);
    $where .= " AND G.id_catalog IN ({$ids})";
  }
  if($f_matrix)   $where .= " AND G.matrix = '{$f_matrix}'";
  if($f_context)	$where .= " AND (
  																	G.name LIKE '%{$f_context}%'
  															OR  G.articul LIKE '%{$f_context}%'
  														)";

  if($sitemap) $razdel['Сохранить'] = "javascript:saveall();";
  $razdel['Добавить'] = '?red=0';
  $razdel['Удалить'] = "javascript:multidel(document.red_frm,'check_del_','');";
  $subcontent = show_subcontent($razdel);

  $rubric .= ' &raquo; Общий список';
  $page_title .= ' :: '.$rubric;

  $query = "SELECT G.*%s FROM {$prx}{$tbl} G";
  if($sitemap)
  {
    $query  = sprintf($query,',S.lastmod,S.changefreq,S.priority');
    $query .= " LEFT JOIN (SELECT * FROM {$prx}sitemap WHERE `type`='{$tbl}') S ON G.id=S.id_obj";
  }	else $query  = sprintf($query,'');
  $query .= " WHERE 1{$where} GROUP BY G.id";

  $r = sql($query);
  $count_obj = (int)@mysql_num_rows($r); // кол-во объектов в базе
  $count_obj_on_page = 30; // кол-во объектов на странице
  $kol_str = ceil($count_obj/$count_obj_on_page); // количество страниц

  // проверяем текущую сортировку
  // и формируем соответствующий запрос
  if($_SESSION['ss']['sort'])
  {
    $sort = explode(':',$_SESSION['ss']['sort']);
    $cur_pole = $sort[0];
    $cur_sort = $sort[1];

    $query .= " ORDER BY {$cur_pole} ".($cur_sort=='up'?'DESC':'ASC');
  }
  else
    $query .= ' ORDER BY name,id';
  $query .= ' LIMIT '.($count_obj_on_page*$cur_page-$count_obj_on_page).",".$count_obj_on_page;
  //-----------------------------
  //echo $query;

  ob_start();

  show_filters($script);
  show_navigate_pages($kol_str,$cur_page,$script);

  ?>
  <style>
    .articul {font-size:11px; color:#a0a0a0;}
    .articul span {color:#f60;}
    td.glink, td.glink * { font-size:10px;}
    td.matrixA { background-color:#cbffa9 !important;}
    td.matrixB { background-color:#fff9a9;}
    td.matrixC { background-color:#ffd7c6;}
  </style>

  <table class="filter_tab" style="margin:5px 0 0 0;">
    <tr>
      <td align="left">Рубрика <?=help('отображаются объекты выбранной рубрики<br>(вместе с объектами подчинённых рубрик)')?></td>
      <td colspan="2"><?=dllTree("SELECT * FROM {$prx}catalog ORDER BY sort,id",'style="width:100%" onChange="RegSessionSort(\''.$script.'\',\'catalog=\'+this.value);return false;"',$f_catalog,array('remove'=>'-- все --'))?></td>
    </tr>
    <tr>
      <td align="left">Матрица</td>
      <td colspan="2"><?=dllEnum($tbl,'matrix','style="width:100%" onChange="RegSessionSort(\''.$script.'\',\'matrix=\'+this.value);return false;"',$f_matrix,array('remove'=>'-- все --'))?></td>
    </tr>
    <tr>
      <td>контекстный поиск</td>
      <td><input type="text" id="searchTxt" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
      <td><a id="searchBtn" href="" class="link">найти</a></td>
    </tr>
  </table>

  <? if(!$sitemap){ ?>
  <div style="padding:5px 0 0 0;">Отобразить <a href="" style="color:#F60" onclick="RegSessionSort('<?=$script?>','sitemap');return false;">Sitemap поля</a></div>
  <div class="clear"></div>
  <? } ?>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
    <input type="hidden" id="cur_id" value="<?=@$_GET['id']?@(int)$_GET['id']:""?>" />
    <table width="100%" border="1" cellspacing="0" cellpadding="0" class="tab1">
      <tr>
        <th><input type="checkbox" name="check_del" id="check_del" /></th>
        <th>№</th>
        <th><img src="img/image.png" title="изображение" /></th>
        <th nowrap style="width:<?=$f_catalog?'100':'50'?>%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Наименование','name')?></th>
        <? if($sitemap){?>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
        <? }?>
				<th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ссылка','link')?></th>
        <? if(!$f_catalog){ ?><th nowrap style="width:100%">Рубрика</th><? }?>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Матрица','matrix')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Новинка','is_new')?> <?=help('отображение товара в рубрике каталога «Новинки»')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Хит продаж','is_hit')?> <?=help('Отображение товара в рубрике каталога «Хиты продаж»')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Архив','is_archive')?> <?=help('архивный данный товар или нет')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?> <?=help('заблокирован данный товар или нет')?></th>
        <th style="padding:0 30px;"></th>
      </tr>
      <?
      $res = mysql_query($query);
      if(@mysql_num_rows($res))
      {
        $i=1;
        while($good = mysql_fetch_array($res))
        {
          $id = $good['id'];
          ?>
          <tr id="row<?=$id?>">
            <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>" /></th>
            <th><?=$i++?></th>
            <th style="padding:3px 5px;">
              <?
              $src = '/uploads/no_photo.jpg';
              $big_src = '/uploads/no_photo.jpg';
							if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/goods/{$id}.jpg")){
								$src = "/uploads/goods/45x45/{$id}.jpg";
								$big_src = "/uploads/goods/{$id}.jpg";
							}
              ?>
              <a href="<?=$big_src?>" class="highslide" onclick="return hs.expand(this)">
                <img src="<?=$src?>" align="absmiddle" height="45" />
              </a>
            </th>
            <td nowrap class="sp">
              <div class="articul">артикул <span><?=$good['articul']?></span></div>
							<a href="?red=<?=$id?>" class="link1"><?=$good['name']?></a>
						</td>
            <? if($sitemap){?>
              <th class="sitemap"><input type="text" class="datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($good['lastmod'])?date('d.m.Y',strtotime($good['lastmod'])):date("d.m.Y"))?>" /></th>
              <th class="sitemap"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$good['changefreq']?$good['changefreq']:'monthly')?></th>
              <th class="sitemap"><input type="text" name="priority[<?=$id?>]" value="<?=$good['priority']?$good['priority']:'0.5'?>" maxlength="3" style="text-align:center; width:30px;" /></th>
            <? }?>
						<td class="glink"><?
							if($good['url'] and $good['link'])
							{
								?><?=$good['url']?><a href="<?=$good['url']?><?=$good['link']?>.htm" target="_blank" title="<?=$good['url']?><?=$good['link']?>.htm" style="color:#090"><?=$good['link']?></a>.htm<?
							}
							?></td>
						<? if(!$f_catalog){ ?>
							<td nowrap><?
							$tree = '';
							if($good['id_catalog'])
							{
								$ids_catalog = getArrParents("SELECT id,id_parent FROM {$prx}catalog WHERE id='%s'",$good['id_catalog']);
								$tree = '';
								foreach($ids_catalog as $id_catalog)
								{
									ob_start();
									?><a href="/catalog.php?red=<?=$id_catalog?>" style="color:#090"><?=gtv('catalog','name',$id_catalog)?></a><?
									$tree .= ($tree?' &raquo; ':'').ob_get_clean();
								}
								echo $tree;
							}
							?></td><?
						}?>
            <td align="center" class="matrix<?=$good['matrix']?>"><?=$good['matrix']?></td>
            <td align="center"><?=btn_flag($good['is_new'],$id,'action=is_new&id=')?></td>
            <td align="center"><?=btn_flag($good['is_hit'],$id,'action=is_hit&id=')?></td>
						<td align="center"><?=btn_flag($good['is_archive'],$id,'action=is_archive&id=')?></td>
            <td align="center"><?=btn_flag($good['status'],$id,'action=status&id=')?></td>
            <td nowrap align="center"><?=btn_edit($id)?></td>
          </tr>
        <?
        }
      }
      else
      {
        ?>
        <tr>
          <td colspan="100" align="center">
            по вашему запросу ничего не найдено. <?=help('нет ни одной записи отвечающей критериям вашего запроса,<br>возможно вы установили неверные фильтры')?>
          </td>
        </tr>
      <?
      }
      ?>
    </table>
  </form>
  <?
  $content = $subcontent.ob_get_clean();
}

require('tpl/tpl.php');