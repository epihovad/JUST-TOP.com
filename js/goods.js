jQuery(document).ready(function( $ ) {
  $('.nav-tabs a:first').tab('show');
  //
  $('.good-preview a.more').click(function(){
    $('.nav-tabs a:first').tab('show');
    $('html:not(:animated),body:not(:animated)').animate({scrollTop:$('.nav-tabs a:first').offset().top - 70},500);
    return false;
  });
  //alert($(location).attr('hash'));
  //
  $( window ).resize(function() {
    gimHover($('.gim-other a.im').eq(0));
    gimModsHover($('.gim-mods a.im.active'));
  });
  //
  setTimeout(function () {
    gimHover($('.gim-other a.im').eq(0));
    gimModsHover($('.gim-mods a.im.active'));
  },200);
  //
  $('.gim-other a.im').click(function () {
    var $this = $(this);
    var ind = $this.index();
    var src = $this.attr('base');
    var $chief = $('.gim-chief');
    $chief.attr('ind',ind);
    $chief.find('img').attr('src',src);
    $('.gim-other a.im').removeClass('active');
    $(this).addClass('active');
    gimHover($this);
    return false;
  });
  //
  function gimHover($obj){
    $('.gim-over').show();
    var _w = $obj.outerWidth();
    var _h = $obj.outerHeight() + 8;
    var _t = $obj.offset().top - 4;
    var _l = $obj.offset().left;
    $('.gim-over').css({width: _w, height: _h}).offset({top: _t, left: _l});
  }
  //
  $('.gim-chief').click(function () {
    var ind = parseInt($(this).attr('ind'));
    ind = isNaN(ind) ? 0 : ind;
    if(ind >= 0){
      var $im = $('.gim-other a.im').eq(ind);
      var link = $im.attr('href'),
          options = {index: link, index: ind},
          links = $('.gim-other').find('a');
      blueimp.Gallery(links, options);
    } else {
      var link = $('.gim-mods a.im.active').attr('href'),
          options = {index: link},
          links = $('.gim-mods a.im.active');
      blueimp.Gallery(links, options);
    }
  });
  //
  $('.gim-mods a.im').click(function () {
    //
    var $this = $(this);
    $('.gim-mods a.im').removeClass('active');
    $this.addClass('active');
    gimModsHover($this);
    //
    $('.good-tocart .lb div').html($this.attr('mod_type'));
    //
    $('.good-tocart .price-actual').html($this.attr('price')+' руб');
    var old_price = parseInt($this.attr('old_price'));
    $('.good-tocart .price-old').html(old_price > 0 ? old_price+' руб' : '');
    //
    var src = $this.attr('base');
    var $chief = $('.gim-chief');
    $chief.attr('ind',-1);
    $chief.find('img').attr('src',src);
    $('.gim-other a.im').removeClass('active');
    $('.gim-over').hide();
    return false;
  });
  //
  function gimModsHover($obj){
    var _w = $obj.outerWidth() - 4;
    var _h = $obj.outerHeight() + 4;
    var _t = $obj.offset().top - 2;
    var _l = $obj.offset().left + 2;
    $('.gim-mods-over').css({width: _w, height: _h}).offset({top: _t, left: _l});
  }
  //
  $('.tocart').click(function(){
    var mod = $('.gim-mods a.im.active').attr('mod');
    var quant = $('.good-tocart input[name="quant"]').val();
    $('.tocart').addClass('disabled');
    toCart(mod,quant);
    return false;
  });
  //
  $('.one-click').click(function(){
    var mod = $('.gim-mods a.im.active').attr('mod');
    jPop('/inc/actions.php?show=call&mod='+mod);
    return false;
  });
});