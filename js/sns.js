function showDiv(showID){
	if( showID != "snsHelp" && !checkMember()){
		return false;
	}
	if( showID == "snsSend"){
		$("#comment0").val("������ �Է��ϼ���.");
		$("#cmtByte").html("0");
		/*if($("input[name=send_chk]:checkbox:checked").length == 0){
			alert("snsä���� �������ּ���.");
			return false;
		}*/
	}
	if(preShowID != showID){
		
		if(preShowID!="" && $("#"+preShowID).length > 0) $("#"+preShowID).css({visibility:'hidden'});
		if($("#"+showID).length > 0) {
			//
			
			$("#"+showID).css({visibility:'visible'});
			
			$("#m_snsSend").attr("onClick","snsReg_top('f');");

		}
		preShowID = showID;
	}else{
		if($("#"+showID).length > 0) $("#"+showID).css({visibility:'hidden'});
		preShowID = "";
	}
	return false;
}



function checkMember(){
	if(memId == ""){
		alert("�α����� �ʿ��� �����Դϴ�.");
		return false;
	}
	return true;
}
function snsImg(){
	$.post("/front/snsAction.php", { "method": "snsImage" }, function(data){
		if (data.result == 'true') {
			if ( data.sns_image != "" )
				$("#snsThumb").attr("src", data.sns_image);
		}
		else {
			$("#snsThumb").attr("src", "/images/design/sns_default.jpg");
			//alert("���� �߻� : " + data.message );
		}
	}, "json");
}
function setImgChange(type){
	if ( $("#"+type+"LoginBtnChk").val() == "Y" ) {
		//sImg1 ="_off.png";sImg2 ="_on.png";
		chkBoolean = false;
	}else{
		//sImg1 ="_on.png";sImg2 ="_off.png";
		chkBoolean = true;
	}
	for(k=0;k<5;k++){
		
		var setName = "#"+type+"LoginBtn"+k;
		/*if($(setName).length > 0){
			$(setName).attr('src',$(setName).attr('src').replace(sImg1,sImg2));
		}*/
	}
	if($("#send_chk_"+type).length > 0){ 
		$("#send_chk_"+type).attr("disabled",chkBoolean);
		$("#send_chk_"+type).attr("checked",false); 
	}
}
function snsInfo(){
	$.post("/front/snsAction.php", { "method": "snsLoginCheck" },
	 function(data){
		if (data.result == 'true') {
			if (data.me2day == undefined ) {
				$("#mLoginBtnChk").val("NON");
				setImgChange("m");
			}else if ( data.me2day == "N") {
				$("#mLoginBtnChk").val("N");
				setImgChange("m");
			}else if ( data.me2day == "Y") {
				$("#mLoginBtnChk").val("Y");
				setImgChange("m");
			}
			if (data.twitter == undefined ) {
				$("#tLoginBtnChk").val("NON");
				setImgChange("t");
			}else if ( data.twitter == "N") {
				$("#tLoginBtnChk").val("N");
				setImgChange("t");
			}else if ( data.twitter == "Y") {
				$("#tLoginBtnChk").val("Y");
				setImgChange("t");
			}
			if (data.facebook == undefined ) {
				$("#fLoginBtnChk").val("NON");
				setImgChange("f");
			}else if ( data.facebook == "N") {
				$("#fLoginBtnChk").val("N");
				setImgChange("f");
			}else if ( data.facebook == "Y") {
				$("#fLoginBtnChk").val("Y");
				setImgChange("f");
			}
		}
		else {
			//alert("���� �߻� : " + data.message );
		}
	 }, "json");
}
function changeSnsInfo(type){
	if(checkMember()){
		var id = "#"+type+"LoginBtnChk";
		if (  $(id).val() == "Y" || $(id).val() == "N" ) {
			if ( $(id).val() == "Y" ) {
				$(id).val("N");
				setImgChange(type);
			}
			else if ( $(id).val() == "N" ){
				$(id).val("Y");
				setImgChange(type);
			}
			//sns �α׾ƿ�
			$.post("/front/snsAction.php", { "method": "snsChange",  "sns_type":type,  "sns_state":$(id).val() },
			 function(data){
				if (data.result == 'true') {
				}
				else {
					alert("���� �߻� : " + data.message );
				}
			 }, "json");
		}else{
			if(type == "m") {
				window.open("/front/snsLogin.php?type="+type,  'snsLogin', 'width=800, height=500, top=0, left=0, scrollbars=yes');
			}else if(type == "t") {
				window.open("/front/snsLogin.php?type="+type,  'snsLogin', 'width=800, height=500, top=0, left=0, scrollbars=yes');
			}else if(type == "f") {
				window.open("/front/facebook.php",  'snsLogin', 'width=1000, height=630, top=0, left=0, scrollbars=yes');
			}
			snsImg();
		}
	}
}
/*function snsReg_top(){
	if ( !checkMember() ) return false;
	if($("input[name=send_chk]:checkbox:checked").length == 0){
		alert("snsä���� �������ּ���.");
		return false;
	}
	if ( $.trim($("#comment0").val()) == '' ){
		alert("������  �Է��� �ֽʽÿ�.");
		return;
	}
	$("input[name=send_chk]").each(
		function(){
			if(this.checked)
				snsType += this.value+",";
		}
	)

	alert(snsType);
	snsCmt = $.trim($('#comment0').val());
	snsCommonReg(snsType);
	$("#comment0").val("");
	snsType ="";
	showDiv('snsSend');
}*/ 
//����
function snsReg_top(idx){
	//alert(idx);
	//if ( !checkMember() ) return false;
	/*if($("input[name=send_chk]:checkbox:checked").length == 0){
		alert("snsä���� �������ּ���.");
		return false;
	}*/
	if ( $.trim($("#comment0").val()) == '' ){
		alert("������  �Է��� �ֽʽÿ�.");
		return;
	}
	/*$("input[name=send_chk]").each(
		function(){
			if(this.checked)
				snsType += this.value+",";
		}
	)*/
	snsType = idx;
	alert(snsType);
	snsCmt = $.trim($('#comment0').val());
	snsCommonReg(snsType);
	$("#comment0").val("");
	snsType ="";
	showDiv('snsSend');
}

