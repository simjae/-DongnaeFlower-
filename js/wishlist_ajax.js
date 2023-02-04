function wishAjax(code, e){
	$.ajax({
		type:'POST',
		url:'/m/inc/wishlist_ajax.php',
		dataType:"html",
		data:{code:code},
		success:function(msg){
			if(msg=='on'){
				$('#'+e).css('background-image','url("./skin/default/img/icon_wishlist_on.png")');
				alert('위시리스트에 추가되었습니다.');
			}
			else if(msg=='off'){
				$('#'+e).css('background-image','url("./skin/default/img/icon_wishlist_off.png")');
				alert('위시리스트에서 삭제되었습니다.');
			}
			else
				alert(msg);
		}
	});
}