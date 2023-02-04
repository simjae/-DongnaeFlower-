function cateAjax(type,code){
	var _object = document.getElementById('catelistwrap');
	$.ajax({
		type:'POST',
		url:'/m/inc/cate_ajax.php',
		dataType:"html",
		data:{code:code,type:type},
		success:function(msg){
			
			if(type!='p'){

				$('.btn_box_m').hide();
				$('.btn_box_p').show();
			}else{
				if(_object.style.display != 'block'){
					alert("카테고리가 숨기기 설정되어있습니다.");
				}
				
				$('.btn_box_p').hide();
				$('.btn_box_m').show();
			}
			$('#catelist').html(decodeURIComponent(msg));
		}
	});
}


function cateAll(){
	var _object = document.getElementById('catelistwrap');

	if(_object.style.display == 'block'){
		_object.style.display = 'none';
		$('#allList').text('펼치기');
	}else{
		_object.style.display = 'block';
		$('#allList').text('접기');
	}
}