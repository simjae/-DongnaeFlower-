// JavaScript Document

function logout() {
	//location.href="../main/main.php?type=logout";
	location.href="logout.php";
	return;
}

function TopSearchCheck() {
	try {
		if(document.search_tform.search.value.length==0) {
			alert("상품 검색어를 입력하세요.");
			document.search_tform.search.focus();
			return;
		}
		document.search_tform.submit();
	} catch (e) {}
}



function sendsns(type,title,shop_url,site_name){
	//var shop_url = shop_url;
	switch(type){

		case "twitter" :
			var link = 'http://twitter.com/home?status=' + encodeURIComponent(title) + ' : ' + encodeURIComponent(shop_url);
			var w = window.open("http://twitter.com/home?status=" + encodeURIComponent(title) + " " + encodeURIComponent(shop_url), 'twitter', 'menubar=yes,toolbar=yes,status=yes,resizable=yes,location=yes,scrollbars=yes');
			if(w)  {	w.focus();	}
			break;

		case "facebook" :
			var link = 'http://www.facebook.com/share.php?t=' + encodeURIComponent(title) + '&u=' + encodeURIComponent(shop_url);
			var w = window.open(link,'facebook', 'menubar=yes,toolbar=yes,status=yes,resizable=yes,location=yes,scrollbars=yes');
			if(w)  {	w.focus();	}
			break;

		default :
		break;
	}
}

function quantityControl(mode, idx){
	var _form = document['form_'+idx];

	if(mode != null || mode != 'undifined'){
		if(mode == 'plus'){
			_form.quantity.value = parseInt(_form.quantity.value) + 1;
		}

		if(mode == 'minus'){
			if(_form.quantity.value > 1){
				_form.quantity.value = parseInt(_form.quantity.value) - 1;
			}else{
				alert("최소 구매가능한 수량은 1개 입니다.");
			}
		}
	}
}

function quantityControlGlobal(mode, _form){

	if(mode != null || mode != 'undifined'){
		if(mode == 'plus'){
			_form.quantity.value = parseInt(_form.quantity.value) + 1;
		}

		if(mode == 'minus'){
			if(_form.quantity.value > 1){
				_form.quantity.value = parseInt(_form.quantity.value) - 1;
			}else{
				alert("최소 구매가능한 수량은 1개 입니다.");
			}
		}
	}
}

function openSubCate(idx){
	var open = document.getElementById('btn_plus_'+idx);
	var close = document.getElementById('btn_minus_'+idx);
	var viewbox = document.getElementById('subCatelist_'+idx);
	if(idx != "undifined" && idx != null){
		open.style.display = "none";
		close.style.display = "inline-block";
		viewbox.style.display = "block";
	}
}

function closeSubCate(idx){
	var open = document.getElementById('btn_plus_'+idx);
	var close = document.getElementById('btn_minus_'+idx);
	var viewbox = document.getElementById('subCatelist_'+idx);
	if(idx != "undifined" && idx != null){
		close.style.display = "none";
		open.style.display = "inline-block";
		viewbox.style.display = "none";
	}
}

function _toggle(idx){
	var open = document.getElementById('btn_plus_'+idx);
	var close = document.getElementById('btn_minus_'+idx);
	var viewbox = document.getElementById('subCatelist_'+idx);
	if(viewbox.style.display == 'block'){
		viewbox.style.display = 'none';
		close.style.display = 'none';
		open.style.display = 'inline-block';	
	}else{
		viewbox.style.display = 'block';
		open.style.display = 'none';
		close.style.display = 'inline-block';
	}
	return;
}

function venderInfo(vidx){
	location.href="./venderinfo.php?vidx="+vidx;
	return;
}

function noticeView(num,thread,board){
	if(num != "" && thread !="" && board !=""){
		location.replace("/m/notice_view.php?board="+board+"&num="+num+"&thread="+thread);
		return;
	}else{
		alert("필수값이 전달되지 않아\n페이지를 열람하실 수 없습니다.");
		return false;
	}
}

function zoomImage(imgurl){
	var _url = "./zoomimage.php?iurl="+imgurl;

	window.open(_url,'zoomimgage','scrollbars=yes,toolbar=yes,resizable=yes');
	return;
}

function go_wishlist(idx) {
	document.wishform.mode.value = "wishlist";
	document.wishform.productcode.value = document["form_" + idx].productcode.value;
	document.wishform.opts.value = document["form_" + idx].opts.value;
	document.wishform.option1.value = document["form_" + idx].orgoption1.value;
	document.wishform.option2.value = document["form_" + idx].orgoption2.value;
	document.wishform.opt_comidx.value = document["form_" + idx].opt_comidx.value;
	//window.open("about:blank","confirmwishlist","width=500,height=300,scrollbars=no");
	document.wishform.submit();
	alert("1")
}