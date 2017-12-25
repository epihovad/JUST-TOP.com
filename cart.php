<?
require('inc/common.php');
//unset($_SESSION['cart']);

if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		// -------------- ДОБАВЛЕНИЕ ТОВАРА В КОРЗИНУ
		case 'tocart':
		  //unset($_SESSION['cart']); exit;
			$id = (int)$_GET['mod'];
			$kol = (int)$_GET['quant'];
			$kol = $kol ? $kol : 1;

			$mod = getRow("SELECT * FROM {$prx}mods WHERE status = 1 AND id = '{$id}'");
			if(!$mod['id']) exit;
			$good = getRow("SELECT * FROM {$prx}goods WHERE status = 1 AND id = '{$mod['id_good']}'");
			if(!$good['id']) exit;

			$_SESSION['cart']['mods'][$id] = array(
			  'quant'     => $kol,
        'price'     => $mod['price'],
				'amount'    => $kol * $mod['price'],
        'good_name' => $good['name'],
        'mod_name'  => $mod['name'],
        'mods_type' => $good['mods_type']
      );
			$quant = 0;
			$total = 0;
			foreach ($_SESSION['cart']['mods'] as $arr) {
				$quant += $arr['quant'];
        $total += $arr['amount'];
			}

			$_SESSION['cart']['quant'] = $quant;
			$_SESSION['cart']['total'] = $total;

			?>
			<script>
        top.jQuery('.hcart .cnt').html('<?=$_SESSION['cart']['quant']?>');
				top.jQuery(document).jAlert('show','confirm',
					'Отлично! Товар практически у Вас в руках!<br>Осталось оформить покупку, что скажете?',
					function(){top.location.href='/cart.php'},
					{b_confirm : {b1:'Поехали!',b2:'Позже'}}
				);
				top.jQuery('.tocart').removeClass('disabled');
			</script>
			<?
			break;

		// -------------- ИЗМЕНЕНИЕ КОЛ-ВА/УДАЛЕНИЕ ТОВАРА В КОРЗИНЕ
    case 'change':
      $remove_mod_id = (int)$_GET['mod'];
			if($remove_mod_id){
        unset($_SESSION['cart']['mods'][$remove_mod_id]);
      }

			$quant = 0;
			$total = 0;
      foreach ($_POST['quant'] as $mod_id=>$mod_quant){
				$_SESSION['cart']['mods'][$mod_id]['quant'] = $mod_quant;
				$_SESSION['cart']['mods'][$mod_id]['amount'] = $mod_quant * $_SESSION['cart']['mods'][$mod_id]['price'];
        $quant += $mod_quant;
				$total += $_SESSION['cart']['mods'][$mod_id]['amount'];
			}

			if(!$quant){
        unset($_SESSION['cart']);
        ?><script>top.location.href = '/cart.php'</script><?
        exit;
      }

			$_SESSION['cart']['quant'] = $quant;
			$_SESSION['cart']['total'] = $total;

			$dtype = $_POST['dtype'];
			$ptype = $_POST['ptype'];
			$itogo = $_SESSION['cart']['total'];

			if(!$dtype) $itogo += 350;
			if($dtype == 1) $itogo += 650;
			if($ptype == 1) $itogo = round($itogo/2);

			ob_start();
			$i=1;
			foreach ($_SESSION['cart']['mods'] as $mod){
				?><div><b><?=$i++?></b>. <?=$mod['good_name']?> (<?=$mod['mods_type']?>: <?=$mod['mod_name']?>) &mdash; <span class="price"><?=$mod['quant']?></span> <span class="rub">шт.</span> <span class="price"><?=$mod['amount']?></span> <span class="rub">руб</span></div><?
			}
			$order_list = ob_get_clean();

      ?>
      <script>
        top.jQuery('.hcart .cnt').html('<?=$_SESSION['cart']['quant']?>');
        top.jQuery('tr.total').find('td').eq(0).html('<?=number_format($_SESSION['cart']['total'],0,',',' ')?> <span class="rub">руб</span>');
        top.jQuery('#order-list .td').html('<?=cleanJS($order_list)?>');
        top.jQuery('#order-tatal .itogo').html('<?=number_format($itogo,0,',',' ')?> <span class="rub">руб</span>');
        top.jQuery('#order .row').removeClass('wait');
      </script>
      <?
      break;

		// -------------- СМЕНА ПАРАМЕТРОВ ЗАКАЗА
    case 'total':
      switch ((int)$_GET['step'])
      {
				// контактная информация
				case 3:
				  ob_start();
				  ?>
          <div class="cell th">Контактная информация:</div>
          <div class="cell td">
            <b>Имя</b>: <?=$_POST['user']['name']?>;<br>
            <b>E-mail</b>: <?=$_POST['user']['email']?>;<br>
            <b>Телефон</b>: <?=$_POST['user']['phone']?>;<br>
            <b>Адрес доставки</b>: <?=$_POST['user']['address']?>;<br>
            <b>Почтовый индекс</b>: <?=$_POST['user']['index']?>
          </div>
          <?
				  $data = ob_get_clean();
				  ?>
          <script>
            top.jQuery('#order-contacts').html('<?=cleanJS($data)?>');
            top.jQuery('#order .row').removeClass('wait');
          </script>
          <?
					break;
				// способ доставки
				case 4:
				  $dtype = $_GET['dtype'];
					$ptype = $_GET['ptype'];

					ob_start();
          ?><div class="cell th">Способ доставки:</div><?
          switch ($dtype){
            default:  ?><div class="cell td">Доставка «Почта России» &mdash; <span class="price">350</span> <span class="rub">руб</span></div><? break;
            case '1': ?><div class="cell td">Курьерская Доставка EMS &mdash; <span class="price">650</span> <span class="rub">руб</span></div><? break;
						case '2': ?><div class="cell td">Доставка транспортной компанией &mdash; <span class="cmt">доставки оплачивается отдельно</span></div><? break;
          }
					$data = ob_get_clean();

					$itogo = $_SESSION['cart']['total'];
					if(!$dtype) $itogo += 350;
					if($dtype == 1) $itogo += 650;
					if($ptype == 1) $itogo = round($itogo/2);

					?>
          <script>
            top.jQuery('#order-delivery').html('<?=cleanJS($data)?>');
            <? if($dtype){ ?>
              top.jQuery('#order-payment').html('<div class="cell th">Способ доставки:</div><div class="cell td">100% предоплата &mdash; <span class="cmt">реквизиты для перевода мы отправим Вам после оформления заказа</span></div>');
            <?}?>
            top.jQuery('#order-tatal .itogo').html('<?=number_format($itogo,0,',',' ')?> <span class="rub">руб</span>');
            top.jQuery('#order .row').removeClass('wait');
          </script>
					<?
					break;
				// способ оплаты
				case 5:
					$ptype = $_GET['ptype'];
					$dtype = $_GET['dtype'];

					ob_start();
					?><div class="cell th">Способ оплаты:</div><?
					switch ($ptype){
						default:  ?><div class="cell td">100% предоплата &mdash; <span class="cmt">реквизиты для перевода мы отправим Вам после оформления заказа</span></div><? break;
						case '1': ?><div class="cell td">50% предоплата &mdash; <span class="cmt">реквизиты для перевода мы отправим Вам после оформления заказа</span></div><? break;
						case '2': ?><div class="cell td">Оплата наличными &mdash; <span class="cmt">оплата производится непосредственно в момент получения заказа</span></div><? break;
					}
					$data = ob_get_clean();

					$itogo = $_SESSION['cart']['total'];
					if(!$dtype) $itogo += 350;
					if($dtype == 1) $itogo += 650;
					if($ptype == 1) $itogo = round($itogo/2);

					?>
          <script>
            top.jQuery('#order-payment').html('<?=cleanJS($data)?>');
            top.jQuery('#order-tatal .itogo').html('<?=number_format($itogo,0,',',' ')?> <span class="rub">руб</span>');
            top.jQuery('#order .row').removeClass('wait');
          </script>
					<?
					break;
      }

      break;

		// -------------- СОХРАНЕНИЕ ЗАКАЗА
		case 'save':
			//jAlert('сохраняем заказ');
			pre($_POST);
			break;

		// -------------- ОПЛАТА ЗАКАЗА ПО БЕЗНАЛУ
		case 'pay':
			if(!$id = (int)$_GET['id']) exit;
			if(!$order = gtv('orders','id,id_restaurant,cost,date',$id)) exit;
			if(!$order['cost'] || (!$_SESSION['user'] && !$_SESSION['splinks']['orderpay']['user'])) exit;

			$pay = (float)str_replace(',','.',$order['cost']);
			$id = update('restaurant_pay', "date=NOW(), id_restaurant='{$order['id_restaurant']}', id_orders='{$order['id']}', pay='{$pay}', text='оплата заказа', status='0'");
			if(!$id) exit;
			?>
			<form method="POST" action="https://merchant.roboxchange.com/Index.aspx" id="frmPay" target="_top">
			<!--form method="POST" action="http://test.robokassa.ru/Index.aspx" id="frmPay" target="_top"-->
				<input type="hidden" name="MrchLogin" value="<?=$robokassa['login']?>">
				<input type="hidden" name="OutSum" value="<?=$pay?>">
				<input type="hidden" name="InvId" value="<?=$id?>">
				<textarea name="Desc">Оплата заказа №<?=$order['id']?> от <?=date('d.m.Y H:i', strtotime($order['date']))?></textarea>
				<input type="hidden" name="ShpTypePay" value="order">
				<input type="hidden" name="SignatureValue" value="<?=md5("{$robokassa['login']}:{$pay}:{$id}:{$robokassa['pwd1']}:ShpTypePay=order")?>">
			</form>
			<script>document.getElementById('frmPay').submit();</script>
			<?
			break;
	}
	exit;
}

