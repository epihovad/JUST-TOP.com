<?
require('inc/common.php');

$rubric = 'Страницы';
$tbl = 'pages';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);
			
			if(!$name) errorAlert('необходимо указать название !');
				
			if($locked)
			{
				$set = "text=".($text?"'{$text}'":"NULL").",
								h1=".($h1?"'{$h1}'":"NULL").",
								title=".($title?"'{$title}'":"NULL").",
								keywords=".($keywords?"'{$keywords}'":"NULL").",
								description=".($description?"'{$description}'":"NULL");
				update($tbl,$set,$id);
				?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
				exit;
			}
			
			$updateLink = false;
			$where = $id ? " AND id<>'{$id}'" : '';
			
			if($type=='page')
			{
				if($link)
				{
					if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
						$updateLink = true;
				}
				else
				{
					$link = makeUrl($name);
					if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
						$updateLink = true;
				}
			}
			
			$set = "id_parent='{$id_parent}',
							name='{$name}',
							text=".($text?"'{$text}'":"NULL").",
							type='{$type}',
							main='{$main}',
							tmain='{$tmain}',
							bmain='{$bmain}',
							status='{$status}',
							h1=".($h1?"'{$h1}'":"NULL").",
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
			if(!$updateLink) $set .= ",link='{$link}'";
				
			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');
			
			if($updateLink)
				update($tbl,"link='".($link.'_'.$id)."'",$id);
			
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?		
			break;
		// ----------------- обновление в меню
		case 'main':
		case 'tmain':
		case 'bmain':
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- сортировка вверх
		case 'moveup':
			$id_parent = gtv($tbl,'id_parent',$id);
			sort_moveup($tbl,$id,"id_parent='{$id_parent}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- сортировка вниз
		case 'movedown':
			$id_parent = gtv($tbl,'id_parent',$id);
			sort_movedown($tbl,$id,"id_parent='{$id_parent}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление одной записи
		case 'del':
			if(gtv($tbl,'locked',$id))
				errorAlert("данная страница защищена от удаления!");
			else
				remove_page($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
				if(!gtv($tbl,'locked',$id))
					remove_page($id);
			?><script>top.location.href = '<?=$script?>'</script><?
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
	
	$row = gtv($tbl,'*',$id);
	$locked = $row['locked'];
	
	ob_start();
	?>
  <style>
    .for-page { display:<?=$row['type']=='link'?'none':'table-row'?>;}
  </style>
  <script>
    $(function () {
      $('select[name="type"]').change(function () {
        var val = $(this).find('option:selected').val();
        if(val=='link'){ $('.for-page').hide(); }
        else { $('.for-page').show(); }
      });
    })
  </script>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <input type="hidden" name="locked" value="<?=$locked?>" />
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <tr>
      <th class="tab_red_th"></th>
      <th>Подчинение</th>
      <td><?=dllTree("SELECT * FROM {$prx}{$tbl} ORDER BY sort,name",'name="id_parent" style="width:100%"',$row['id_parent'],array('0'=>'без подчинения'),$id)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Название</th>
      <td><?=show_pole('text','name',htmlspecialchars($row['name']),$locked)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"><?=help('при отсутствии значения в данном поле<br>ссылка формируется автоматически')?></th>
      <th>Ссылка</th>
      <td><?=show_pole('text','link',$row['link'],$locked)?></td>
    </tr>
    <tr class="for-page">
      <th class="tab_red_th"></th>
      <th>Текст</th>
      <td><?=showFck('text',$row['text'])?></td>
    </tr>
    <?
    if(!$locked)
    {
      ?>
      <tr>
        <th class="tab_red_th"></th>
        <th>Тип</th>
        <td><?=dll(array('page'=>'страница','link'=>'ссылка'),' name="type"',$row['type'])?></td>
      </tr>
      <tr>
        <th class="tab_red_th"><?=help('отображать объект в главном меню')?></th>
        <th>Главное меню</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="main"',$row['main'])?></td>
      </tr>
			<tr>
				<th class="tab_red_th"><?=help('отображать объект в нижнем меню (в футере)')?></th>
				<th>Верхнее меню</th>
				<td><?=dll(array('0'=>'нет','1'=>'да'),'name="tmain"',$row['tmain'])?></td>
			</tr>
      <tr>
        <th class="tab_red_th"><?=help('отображать объект в нижнем меню (в футере)')?></th>
        <th>Нижнее меню</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="bmain"',$row['bmain'])?></td>
      </tr>
      <tr>
        <th class="tab_red_th"></th>
        <th>Статус</th>
        <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
      </tr>
      <?
    }
    ?>
    <tr class="for-page">
      <th class="tab_red_th"><?=help('используется вместо названия в &lt;h1&gt;')?></th>
      <th>Заголовок</th>
      <td><?=show_pole('text','h1',htmlspecialchars($row['h1']))?></td>
    </tr>
    <tr class="for-page">
      <th class="tab_red_th"></th>
      <th>title</th>
      <td><?=show_pole('text','title',htmlspecialchars($row['title']))?></td>
    </tr>
    <tr class="for-page">
      <th class="tab_red_th"></th>
      <th>keywords</th>
      <td><?=show_pole('text','keywords',htmlspecialchars($row['keywords']))?></td>
    </tr>
    <tr class="for-page">
      <th class="tab_red_th"></th>
      <th>description</th>
      <td><?=show_pole('textarea','description',$row['description'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th></th>
      <td align="center">
        <input type="submit" value="<?=($id ? 'Сохранить' : 'Добавить')?>" class="but1" onclick="loader(true)" />&nbsp;
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
	$page_title .= ' :: '.$rubric;
	$rubric .= ' &raquo; Общий список'; 
	
	$razdel['Добавить'] = '?red=0';
	$razdel['Удалить'] = "javascript:multidel(document.red_frm,'check_del_','');";
	$subcontent = show_subcontent($razdel);
	
	$query = "SELECT * FROM {$prx}{$tbl}";

	ob_start();
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
		$query .= ' ORDER BY sort,id';
	//-----------------------------
	//echo $query;
	
	show_filters($script);
  ?>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table width="100%" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" /></th>
      <th>№</th>
      <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Название','name')?></th>
      <? if($sitemap){?>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
			<? }?>
      <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ссылка','link')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Тип','type')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Глав. меню','main')?> <?=help('отображать объект в главном меню')?></th>
			<th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Верх. меню','tmain')?> <?=help('отображать объект в верхнем меню')?></th>
			<th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ниж. меню','bmain')?> <?=help('отображать объект в нижнем меню')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
      <? if(!$_SESSION['ss']['sort']) { ?><th nowrap>Порядок <?=help('параметр с помощью которого можно изменить порядок вывода элемента в клиентской части сайта')?></th><? }?>
      <th style="padding:0 30px;"></th>
    </tr>
  <?
	$mas = getTree($query);
	if(sizeof($mas))
	{
		$i=1;
		foreach($mas as $vetka)
		{
			$row = $vetka['row'];
			$level = $vetka['level'];
			
			$id = $row['id'];
			$locked = $row['locked'];
			$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
			$prfx = $prefix===NULL ? getPrefix($level) : str_repeat($prefix, $level);
			
			?>
			<tr id="row<?=$id?>">
				<th><? if(!$locked){ ?><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>" /><? }?></th>
				<th nowrap><?=$i++?></th>
				<td><?=$prfx?><a href="?red=<?=$id?>" class="link1"><?=$row['name']?></a></td>
				<td><?=$row['type']=='page'?'/':''?><a href="<?=$link?>" style="color:#090" target="_blank"><?=$row['link']?></a><?=$row['type']=='page'?'.htm':''?></td>
				<td align="center"><?=$row['type']=='page'?'страница':'ссылка'?></td>
				<td align="center"><?=btn_flag($row['main'],$id,'action=main&id=',$locked)?></td>
				<td align="center"><?=btn_flag($row['tmain'],$id,'action=tmain&id=',$locked)?></td>
				<td align="center"><?=btn_flag($row['bmain'],$id,'action=bmain&id=',$locked)?></td>
				<td align="center"><?=btn_flag($row['status'],$id,'action=status&id=',$locked)?></td>
				<? if(!$_SESSION['ss']['sort']){ ?><td nowrap align="center"><?=btn_sort($id)?></td><? }?>
				<td nowrap align="center"><?=btn_edit($id,$locked)?></td>
			</tr>
			<?
		}
	}
	else
	{
		?>
    <tr>
      <td colspan="10" align="center">
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