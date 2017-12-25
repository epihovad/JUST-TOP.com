var hArr = [];

jQuery(document).ready(function( $ ) {
  //
  $('.user-data input[type=text], .user-data input[name="user[pwd]"], .user-data textarea').val('');
  var nopost = false;
  var $stype = $('.stype');
  var $dtype = $('.dtype');
  var $ptype = $('.ptype');
  //
  var i = 0;
  $('.step').each(function () {
    hArr[i++] = $(this).offset().top + $(this).outerHeight()/2;
  });
  //
  //
  if(!$.browser.iDevice) {
    $('.step').hover(function(){$(this).addClass('active')},function(){$(this).removeClass('active')});
  }
  CartStepPaint();
  $(document).add('body').scroll(function(){
    CartStepPaint();
  });
  //
  $(window).resize(function(){
    CartStepPaint();
    posCheck($stype,-1);
    posCheck($dtype,-1);
    posCheck($ptype,-1);
  });
  //
  $('.cart tbody .c2 a').click(function () {
    $this = $(this);
    var link = $this.attr('href'),
        options = {index: link},
        links = $this;
    blueimp.Gallery(links, options);
    return false;
  });
  //
  $('.cart tbody tr.row-mod').hover(
    function () { $(this).find('th,td').css('background-color','#f9f9f9'); },
    function () { $(this).find('th,td').css('background-color','#fff'); }
  );
  //
  Inputmask({mask: '+7 (999) 999-99-99', showMaskOnHover: false}).mask($('input[name="user[phone]"]'));
  //
  posCheck($stype,0,1);
  posCheck($dtype,0,1);
  posCheck($ptype,0,1);
  //
  $stype.find('.item').click(function () {
    var ind = $(this).index();
    posCheck($stype,ind);
    $(this).parents('.tile:first').find('.item').removeClass('active');
    $(this).addClass('active');
  });
  $dtype.find('.item').click(function () {
    var ind = $(this).index();
    /*if(ind == 2){
      $(document).jAlert('show','alert','Данный тип доставки временно недоступен.<br>Приносим Вам свои извинения за неудобства.');
      return false;
    }*/
    //
    if(ind > 0) {
      posCheck($ptype,0);
      nopost = true;
      $ptype.find('.item:gt(0)').addClass('blocked').removeClass('active');
      $ptype.find('.item:eq(0)').addClass('active');
    } else {
      nopost = false;
      $ptype.find('.item:gt(0)').removeClass('blocked');
    }
    //
    posCheck($dtype,ind);
    $(this).parents('.tile:first').find('.item').removeClass('active');
    $(this).addClass('active');
    //
    UpdateOrderTotal(4,'&dtype='+ind+'&ptype='+$('input[name="ptype"]').val());
  });
  $ptype.find('.item').click(function () {
    if(nopost){
      $(document).jAlert('show','alert','Данный тип оплаты при указанном способе доставки недоступен.');
      return false;
    }
    var ind = $(this).index();
    posCheck($ptype,ind);
    $(this).parents('.tile:first').find('.item').removeClass('active');
    $(this).addClass('active');
    //
    UpdateOrderTotal(5,'&ptype='+ind+'&dtype='+$('input[name="dtype"]').val());
  });
  //
  $('#frm-order input[type="text"]').focus(function () { var v = $(this).val(); $(this).attr('old-value',v) });
  //
  $('#frm-order .user-data input[type="text"]').blur(function () {
    var o = $(this).attr('old-value');
    var n = $(this).val();
    if(n!=o)
      UpdateOrderTotal(3);
  });
  //
  $('div.gdel').click(function(){
    var mod = parseInt($(this).parents('tr:first').attr('mod'));
    $(this).parents('tr:first').remove();
    $('#frm-order').attr('action','/cart.php?action=change&mod='+mod).submit();
  });
  //
  $('#frm-order .tocart').click(function () {
    $('#frm-order').attr('action','/cart.php?action=save').submit();
    return false;
  });
  //
  $('.callme li').click(function () {
    var ind = $(this).index();
    $('.callme button').html('<b>'+(ind?'нет':'да')+'</b><span class="caret"></span>');
    $('.callme input').val(ind?'0':'1');
    $(this).parents('.btn-group:first').removeClass('open');
    return false;
  });
});

function UpdateOrderTotal(num,prm) {
  jQuery('#order .row').addClass('wait');
  jQuery('#frm-order').attr('action','/cart.php?action=total&step='+num+prm).submit();
}

function posCheck($obj, ind, start) {
  var ind_cur = $obj.find('.item.active').index();
  if(!start && ind == ind_cur) return false;
  if(ind == -1) ind = ind_cur;
  var $ch = $obj.find('.ch');
  var _w = $ch.outerWidth();
  var _h = $ch.outerHeight();
  var $input = $obj.find('input');
  var $item = $obj.find('.item').eq(ind);
  var _iw = $item.outerWidth();
  var _ih = $item.outerHeight();
  var _it = $item.offset().top;
  var _il = $item.offset().left;
  $ch.offset({top: _it+_ih-_h-20, left: _il+_iw/2-_w/2});
  $input.val(ind);
}

function CartStepPaint() {
  if(!jQuery.browser.iDevice)
    return false;
  var y = jQuery(document).scrollTop()*1;
  var h = jQuery(window).height();
  var center = y + h/2 - 100;
  var dArr = [];
  var i = 0;
  jQuery('.step').each(function () {
    dArr[i] = Math.abs(center - hArr[i]);
    i++
  });
  var min = Math.min.apply(Math, dArr);
  var ind = parseInt(dArr.indexOf(min));
  var $step = jQuery('.step').eq(ind);
  if($step.hasClass('active'))
    return false;
  jQuery('.step').removeClass('active');
  $step.addClass('active');
}