function snsReg(){
	if ( !checkMember() ) return false;

	if($("#tLoginBtnChk").val() != "Y" && $("#fLoginBtnChk").val() != "Y" && $("#mLoginBtnChk").val() != "Y"){
		alert("������ ä���� �����ϼ���.");
		return;
	}
	if ( $.trim($("#comment").val()) == '' ){
		alert("������  �Է��� �ֽʽÿ�.");
		return;
	}
	if ( $("#tLoginBtnChk").val() == "Y") {
		snsType +="t,";
	}
	if ( $("#fLoginBtnChk").val() == "Y") {
		snsType +="f,";
	}
	if ( $("#mLoginBtnChk").val() == "Y") {
		snsType +="m,";
	}
	snsCmt = $.trim($('#comment').val());
	snsCommonReg(snsType);	
	$("#comment").val("");
	snsType ="";
}

function snsCommonReg(snsType){
	$.post("/front/snsAction.php",
		{ method: "regPcode", pcode: pcode}
		,function(data){
			if ( data.result == 'true' ) {
				snsLink = $.trim(data.sns_url);
				$.post(
					"/front/snsAction.php",
					{ method: "regSns", sns_type:snsType, pcode: pcode, comment: snsCmt },
					  
					  function(data){
						if ( data.result == 'true' ) {
							if (snsType.indexOf("t") >-1) {
								$.post(
									"/front/twitterReg.php",
									{comment: snsCmt+" | "+snsLink, seq: data.seq, name:productName },
									  function(data){
										if ( data.result == 'true' ) {
											showSnsComment();
										}else{
											showSnsComment();alert("twitter error : " + data.message );
										}
									},"json"
								)				
							}
							if ( snsType.indexOf("m") >-1) {
								$.post(
									"/front/me2dayReg.php",
									{comment: snsCmt.substring(0,145-snsLink.length)+" | "+snsLink, seq: data.seq, name:productName  },
									  function(data){
										if ( data.result == 'true' ) {
											showSnsComment();
										}else{
											showSnsComment();alert("me2day error : " + data.message );
										}
									},"json"
								)
							}
							if ( snsType.indexOf("f") >-1) {
								$.post(
									"/front/facebookReg.php",
									{comment: snsCmt, seq: data.seq , link:snsLink, picture :fbPicture, name:productName },
									  function(data){
										if ( data.result == 'true' ) {
											showSnsComment();
										}else{
											showSnsComment();alert("facebook error: " + data.message );
										}
									},"json"
								)
							}							
						}
						else {
							alert("���� �߻� : " + data.message );
						}
					},
					"json"
				);
			}
			else {
				alert("���� �߻� " + data.message);
			}

		},
		"json"
	);
}

