//정방형 리사이징
function resizing_img() {
	$('.resizing_img').each(function () {
		$(this).wrap("<div class='resizing_wrap'></div>");
		if ($(this).width() < $(this).height()) {
			$(this).css('width', '100%');
			$(this).css('height', 'auto');
		} else {
			$(this).css('width', 'auto');
			$(this).css('height', '100%');
		}
	});
}

//제품 레이아웃
function product_view() {
	$('.product_view').each(function () {
		$(this).height($(this).width())
	});

	$('.product_slide_wrap').each(function () {
		var productH = $(this).find('.product_view').height();
		$(this).find('.slide_button').css("top", productH / 2 + $(this).find('.product_view').position().top);
	});
}

//제품 슬라이더
function product_slider() {
	$('.product_slide').each(function () {
		$(this).wrap("<div class='product_slide_wrap'></div>");
		$(this).after("<div class='slide_button'><div class='wrapper'><div><div class='swiper-button-prev'></div><div class='swiper-button-next'></div></div></div></div>");

		if ($(this).attr('col') == undefined) {
			if ($(this).hasClass('product_b') == true) {
				product_col = 1;
			} else if ($(this).hasClass('product_c') == true) {
				product_col = 1;
			} else {
				product_col = 2;
			}
		} else {
			product_col = $(this).attr('col');
		}
		if ($(this).attr('slide-delay') == undefined) {
			slide_delay = false;
		} else {
			slide_delay = {
				delay: $(this).attr('slide-delay'),
				disableOnInteraction: false
			}
		}

		var product_slide = new Swiper($(this), {
			loop: true,
			navigation: {
				nextEl: $(this).parent('.product_slide_wrap').find('.swiper-button-next'),
				prevEl: $(this).parent('.product_slide_wrap').find('.swiper-button-prev')
			},
			containerModifierClass: 'product_slide',
			wrapperClass: 'product_slide .product_list',
			slideClass: 'product_slide .product_item',
			slidesPerView: product_col,
			spaceBetween: parseInt($(this).find('.product_item').css("margin-right")),
			autoplay: slide_delay
		});

	});
}

$(document).ready(function () {

	//모바일 메뉴
	$('.left_close').click(function () {
		$('.left_bg').fadeOut();
		$('#left_menu').removeClass('on');
		$('body').removeClass('lock');
	});
	$('#gnb_button').click(function(){
		$('.left_bg').fadeIn();
		$('#left_menu').addClass('on');
		$('body').addClass('lock');
	});

	$('.detail_options>div>ul').click(function () {
		$(this).toggleClass('on').parent('div').siblings('div').children('ul').removeClass('on');
	});
	$('.options>div>ul').click(function () {
		$(this).toggleClass('on').parent('div').siblings('div').children('ul').removeClass('on');
	});
	$('.option_button').click(function(){
		$('.detail_button').toggleClass('on');
		$('body').toggleClass('lock');
	});
	product_slider();
	product_view();
	resizing_img();

	$(window).resize(function () {
		product_view();
		resizing_img();
	});

});

function _toggle(idx){
	//var open = document.getElementById('btn_plus_'+idx);
	//var close = document.getElementById('btn_minus_'+idx);
	var viewbox = document.getElementById('subCatelist_'+idx);
	if(viewbox.style.display == 'block'){
		viewbox.style.display = 'none';
		//close.style.display = 'none';
		//open.style.display = 'inline-block';	
	}else{
		viewbox.style.display = 'block';
		//open.style.display = 'none';
		//close.style.display = 'inline-block';
	}
	return;
}

//입점사 바로가기
function venderInfo(vidx){
	location.href="./venderinfo.php?vidx="+vidx;
	return;
}