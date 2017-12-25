<?
require('inc/common.php');

$rubric = 'Слайдер';
$tbl = 'slider';

// -------------------СОХРАНЕНИЕ----------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);

			$set = "name='{$name}',
			        note=".($note ? "'{$note}'" : 'NULL').",
			        price='".(int)$price."',
			        link='{$link}',
			        status='{$status}'";

			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');
							

			foreach (array('left','right') as $type){

				// загружаем картинки левого и правого блока
				if($_FILES[$type]['name'])
				{
					$info = @getimagesize($_FILES[$type]['tmp_name']);
					if($info===false) errorAlert('Ошибка загрузки файла');

					// проверка размеров
					//if($info[0]!=958 && $info[1]!=350)
					//errorAlert('Нарушение требований к изображения!\n(см. примечание)');

					remove_img($id,$tbl.'/'.$type); // удаляем старую картинку

					$path = $_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$type}/{$id}.jpg";
					@move_uploaded_file($_FILES[$type]['tmp_name'],$path);
					@chmod($path,0644);
					resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$type}/{$id}.jpg",array('45','45'),$_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$type}/45x45/{$id}.jpg",1,'');
				}

      }

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- обновление статуса
		case 'status':
			update_flag($tbl,'status',$id);
		break;
		// ----------------- сортировка вниз
		case 'moveup':
			sort_moveup($tbl,$id);
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- сортировка вниз
		case 'movedown':
			sort_movedown($tbl,$id);
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление банера
		case 'del':
			update($tbl,'',$id);
			@unlink($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/left/{$id}.jpg");
			@unlink($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/right/{$id}.jpg");
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
			{
				update($tbl,'',$id);
				@unlink($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/left/{$id}.jpg");
				@unlink($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/right/{$id}.jpg");
			}
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление изображения
		case 'pic_del':
			remove_img($id,$tbl.'/'.$_GET['type']);
			?><script>top.location.href = '<?=$script?>?red=<?=$id?>'</script><?
			break;
	}
	exit;
}
// ------------------РЕДАКТИРОВАНИЕ--------------------
if(isset($_GET['red']))
{
	$id = (int)@$_GET['red'];
	
	$rubric .= ' &raquo; '.($id ? 'Редактирование' : 'Добавление');
	$page_title .= ' :: '.$rubric;
	
	$row = gtv($tbl,'*',$id);
	
	ob_start();
	?>
  <form id="frm_edit" action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <tr>
      <th class="tab_red_th"></th>
      <th>Название</th>
      <td><?=show_pole('text','name',htmlspecialchars($row['name']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Примечание</th>
      <td><?=show_pole('text','note',htmlspecialchars($row['note']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"><?=help('указывается если слайд с товаром')?></th>
      <th>Цена</th>
      <td><?=show_pole('text','price',$row['price'])?></td>
    </tr>
    <tr>
    	<th class="tab_red_th"></th>
      <th>Ссылка</th>
      <td><?=show_pole('text','link',$row['link'])?></td>
    </tr>
		<?=show_tr_img('left',"/uploads/{$tbl}/left/","{$id}.jpg",$script."?action=pic_del&id={$id}&type=left",'Изображение слева')?>
		<?=show_tr_img('right',"/uploads/{$tbl}/right/","{$id}.jpg",$script."?action=pic_del&id={$id}&type=right",'Изображение справа')?>
    <tr>
      <th class="tab_red_th"></th>
      <th>Статус</th>
      <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
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
	ob_start();
	
	$page_title .= ' :: '.$rubric; 
	$rubric .= ' &raquo; Общий список'; 
	
	$razdel = array('Добавить'=>'?red=0','Удалить'=>"javascript:multidel(document.red_frm,'check_del_','');");
	$subcontent = show_subcontent($razdel);
	
	?>
  <form action="?action=multidel" name="red_frm" method="post" enctype="multipart/form-data" style="margin:0;" target="ajax">
  <input type="hidden" id="cur_id" value="<?=@$_GET['id']?@(int)$_GET['id']:""?>" />
  <table width="100%" border="1" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" onclick="check_uncheck('check_del')" /></th>
      <th>№</th>
      <th>Фото слева</th>
      <th>Фото справа</th>
      <th width="50%">Название</th>
      <th width="50%">Примечание</th>
      <th>Цена</th>
      <th>Ссылка</th>
      <th nowrap>Статус</th>
      <th nowrap>Порядок <?=help('параметр с помощью которого можно изменить порядок вывода элемента в клиентской части сайта')?></th>
      <th style="padding:0 30px;"></th>
    </tr>
  <?
	$res = sql("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id");
	if(mysql_num_rows($res))
	{
		$i=1;
		while($row = mysql_fetch_array($res))
		{
			$id = $row['id'];
			?>
			<tr id="row<?=$row['id']?>">
			  <th><input type="checkbox" name="check_del_[<?=$row['id']?>]" id="check_del_<?=$row['id']?>" /></th>
			  <th nowrap><?=$i++?></th>
			  <td align="center"><a href="/<?=$tbl?>/left/<?=$id?>.jpg" class="highslide" onclick="return hs.expand(this)"><img src="/<?=$tbl?>/left/45x45/<?=$id?>.jpg"></a></td>
        <td align="center"><a href="/<?=$tbl?>/right/<?=$id?>.jpg" class="highslide" onclick="return hs.expand(this)"><img src="/<?=$tbl?>/right/45x45/<?=$id?>.jpg"></a></td>
        <td nowrap><a href="?red=<?=$id?>" class="link1"><?=$row['name']?></a></td>
        <td nowrap><?=$row['note']?></td>
        <td nowrap><?=number_format($row['price'],0,',',' ')?></td>
        <td nowrap><a href="<?=$row['link']?>" class="green_link" target="_blank"><?=$row['link']?></a></td>
        <td nowrap align="center"><?=btn_flag($row['status'],$row['id'],'action=status&id=')?></td>
        <td nowrap align='center'><?=btn_sort($id)?></td>
			  <td nowrap align="center"><?=btn_edit($row['id'])?></td>
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