function CopyUrl(){
	if ( !checkMember() ) return false;
	$.post("/front/snsAction.php", { method: "regPcode", pcode: pcode}
		,function(data){
			if ( data.result == 'true' ) {
				window.clipboardData.setData("Text", data.sns_url);
				$.post(
					"/front/snsAction.php",
					{ method: "regSnsUrl", pcode: pcode},
					  function(data){
						if ( data.result == 'true' ) {
							alert("URL�� ����Ǿ����ϴ�.\n��α׳� �޽��� â�� �ٿ��ֱ� �غ�����!");
							showSnsComment();
						}
						else {

							alert("���� �߻� : " + data.message );
						}
					},
					"json"
				);
			}
			else {
				alert("���� �߻� " + data.message);
			}
		},
		"json"
	)
}

function CopyUrl2(){
	if ( !checkMember() ) return false;
	if(pcode == ""){ alert("��ǰ�� �����ϼ���.");return false;}
	$.post("/front/snsAction.php",
		{ method: "regGonggu", pcode: pcode}
		,function(data){
			if ( data.result == 'true' ) {
				window.clipboardData.setData("Text", data.sns_url);
				$.post(
					"/front/snsAction.php",
					{ method: "regGongguUrl", pcode: pcode},
					  function(data){
						if ( data.result == 'true' ) {
							alert("URL�� ����Ǿ����ϴ�.\n��α׳� �޽��� â�� �ٿ��ֱ� �غ�����!");
							showGongguCmt();
						}
						else {

							alert("���� �߻� : " + data.message );
						}
					},
					"json"
				);
			}
			else {
				alert("���� �߻� " + data.message);
			}
		},
		"json"
	);
}

function CopyBodUrl(){
	window.clipboardData.setData("Text", bodUrl);
}

function showSnsComment(block ,pgid){
	$.post(
		"/front/snsComment.php",
		{pcode: pcode, gotopage :pgid, block :block},
		  function(data){
			$("#snsBoardList").html(data);
		}
	)	
}

function CheckStrLen(maxlen,field,pos) {
	var fil_str = field.value;
	var fil_len = 0;
	fil_len =  field.value.length;
	//alert(this.value);
	if(pos =='top')
		$("#cmtByte").html(fil_len);
	if (fil_len > maxlen ) {
	   alert("�� " + maxlen + "�� ���� ���� �����մϴ�.");
	   field.value = fil_str.substr(0,maxlen);
	   return;
	}
}

function snsGongguReg(){
	if ( !checkMember() ) return false;

	if(gRegFrm =="list"){
		if ( pcode == '' ){
			alert("��ǰ�� �������ּ���.");
			return;
		}
	}

	if ( $.trim($("#gonggu_cmt").val()) == '' ){
		alert("������  �Է��� �ֽʽÿ�.");
		return;
	}
	if ( $("#tLoginBtnChk").val() == "Y") {
		snsType +="t,";
	}
	if ( $("#fLoginBtnChk").val() == "Y") {
		snsType +="f,";
	}
	if ( $("#mLoginBtnChk").val() == "Y") {
		snsType +="m,";
	}
	snsCmt = $.trim($('#gonggu_cmt').val());
	gongguCmtReg(snsType);	
	$("#gonggu_cmt").val("");
	snsType ="";
}

function gongguCmtReg(snsType){
	$.post("/front/snsAction.php",
		{ method: "regGonggu", pcode: pcode}
		,function(data){
			if ( data.result == 'true' ) {
				snsLink = $.trim(data.sns_url);
				$.post(
					"/front/snsAction.php",
					{ method: "regGongguCmt", sns_type:snsType, pcode: pcode, comment: snsCmt , etc:"11"},
					  function(data){
						if ( data.result == 'true' ) {
							if (snsType.indexOf("t") >-1) {
								$.post(
									"/front/twitterReg.php",
									{comment: snsCmt+" | "+snsLink, seq: data.seq, name:productName, gb:"2" },
									  function(data){
										if ( data.result == 'true' ) {
											showGongguCmt();
										}else{
											showGongguCmt();alert("twitter error : " + data.message );
										}
									},"json"
								)				
							}
							if ( snsType.indexOf("m") >-1) {
								$.post(
									"/front/me2dayReg.php",
									{comment: snsCmt.substring(0,145-snsLink.length)+" | "+snsLink, seq: data.seq, name:productName, gb:"2"  },
									  function(data){
										if ( data.result == 'true' ) {
											showGongguCmt();
										}else{
											showGongguCmt();alert("me2day error : " + data.message );
										}
									},"json"
								)
							}
							if ( snsType.indexOf("f") >-1) {
								$.post(
									"/front/facebookReg.php",
									{comment: snsCmt, seq: data.seq , link:snsLink, picture :fbPicture, name:productName, gb:"2" },
									  function(data){
										if ( data.result == 'true' ) {
											showGongguCmt();
										}else{
											showGongguCmt();alert("facebook error: " + data.message );
										}
									},"json"
								)
							}
							showGongguCmt();
						}
						else {
							alert("���� �߻� : " + data.message );
						}
					},
					"json"
				);
			}
			else {
				alert("���� �߻� " + data.message);
			}

		},
		"json"
	);
}

