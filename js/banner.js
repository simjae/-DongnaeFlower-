if(typeof(pops)=='undefined') {
	BaramangSwipe.mainBanner = function(obj, pagination, autoslidetime,options) {
		var _banner = $(obj).baramangSwipe(options && options.childTag || "div", $.extend({	
			elementCountPerGroup: 1,
			isLoop: true,
			isAutoScroll: true,
			autoScrollTime: autoslidetime,
		}, options));

		_banner.bannerNavigator = function() {
			_banner.success();
		};
		_banner.success = function() {
			$(".naviImg"+_banner.currentPageNo).attr("src","/m/images/pon.png");
		};
		return _banner;
	};
}