/* OT Document JAVASCRIPT */
jQuery.noConflict();
jQuery(document).ready(function($) {

	// TOOLTIP - POPOVER
	$('.ot-single .productslide').on('slide.bs.carousel', function (e) {
		// console.log($(e.relatedTarget).index());
		var _over = $('.carousel-indicators li.li_img[data-slide-to="'+$(e.relatedTarget).index()+'"]', e.target);
		$('.carousel-nav .carousel-indicator-over', e.target).offset({left: _over.offset().left - 6});
	});
	function indicatorHover(){
		$('.ot-single .productslide .carousel-nav .carousel-indicator-over').each(function(){
			var $this = $(this);
			setTimeout(function () {
				var _iw = $('.active', $this.parent()).outerWidth() + 12;
				var _ih = $('.active', $this.parent()).outerHeight() + 12;
				var _it = $('.active', $this.parent()).offset().top - 6;
				var _il = $('.active', $this.parent()).offset().left - 6;
				$this.css({width: _iw, height: _ih}).offset({top: _it, left: _il});
			},200);
		});
	}
	indicatorHover();
	$( window ).resize(function() {
		indicatorHover();
	});
	// CAROUSEL MULTIPLE
	$('.carousel.slide[data-type="multi"]').on('slide.bs.carousel', function (e) {
		for (var i=0;i<($(e.target).find('.item').length);i++){
			$(e.target).find('.item').removeClass('next' + i);
			$(e.target).find('.item').removeClass('prev' + i);
		}
		$(e.relatedTarget).addClass('next0 prev0');
		var next = $(e.relatedTarget).next();
		if (!next.length) {
			next = $(e.relatedTarget).siblings(':first');
		}
		next.addClass('next1');
		for (var i=0;i<($(e.target).find('.item').length - 2);i++) {
			next=next.next();
			if (!next.length) {
				next = $(e.relatedTarget).siblings(':first');
			}
			next.addClass('next' + (i + 2));
		}
		var prev = $(e.relatedTarget).prev();
		if (!prev.length) {
			prev = $(e.relatedTarget).siblings(':last');
		}
		prev.addClass('prev1');
		for (var i=0;i<($(e.target).find('.item').length - 2);i++) {
			prev=prev.prev();
			if (!prev.length) {
				prev = $(e.relatedTarget).siblings(':last');
			}
			prev.addClass('prev' + (i + 2));
		}
	});
});