function showGongguCmt(block ,pgid){
	if(gRegFrm =="list"){
		//best ����
		showGongguCmtBest();
		pcode="";
		$("#prdtSchBtn").attr('src', "../images/design/gonggu_order_btn04.gif");
	}
	$.post(
		"/front/snsGongguCmt.php",
		{pcode: pcode, gotopage :pgid, block :block},
		  function(data){
			$("#snsGongguList").html(data);
		}
	);
}

function checkWrite(c_seq, pcode, chkmemId){
	if ( !checkMember() ) return false;

	if(chkmemId == ""){
		chkval = "fail";
		alert("�ڽ��� �ۿ� ��� �� �� �����ϴ�.");
	}else{
		var f = document.GongguWishFrm;
		if(f.comment.value==""){
			alert("�޼����� �Է��ϼ���");
		}
		$.post(
			"/front/snsAction.php",
			{ method: "regGongguChk", c_seq:c_seq, pcode: pcode},
			  function(data){
				if ( data.check == 'ok' ) {

					var viewportScroll = $(window).scrollTop();
			    	var cssLeft = (screen.width+200) / 2 - 420;
					var cssTop = (screen.height) / 2 + viewportScroll-320;					
					$("#GongguWish").appendTo('body').css({'position':'absolute','top':cssTop+'px','left':cssLeft+'px','z-index':'1000'}).show();
					$("#GongguWish .LayerHide").click(function() {
						$("#GongguWish").hide();
					});					
					f.c_seq.value = c_seq;
					f.pcode.value = pcode;
				}else if (data.check == "duplicated")
				{
					alert("�̹� ���� ��� �Ǿ� ��� �� �� �����ϴ�.");
				}
			},
			"json"
		);
	}
}

function txtchk(f){
	if(f.value=="���� �� ��ǰ �������Ÿ� ����մϴ�."){
		f.value="";
	}
}
function regTogetherGonggu(){
	var f = document.GongguWishFrm;
	var chk = "";
	//�ڵ��� ���� ���ſ��� üũ
	if(f.hpno.checked&&f.email.checked){
		chk = "11";
	}else if(!(f.hpno.checked)&&f.email.checked){
		chk = "01";
	}else if(f.hpno.checked&&!(f.email.checked)){
		chk = "10";
	}else if(!(f.hpno.checked)&&!(f.email.checked)){
		chk = "00";
	}
	f.etc.value = chk;

	var reg_con = confirm('��û�Ͻðڽ��ϱ�? [Ȯ��]�� �����ø�, ��û�˴ϴ�.');
 	if ( reg_con == true  )
	{
		f.method.value = "regGongguCmtsub";
		f.action = "snsAction.php";
		f.target = "ifrmHidden";
		f.submit();
	 }
	 $("#GongguWish").hide();
	 showGongguCmt();
}
 
var preCmtSeq = "";
var preCmtSubObj = "";
function showGongguCmtRe(obj){
	var c_seq = obj.next('span').text();
	if(preCmtSubObj !=""){
		preCmtSubObj.attr('src',preCmtSubObj.attr('src').replace("gonggu_order_btn03_c.gif","gonggu_order_btn03.gif"));
		$("#GongguCmtSubList"+preCmtSubObj.next('span').text()).html("");
	}
	if(preCmtSeq =="" || preCmtSeq != c_seq){
		$.post(
			"/front/snsGongguCmtSub.php",{c_seq :c_seq},
			  function(data){
				preCmtSeq = c_seq;
				preCmtSubObj = obj;
				obj.attr('src',obj.attr('src').replace("gonggu_order_btn03.gif","gonggu_order_btn03_c.gif"));
				$("#GongguCmtSubList"+c_seq).html(data);
				
			}
		)
	}else{
		preCmtSeq="";
		preCmtSubObj = "";
	}
}

