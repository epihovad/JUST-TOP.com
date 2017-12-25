<?
@session_start();

require($_SERVER['DOCUMENT_ROOT'].'/inc/db.php');
require($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php');
require($_SERVER['DOCUMENT_ROOT'].'/inc/spec.php');

if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		// ------------------- Заказ обратного звонка + Заказ в 1 клик
		case 'call':
			foreach($_REQUEST as $k=>$v)
				$$k = clean($v);

			if($hdn) exit; // спам-боты

			if($phone){
				$phone = preg_replace("/\D/",'',$phone);
				$phone = substr($phone, -10);
				if(strlen($phone)!=10)
					jAlert('Введен некорректный номер телефона');
			} else {
				jAlert('Пожалуйста, укажите Ваш номер телефона');
			}
			if(!$name) jAlert('Пожалуйста, представьтесь');

			$good = array();
			if($mod){
				$q = "SELECT 	g.id,
                      CONCAT(g.name,' (',g.mods_type,': ',m.name,') - ',m.price,' руб.') AS info
              FROM {$prx}goods g
              JOIN {$prx}mods m ON m.id_good = g.id
              WHERE 1=1
                    AND g.status = 1
                    AND m.id = {$mod}";
				$good = getRow($q);
      }

      $type = $good ? 'Заказ в 1 клик' : 'Звонок';

			$mailto = array();

			ob_start();
			?><b>Имя</b>: <?=$name?><br /><b>Телефон</b>: <?=$phone?><?
      if($good){
        $lnk = '<a href="http://'.$_SERVER['HTTP_HOST'].'/admin/goods.php?red='.$good['id'].'" target="_blank">'.$good['info'].'</a>';
        ?><br /><b>Товар</b>: <?=$lnk?><?
      }
			$mailto['text'] = ob_get_clean();

			$set = "type='{$type}', name='{$name}', phone='{$phone}'";
			if($good){
			  $set .= ", text = '".clean($lnk)."'";
      }

			if(!update('msg',$set)){
				$alert = 'Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.';
				$mailto['theme'] = "Ошибка ({$type})";
			}
			else
			{
				$alert = 'Спасибо за Ваше обращение.<br>Заявка уже отправлена нашему менеджеру,<br>скоро он с Вами свяжется'; //nl2br(set('msg-success'));
				$mailto['theme'] = $type;
			}

			// мылим админу
			//mailTo(set('admin_mail'), $mailto['theme'], $mailto['text'], set('admin_mail'));

			?><script>top.jQuery(document).jAlert('show','alert','<?=cleanJS($alert)?>',function(){top.jQuery.arcticmodal('close')});</script><?
			break;
		// ------------------- Форма обратной связи
		case 'feedback':
			foreach($_REQUEST as $k=>$v)
				$$k = clean($v);

			if($hdn) exit; // спам-боты

			if(!$name) jAlert('Пожалуйста, представьтесь');
			if(!check_mail($mail)) jAlert('Введен некорректный Email');
			if(!$text) jAlert('Пожалуйста, введите Ваше сообщение');

			$mailto = array();

			ob_start();
			?>
      <b>Имя</b>: <?=$name?><br />
      <b>Email</b>: <?=$mail?><br />
      <b>Сообщение</b>: <?=$text?><br />
			<?
			$mailto['text'] = ob_get_clean();

			$set = "type='Сообщение', name='{$name}', mail='{$mail}', text='{$text}'";

			if(!update('msg', $set)){
				$alert = 'Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.';
				$mailto['theme'] = 'Ошибка (Сообщение)';
			}
			else
			{
				$alert = 'Спасибо за Ваше обращение.<br>Сообщение уже отправлено нашему менеджеру';
				$mailto['theme'] = 'Сообщение';
			}

			// мылим админу
			//mailTo(set('admin_mail'), $mailto['theme'], $mailto['text'], set('admin_mail'));

			?><script>top.jQuery(document).jAlert('show','alert','<?=cleanJS($alert)?>',function(){top.jQuery.arcticmodal('close')});</script><?
			break;
		// ------------------- Подписка на рассылку
		case 'subscribe':
			foreach($_REQUEST as $k=>$v)
				$$k = clean($v);

			if($hdn) exit; // спам-боты

			if(!check_mail($mail)) jAlert('Введен некорректный Email');

			// проверка подписан ли уже email
			if($subs = getRow("SELECT * FROM {$prx}subscribers WHERE mail = '{$mail}'")){

				if(!$subs['unsubscribe_date']){
					$alert = 'Вы уже подписаны на нашу рассылку';
				} else {
					if(!update('subscribers',"unsubscribe_date = NULL", $subs['id'])){
						$alert = 'Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.';
					} else {
						$alert = 'Вы успешно подписаны на рассылку.<br>Благодарим за проявленный интерес.<br>Вы не пожалеете!';
					}
				}
			} else {

				if(!update('subscribers',"mail = '{$mail}'")){
					$alert = 'Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.';
				} else {
					$alert = 'Вы успешно подписаны на рассылку.<br>Благодарим за проявленный интерес.<br>Вы не пожалеете!';
				}
			}

			// мылим админу
			//mailTo(set('admin_mail'), 'Подписка', 'У нас новый подписчик:<br>'.$mail, set('admin_mail'));

			?><script>top.jQuery(document).jAlert('show','alert','<?=cleanJS($alert)?>',function(){top.jQuery('.Subscribe input').val('')});</script><?
			break;
		// ------------------- Авторизация
		case 'auth':
			$alert = 'В данный момент на сайте ведутся технические работы.<br>Администрация сайта приносит Вам свои извинения за неудобства.';
			?><script>top.jQuery(document).jAlert('show','alert','<?=cleanJS($alert)?>',function(){top.jQuery('.Subscribe input').val('')});</script><?
			break;
	}
	exit;
}

