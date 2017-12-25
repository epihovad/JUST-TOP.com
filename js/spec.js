var zmax = 1001; // стартовая переменная величина

jQuery.browser = {};
jQuery.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
jQuery.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
jQuery.browser.iDevice = /ip(hone|od|ad)/i.test(navigator.userAgent || navigator.vendor || window.opera);

jQuery(document).ready(function( $ ) {
	// blur эффект при наведении
  /*if(!$.browser.iDevice){
    $('#logo a').hover(
      function(){ $('.Center, .Footer').addClass('blured'); },
      function(){ $('.Center, .Footer').removeClass('blured'); }
    );
  }*/
  var y = $(document).scrollTop()*1;
  var H = $(document).innerHeight();
  var w = $('body').width();
	//
	$('.bmain .cell').hover(
		function(){ $(this).addClass('hover'); },
		function(){ $(this).removeClass('hover'); }
	);
  //
  $('.sz').html($('.Around').width());
  $( window ).resize(function() {
    w = $('body').width();
    ElemPos();
    $('.sz').html($('body').width());
  });
  //
  fixHeader();
  $(document).add('body').scroll(function(){
    y = $(document).scrollTop()*1;
    fixHeader();
  });
  //
  function fixHeader(){
    if(y > 0) {
      $('div.inHeader').addClass('fixed');
    } else {
      $('div.inHeader').removeClass('fixed');
    }
    ElemPos();
  }
  //
  function ElemPos(){
    l = w > 1950 ? 1950-68 : (w < 1024 ? 1024-68 : w-68);
    $('div.hcart').css('left',l);
    $('#up-btn').css('left',l+11);
    //
    if(y/H*100>10) $('#up-btn').show();
    else $('#up-btn').hide();
  }
  //
  $('.btn-number').click(function(e){
    e.preventDefault();
    var $block    = $(this).parents('.input-group:first');
    var $input    = $block.find('input[type="text"]');
    var type      = $(this).attr('data-type');
    var currentVal = parseInt($input.val());
    if (!isNaN(currentVal)) {
      if(type == 'minus') {
        if(currentVal > $input.attr('min')) {
          $input.val(currentVal - 1).change();
        }
        if(parseInt($input.val()) == $input.attr('min')) {
          $(this).attr('disabled', true);
        }
      } else if(type == 'plus') {
        if(currentVal < $input.attr('max')) {
          $input.val(currentVal + 1).change();
        }
        if(parseInt($input.val()) == $input.attr('max')) {
          $(this).attr('disabled', true);
        }
      }
    } else {
      $input.val(0);
    }
  });
  $('.input-number').focusin(function(){
    $(this).data('oldValue', $(this).val());
  });
  $('.input-number').change(function() {
    var $block = $(this).parents('.input-group:first');
    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());
    if(valueCurrent >= minValue) {
      $block.find('.btn-number[data-type="minus"]').removeAttr('disabled')
    } else {
      alert('Sorry, the minimum value was reached');
      $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
      $block.find('.btn-number[data-type="plus"]').removeAttr('disabled')
    } else {
      alert('Sorry, the maximum value was reached');
      $(this).val($(this).data('oldValue'));
    }
    //
    if($('#frm-order').size()){
      $('#frm-order').attr('action','/cart.php?action=change').submit();
    }
  });
  $(".input-number").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
      // Allow: Ctrl+A
      (e.keyCode == 65 && e.ctrlKey === true) ||
      // Allow: home, end, left, right
      (e.keyCode >= 35 && e.keyCode <= 39)) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
  });
  //
  $('.main #lnk17').click(function(){
    $('html:not(:animated),body:not(:animated)').animate({scrollTop:$('h1.catalog').offset().top-75},500,function(){});
    return false;
  });
  $('#up-btn').click(function(){
    $('html:not(:animated),body:not(:animated)').animate({scrollTop:0},500,function(){});
  });
  //
  $('#call-btn').click(function(){ jPop('/inc/actions.php?show=call'); });
  //
  $('.bmain a[pid="16"]').click(function(){ jPop('/inc/actions.php?show=feedback'); return false; });
  //
  $('.bmain a[pid="lc"]').click(function(){ jPop('/inc/actions.php?show=auth'); return false; });
  //
  $('.Subscribe .btn').click(function(){ inajax('/inc/actions.php?action=subscribe','mail='+$('.Subscribe input').val()); return false; });
  //$(document).jAlert('show','confirm','Товар добавлен к Вашему заказу.<br>Вы желаете перейти в корзину?',function(){top.location.href='/cart.php'});
});

function jPop(url) {
  jQuery.arcticmodal({
    type: 'ajax',
    url: url,
    ajax: {
      type:'GET',
      cache: false,
      success:function(data, el, responce){
        data.body.html(jQuery('<div class="box-modal"><div class="box-modal_close arcticmodal-close">X</div>' + responce + '</div>'));
      }
    }
  });
}

function toCart(mod,quant)
{
  inajax('/cart.php','action=tocart&mod='+mod+'&quant='+(quant*1<1?1:quant));
}

function update_captcha()
{
	var tmp = new Date();
	$('#captcha').attr('src','/captcha/'+tmp+'/');
}

function isiPhone(){
  return (
    (navigator.platform.indexOf("iPhone") != -1) ||
    (navigator.platform.indexOf("iPod") != -1)
  );
}