//����Ʈ����
function delGongguCmt(seq){
	if(confirm("�����Ͻðڽ��ϱ�?")) {
		$.post(
			"/front/snsAction.php",
			{ method: "delGongguCmt", seq:seq},
			  function(data){
				if ( data.result == 'true' ) {
					alert("�����Ǿ����ϴ�.");
					showGongguCmt();
					return false;
				}else
				{
					alert("�̹� ���� ��� �Ǿ� ���� �� �� �����ϴ�.");
				}
			},
			"json"
		);
	}
}

//BEST ����
function showGongguCmtBest(){
	$.post(
		"/front/snsGongguCmtBest.php",
		  function(data){
			$(".gongguBest").html(data);
		}
	);
}


$('#prdtSchBtn').click(function(){
	var viewportScroll = $(window).scrollTop();
	var cssLeft = document.body.clientWidth/2 - 330;
	var cssTop = document.body.clientHeight/2 - 250 + viewportScroll;	

	$("#gongPrdtSearch").appendTo('body').css({'position':'absolute','top':cssTop+'px','left':cssLeft+'px','z-index':'1000'}).show();
	schGongguPrdt();

});

var mnuTab=1;
var categoryCode = "";
var s_check="";
var search_txt="";
function schGongguPrdt(){
	categoryCode ="";s_check="";search_txt="";
	$.post(
		"/front/gongguProduct.php",
		{ mnuTab: mnuTab},
		  function(data){
			$("#gongPrdtSearch").html(data);			
			$("#gongPrdtClose").click(function() {
				$("#gongPrdtSearch").hide();
			});	
			if(mnuTab ==1){
				showCatagory(1);
				searchPList();
			}
		}
	);
}

function selProductTab(tabId){
	mnuTab = tabId;
	schGongguPrdt();
}

function showCatagory(depth){
	$.post(
		"/front/gongguProductCtgr.php",
		{ depth: depth, code:categoryCode},
		  function(data){
			$("#prdt_ctgr"+depth).html(data);
		}
	);
}


function selectCode(depth,obj){
	categoryCode = obj.value;
	for(i=depth+1;i<=4;i++){
		$("#prdt_ctgr"+i).empty();
	}
	if(obj.value !="" && depth<4 && obj.ctype!="X"){
		showCatagory(depth+1);
	}
}

function searchCheck(){
	s_check = $("#s_check").val();
	search_txt = $("#search_txt").val();
	searchPList();
}

function searchPList(block ,pgid){
	$.post(
		"/front/gongguProductList.php",
		{code:categoryCode, s_check:s_check, search_txt:search_txt, gotopage :pgid, block :block},
		  function(data){
			$("#prdtList").html(data);
		}
	);
}

function selectProduct(p_code){
	pcode=p_code;
	productName=$("#thumb_"+p_code).attr('alt');
	$("#prdtSchBtn").attr('src', $("#thumb_"+p_code).attr('src'));
	$("#gongPrdtSearch").hide();
}

/* board sns comment */
function showbodComment(){
	$.post(
		"/board/snsbodComment.php",
		{board:board, num: bod_uid},
		  function(data){
			$("#snsBoardList").html(data);
		}
	)	
}