if(isset($_GET['show']))
{
	switch($_GET['show'])
  {
		// ------------------- Заказ обратного звонка + Заказ в 1 клик
		case 'call':
		  $mod = (int)$_GET['mod'];
			?>
			<style>
				#frm-call {text-align:center; width:385px;}
				#frm-call h3 {margin: 0px 0px 20px;}
				#frm-call .form-control { width:100%;}
			</style>

			<form id="frm-call" action="/inc/actions.php?action=call" class="frm" target="ajax" method="post">
				<h3><?=$mod?'Быстрый заказ в 1 клик':'Заказ обратного звонка'?></h3>
				<div class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-phone-alt"></span></span>
					<input class="form-control" placeholder="+7 (___) ___-__-__" name="phone" type="text">
				</div>
				<div class="clear" style="padding-bottom:10px;"></div>
				<div class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
					<input class="form-control" placeholder="Как к Вам обращаться?" name="name" type="text">
				</div>
				<div class="clear" style="padding-top:15px;"></div>
				<div class="btn btn-mini"><div>Подтвердить</div></div>
        <div class="hdn">
          <input type="text" name="hdn" value="">
          <input type="text" name="mod" value="<?=$mod?>">
        </div>
			</form>

			<script src="/js/jquery/inputmask.min.js"></script>
			<script src="/js/jquery/inputmask.phone.extensions.min.js"></script>
			<script>
				jQuery(document).ready(function( $ ) {
					Inputmask({mask: '+7 (999) 999-99-99',showMaskOnHover: false}).mask($('#frm-call input[name="phone"]'));
					$('#frm-call .btn-mini').click(function () {
						jQuery('#frm-call').submit();
					});
				});
			</script>
			<?
			break;
		// ------------------- Форма обратной связи
		case 'feedback':
			?>
			<style>
				#frm-feedback {text-align:center; width:385px;}
				#frm-feedback h3 {margin: 0px 0px 20px;}
				#frm-feedback .form-control { width:100%;}
				#frm-feedback textarea { resize:none; font:16px 'Source Sans Pro', sans-serif;}
			</style>

			<form id="frm-feedback" action="/inc/actions.php?action=feedback" class="frm" target="ajax" method="post">
				<h3>Оставьте нам сообщение</h3>
				<div class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
					<input class="form-control" placeholder="Как к Вам обращаться?" name="name" type="text">
				</div>
				<div class="clear" style="padding-bottom:10px;"></div>
				<div class="input-group">
					<span class="input-group-addon">@</span>
					<input class="form-control" placeholder="Введите Ваш Email адрес" name="mail" type="text">
				</div>
				<div class="clear" style="padding-bottom:10px;"></div>
				<div class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
					<textarea class="form-control" placeholder="Введите Ваше сообщение" rows="4" name="text"></textarea>
				</div>
				<div class="clear" style="padding-top:15px;"></div>
				<div class="btn btn-mini" onclick="jQuery('#frm-feedback').submit();"><div>Отправить</div></div>
        <div class="hdn"><input type="text" name="hdn" value=""></div>
			</form>
			<?
			break;
		// ------------------- Авторизация
		case 'auth':
			?>
			<style>
				#frm-auth {text-align:right; width:385px;}
				#frm-auth h3 {margin: 0px 0px 20px; text-align:center;}
				#frm-auth .form-control { width:100%;}
				#frm-auth .forgot { padding-right:15px;}
				#frm-auth .notes { border-top: 1px solid #ccc; font-size: 12px; font-style: italic; margin-top: 15px; padding: 5px 0 0; text-align: right;}
			</style>

			<form id="frm-auth" action="/inc/actions.php?action=auth" class="frm" target="ajax" method="post">
				<h3>Вход в личный кабинет</h3>
				<div class="input-group">
					<span class="input-group-addon">@</span>
					<input class="form-control" placeholder="Введите Ваш Email адрес" name="mail" type="text">
				</div>
				<div class="clear" style="padding-bottom:10px;"></div>
				<div class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
					<input class="form-control" placeholder="Введите Ваш пароль" name="pass" type="password">
				</div>
				<div class="clear" style="padding-top:15px;"></div>
				<?/*<div class="forgot" style="display:inline-block;"><a href="">Забыли пароль?</a></div>*/?>
				<div class="btn btn-mini" onclick="jQuery('#frm-auth').submit();"><div>Войти</div></div>
				<div class="notes">Только наши покупатели могут войти в свой личный кабинет</div>
        <div class="hdn"><input type="text" name="hdn" value=""></div>
			</form>
			<?
			break;
	}
	exit;
}