ob_start();
?><link type="text/css" rel="stylesheet" href="/css/cart.css" /><?
$const['css_links'] = ob_get_clean();

ob_start();
?>
	<script src="/js/jquery/inputmask.min.js"></script>
	<script src="/js/jquery/inputmask.phone.extensions.min.js"></script>
  <?/*<script src="/js/jquery/form.min.js"></script>*/?>
  <script src="/js/cart.js"></script>
<?
$const['js_links'] = ob_get_clean();

// ------------------ПРОСМОТР---------------------
ob_start();

switch(@$_GET['show'])
{
	// ----------------- ОФОРМЛЕНИЕ ЗАКАЗА
	default:

		if(!$_SESSION['cart']){
			header('Location: /cart.php?show=empty');
			exit;
		}

		?>
		<div class="pd25">

      <form id="frm-order" action="/cart.php?action=save" class="frm" target="ajax" method="post">

        <h1>Оформление заказа</h1>
        <div class="step">
          <div class="step-num">1</div>
          <div class="step-head">Информация о заказе:</div>
          <div class="step-right">
            <div class="step-note-im"><img src="/img/cart-step-info.png" width="48"></div>
            <div class="step-note content">
              <p>Здесь Вам предлагается проверить список своих покупок.</p>
              <p>Возможно Вы что-нибудь забыли или желаете получить хорошую скидку, воспользовавшись специальными акциями, которые приятно повлияют на итоговую стоимость всего заказа:</p>
              <ul>
                <li>скидка на весь заказ, при наборе товара на определённую сумму;</li>
                <li>скидка на основной товар, при условии приобретения сопутствующего товара;</li>
              </ul>
            </div>
          </div>
          <div class="step-left">
            <div class="cart">
              <table>
                <thead>
                  <tr>
                    <th class="c1">№</th>
                    <th class="c2">Модель/Цвет</th>
                    <th class="c3">Наименование товара</th>
                    <th class="c4">Цена</th>
                    <th class="c5">Кол-во</th>
                    <th class="c6">Стоимость</th>
                    <th class="c7">Скидка</th>
                    <th class="c8">Итоговая стоимость</th>
                    <th class="cell-fake"></th>
                  </tr>
                </thead>
                <tbody>
                <?
                $ids_mods = implode(',', array_keys($_SESSION['cart']['mods']));
                //$mods = array();
                $r = sql("SELECT * FROM {$prx}mods WHERE id IN ({$ids_mods})");
                $i=1;
                while ($mod = mysql_fetch_assoc($r)){
                  //$mods[$mod['id']] = $mod;
                  $good = gtv('goods','*',$mod['id_good']);
                  $quant = $_SESSION['cart']['mods'][$mod['id']]['quant'];
                  $start_price = $mod['old_price'] ? $mod['old_price'] : $mod['price'];
                  ?>
                  <tr mod="<?=$mod['id']?>" class="row-mod">
                    <td class="c1"><?=$i++?></td>
                    <td class="c2"><a href="/mods/<?=$mod['id']?>.jpg" rel="nofollow" title="<?=htmlspecialchars($good['name'].' ('.$good['mods_type'].': '.$mod['name'].')')?>"><img src="/mods/45x45/<?=$mod['id']?>.jpg"></a></td>
                    <td class="c3">Экшн-камера SJCAM SJ4000 WiFi<div><?=$good['mods_type']?>: <span><?=$mod['name']?></span></div></td>
                    <td class="c4"><?=number_format($start_price,0,',',' ')?> <span class="rub">руб</span></td>
                    <td class="c5"><?=chQuant('quant['.$mod['id'].']', $quant)?></td>
                    <td class="c6"><?=number_format($start_price*$quant,0,',',' ')?> <span class="rub">руб</span></td>
                    <td class="c7"><?=number_format(($start_price-$mod['price'])*$quant,0,',',' ')?> <span class="rub">руб</span></td>
                    <td class="c8"><?=number_format($mod['price']*$quant,0,',',' ')?> <span class="rub">руб</span></td>
                    <td class="c9"><div class="gdel"></div></td>
                  </tr>
                  <?
                }
                //cartGift();
                ?>
                </tbody>
                <tfoot>
                  <?/*<tr class="coupon">
                    <th colspan="7">Скидочный купон:</th>
                    <td><input type="text" name="coupon" value="" class="form-control mini" placeholder="Номер" maxlength="7"></td>
                    <td class="cell-fake"></td>
                  </tr>*/?>
                  <tr class="total">
                    <th colspan="7">Итого:</th>
                    <td><?=number_format($_SESSION['cart']['total'],0,',',' ')?> <span class="rub">руб</span></td>
                    <td class="cell-fake"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div class="clear"></div>
        </div>

        <div class="step">
          <div class="step-left">
            <div class="step-num">2</div>
            <div class="step-head">Выбор подарочного сувенира:</div>
          </div>
          <div class="step-right">
            <div class="step-note-im"><img src="/img/cart-step-info.png" width="48"></div>
            <div class="step-note content">
              <p>В качестве бесплатного приложения к Вашей покупке, мы предлагаем выбрать один из предложенных вариантов.</p>
            </div>
          </div>
          <div class="tile stype">
            <input type="hidden" name="stype" value="0">
            <div class="ch"></div>
            <center>
              <div class="item active">
                <div class="im"><div><img src="/img/dtype-post.gif"></div></div>
              </div>
              <div class="item">
                <div class="im"><div><img src="/img/dtype-ems.gif"></div></div>
              </div>
              <div class="item">
                <div class="im"><div><img src="/img/dtype-tc.gif"></div></div>
              </div>
            </center>
          </div>
          <div class="clear"></div>
        </div>

        <div class="step">
          <div class="step-left">
            <div class="step-num">3</div>
            <div class="step-head">Способ доставки:</div>
          </div>
          <div class="step-right">
            <div class="step-note-im"><img src="/img/cart-step-info.png" width="48"></div>
            <div class="step-note content">
              <p>Способ доставки, как и способ оплаты, тоже имеет несколько вариантов:</p>
              <ul>
                <li><b>доставка «Почта России»</b> - отправка посылки почтой России за счет получателя (Вас), или наложенным платежом другими словами;</li>
                <li><b>курьерская доставка EMS</b> - это услуга экспресс-доставки писем и посылок. Отправления производятся во все города России и за рубеж в кратчайшие сроки. Одна из главных особенностей EMS – вручение отправлений непосредственно в руки адресату (Вам);</li>
                <li><b>доставка транспортной компанией «СДЭК»</b> - мы сдаём груз в своём городе, указываем в какой город доставить груз, и кому его отдать. Вы получаете груз в своём городе в пункте выдачи транспортной компании по паспорту.</li>
              </ul>
            </div>
          </div>
          <div class="tile dtype">
            <input type="hidden" name="dtype" value="0">
            <div class="ch"></div>
            <center>
              <div class="item active">
                <div class="im"><div><img src="/img/dtype-post.gif"></div></div>
              </div>
              <div class="item">
                <div class="im"><div><img src="/img/dtype-ems.gif"></div></div>
              </div>
              <div class="item<?/* blocked*/?>">
                <div class="im"><div><img src="/img/dtype-tc.gif"></div></div>
              </div>
            </center>
          </div>
          <div class="clear"></div>
        </div>

        <div class="step">
          <div class="step-left">
            <div class="step-num">4</div>
            <div class="step-head">Контактная информация:</div>
          </div>
          <div class="step-right">
            <div class="step-note-im"><img src="/img/cart-step-info.png" width="48"></div>
            <div class="step-note content">
              <p>Личная информация о наших клиентах строго конфиденциальна!</p>
              <p>Нам нужны Ваши данные для того, чтобы Вы всегда могли получать такие важные уведомления как:</p>
              <ul>
                <li>уточнения по заказу;</li>
                <li>статус заказа;</li>
                <li>информация о горячих новинках;</li>
                <li>информация об актуальных акциях.</li>
              </ul>
              <p>СПАМ мы не рассылаем!<br>После регистрации Вы можете в личном кабинете выполнить настройку уведомлений.</p>
            </div>
          </div>
          <div class="step-left">
            <div class="user-data">
              <div class="fld">
                <label>Ваше ФИО:</label><input type="text" name="user[name]" value="" class="form-control" placeholder="ФИО указывается полностью">
                <span>ФИО человека, который будет забирать посылку</span>
              </div>
              <div class="fld"><label>Ваш E-mail:</label><input type="text" name="user[email]" value="" class="form-control" placeholder="Адрес почтового ящика"></div>
              <div class="fld"><label>Ваш телефон:</label><input type="text" name="user[phone]" value="" class="form-control" placeholder="+7 (___) ___-__-__"></div>
              <div class="fld"><label>Ваш адрес:</label><input type="text" name="user[address]" value="" class="form-control" placeholder="Адрес доставки"></div>
              <div class="fld">
                <label>Индекс:</label><input type="text" name="user[index]" value="" class="form-control" placeholder="Почтовый индекс">
                <span>индекс почтового отделения, где Вы будете забирать посылку</span>
              </div>
              <div class="fld">
                <label>Ваш пароль:</label><input type="password" name="user[pwd]" value="" class="form-control" placeholder="минимум 6 символов">
                <span>необходим для входа в личный кабинет</span>
              </div>
              <div class="fld"><label>Повторите пароль:</label><input type="password" name="user[pwd-retry]" value="" class="form-control"></div>
              <div class="fld"><label>Примечание к заказу:</label><textarea name="user[notes]" class="form-control" rows="5" placeholder="Ваш комментарий к заказу"></textarea></div>
            </div>
          </div>
          <div class="clear"></div>
        </div>

        <div class="step">
          <div class="step-left">
            <div class="step-num">5</div>
            <div class="step-head">Способ оплаты:</div>
          </div>
          <div class="step-right">
            <div class="step-note-im"><img src="/img/cart-step-info.png" width="48"></div>
            <div class="step-note content">
              <p>Мы может предложить Вам очень гибкую систему оплаты Вашей покупки:</p>
              <ul>
                <li><b>100% предоплата</b> - в этом случае, мы подарим Вам дополнительную скидку 5% на весь заказ;</li>
                <li><b>частичная предоплата в размере 50%</b> - часто этим способом пользуются клиенты, у которых в данный момент возникли временные финансовые трудности.
                  Оставшаяся стоимость заказа оплачивается в момент получения посылки;</li>
                <li><b>постоплата</b> - сейчас Вы ничего не платите, оплата производится непосредственно при получении посылки
                  либо в Вашем почтовом отделении (наложенный платеж при доставке «Почтой России»), либо курьеру, в случае EMS доставки.</li>
              </ul>
            </div>
          </div>
          <div class="tile ptype">
            <input type="hidden" name="ptype" value="0">
            <div class="ch"></div>
            <center>
              <div class="item active">
                <div class="im"><div><img src="/img/ptype-100.gif"></div></div>
              </div>
              <div class="item">
                <div class="im"><div><img src="/img/ptype-50.gif"></div></div>
              </div>
              <div class="item">
                <div class="im"><div><img src="/img/ptype-cash.gif"></div></div>
              </div>
            </center>
          </div>
          <div class="clear"></div>
        </div>

        <div id="order" class="step">
          <h2>Пожалуйста, ещё раз внимательно проверьте все параметры заказа:</h2>
          <?/*<img class="loader" src="/img/loader.svg">*/?>
          <div class="info">
            <div id="order-list" class="row">
              <div class="cell th">Список покупок:</div>
              <div class="cell td">
                <?
                $i=1;
                foreach ($_SESSION['cart']['mods'] as $mod){
									?><div><b><?=$i++?></b>. <?=$mod['good_name']?> (<?=$mod['mods_type']?>: <?=$mod['mod_name']?>) &mdash; <span class="price"><?=$mod['quant']?></span> <span class="rub">шт.</span> <span class="price"><?=$mod['amount']?></span> <span class="rub">руб</span></div><?
                }
                ?>
              </div>
            </div>
            <div id="order-contacts" class="row">
              <div class="cell th">Контактная информация:</div>
              <div class="cell td"><b>ФИО</b>: <br><b>E-mail</b>: <br><b>Телефон</b>: <br><b>Адрес доставки</b>: <br><b>Почтовый индекс</b>: </div>
            </div>
            <div id="order-delivery" class="row">
              <div class="cell th">Способ доставки:</div>
              <div class="cell td">Доставка «Почта России» &mdash; <span class="price">350</span> <span class="rub">руб</span></div>
            </div>
            <div id="order-payment" class="row">
              <div class="cell th">Способ оплаты:</div>
              <div class="cell td">100% предоплата</div>
            </div>
            <div id="order-tatal" class="row">
              <div class="cell th">Итоговая стоимость заказа:</div>
              <div class="cell td itogo"><?=number_format($_SESSION['cart']['total']+350,0,',',' ')?> <span class="rub">руб</span></div>
            </div>
          </div>
        </div>

        <div style="text-align:center; padding-top:20px;">
          <div><a href="" rel="nofollow" class="btn tocart btn-big">Подтверждаю заказ</a></div>
          <div class="callme">
            <input type="hidden" name="callme" value="1">
            <span>после оформления заказа наш менеджер свяжется с Вам для уточнения информации</span>
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle btn-mini" data-toggle="dropdown"><b>да</b><span class="caret"></span></button>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">да, так надёжнее</a></li>
                <li><a href="#">нет, не нужно</a></li>
              </ul>
            </div>
          </div>
          <div class="confirm">Нажимая кнопку «Подтверждаю заказ», я соглашаюсь на получение информации от интернет-магазина и уведомлений о состоянии моих заказов, а также принимаю условия <a href="">политики конфиденциальности</a> и <a href="">пользовательского соглашения</a>.</div>
        </div>

      </form>

		</div>
		<?

		break;
	// ----------------- КОРЗИНА ПУСТА
	case 'empty':
		?>пусто<?
		break;
	// ----------------- РЕЗУЛЬТАТ ЗАКАЗА
	case 'res':
		break;
}

$content = ob_get_clean();
require("tpl/tpl.php");