function snsbodReg(){
	if ( !checkMember() ) return false;
/*
	if($("#tLoginBtnChk").val() != "Y" && $("#fLoginBtnChk").val() != "Y" && $("#mLoginBtnChk").val() != "Y"){
		alert("������ ä���� �����ϼ���.");
		return;
	}
*/
	if ( $.trim($("#comment").val()) == '' ){
		alert("������  �Է��� �ֽʽÿ�.");
		return;
	}
	snsType = "";
	if ( $("#tLoginBtnChk").val() == "Y") {
		snsType +="t,";
	}
	if ( $("#fLoginBtnChk").val() == "Y") {
		snsType +="f,";
	}
	if ( $("#mLoginBtnChk").val() == "Y") {
		snsType +="m,";
	}
	snsCmt = $.trim($('#comment').val());
	$.post("/front/snsAction.php",
		{ method: "regBodLink", board:board, bod_uid: bod_uid}
		,function(data){
			if ( data.result == 'true' ) {
				snsLink = $.trim(data.sns_url);
				$.post(
					"/front/snsAction.php",
					{ method: "regBod", sns_type:snsType, board:board, bod_uid: bod_uid, comment: snsCmt },
					  function(data){
						if ( data.result == 'true' ) {
							if (snsType.indexOf("t") >-1) {
								$.post(
									"/front/twitterReg.php",
									{comment: snsCmt+" | "+snsLink, seq: data.num, gb:"3" },
									  function(data){
										if ( data.result == 'true' ) {
											showbodComment();
										}else{
											showbodComment();alert("twitter error : " + data.message );
										}
									},"json"
								)				
							}
							if ( snsType.indexOf("m") >-1) {
								$.post(
									"/front/me2dayReg.php",
									{comment: snsCmt.substring(0,145-snsLink.length), seq: data.num, gb:"3"  },
									  function(data){
										if ( data.result == 'true' ) {
											showbodComment();
										}else{
											showbodComment();alert("me2day error : " + data.message );
										}
									},"json"
								)
							}
							if ( snsType.indexOf("f") >-1) {
								$.post(
									"/front/facebookReg.php",
									{comment: snsCmt, seq: data.num , link:snsLink, gb:"3" },
									  function(data){
										if ( data.result == 'true' ) {
											showbodComment();
										}else{
											showbodComment();alert("facebook error: " + data.message );
										}
									},"json"
								)
							}
							showbodComment();
						}
						else {
							alert("���� �߻� : " + data.message );
						}
					},
					"json"
				);
			}
			else {
				alert("���� �߻� " + data.message);
			}

		},
		"json"
	);

	$("#comment").val("");
}

function delbodComment(c_num){
	$.post(
		"/board/snsbodcomment_del.php",
		{board:board, num: bod_uid, c_num:c_num},
		  function(data){
			if ( data.result == 'ok' ) {
				alert("�����Ǿ����ϴ�.");
				showbodComment();
			}else if( data.result == 'no authority' ) {
				alert("������ �����ϴ�.");
			}else if( data.result == 'nodata' ) {
				alert("�����Ͱ� �����ϴ�.");
				showbodComment();
			}else if( data.result == 'no reply' ) {
				alert("����� ���������ʽ��ϴ�.");
			}
		},
		"json"
	);	
}

function snsbodCopy(){
	if ( !checkMember() ) return false;
	if($("input[name=send_chk]:checkbox:checked").length == 0){
		alert("snsä���� �������ּ���.");
		return false;
	}
	if ( $.trim($("#comment0").val()) == '' ){
		alert("������  �Է��� �ֽʽÿ�.");
		return;
	}
	$("input[name=send_chk]").each(
		function(){
			if(this.checked)
				snsType += this.value+",";
		}
	)
	snsCmt = $.trim($('#comment0').val());

	if (snsType.indexOf("t") >-1) {
		$.post(
			"/front/twitterReg.php",
			{comment: snsCmt+" | "+bodUrl },
			  function(data){
				if ( data.result == 'true' ) {
				}
			},"json"
		)				
	}
	if ( snsType.indexOf("m") >-1) {
		$.post(
			"/front/me2dayReg.php",
			{comment: snsCmt.substring(0,145-bodUrl.length)+" | "+bodUrl },
			  function(data){
				if ( data.result == 'true' ) {
				}
			},"json"
		)
	}
	if ( snsType.indexOf("f") >-1) {
		$.post(
			"/front/facebookReg.php",
			{comment: snsCmt, link:bodUrl, picture :fbPicture },
			  function(data){
				if ( data.result == 'true' ) {
				}
			},"json"
		)
	}


	$("#comment0").val("");
	snsType ="";
}

function showDiv_bod(showID){
	if( showID != "snsHelp" && !checkMember()){
		return false;
	}
	if( showID == "snsSend"){
		$("#comment0").val(bodbase_txt);
		$("#cmtByte").html(bodbase_txt.length);
		if($("input[name=send_chk]:checkbox:checked").length == 0){
			alert("snsä���� �������ּ���.");
			return false;
		}
	}
	if(preShowID != showID){
		if(preShowID!="" && $("#"+preShowID).length > 0) $("#"+preShowID).css({visibility:'hidden'});
		if($("#"+showID).length > 0) $("#"+showID).css({visibility:'visible'});
		preShowID = showID;
	}else{
		if($("#"+showID).length > 0) $("#"+showID).css({visibility:'hidden'});
		preShowID = "";
	}
	return false;
}
