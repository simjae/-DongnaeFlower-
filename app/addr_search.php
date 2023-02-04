<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");

$form=$_REQUEST["form"];
$post=$_REQUEST["post"];
$addr=$_REQUEST["addr"];
$gbn=$_REQUEST["gbn"];

$area=trim($_POST["area"]);
$mode=$_POST["mode"];

if (strlen($area)>2 && (strpos(getenv("HTTP_REFERER"),"addr_search.php")==false || strpos(getenv("HTTP_REFERER"),getenv("HTTP_HOST"))==false)) {
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>우편번호 검색</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="./css/common.css" />
	<style>
		article h1 {height:35px;line-height:35px;background-color:#555555;color:#FFFFFF;text-align:center;font-size:1.2em;letter-spacing:4px;}
		section {width:96%; margin:0 auto;}
		input {box-sizing:border-box;}
		#menu_wrap {clear:both; width:100%; font-size:1.1em; margin:20px 0px 10px 0px; padding-bottom:8px; border-bottom:1px solid #222222;}
		#menu_wrap li {width:50%;float:left; text-align:center; cursor:pointer; height:25px; margin:10px 0px; line-height:25px}
		#menu_wrap a {padding:6px 12px;}
		#searchFormLocal {height:28px; padding:8px 0px; border-top:1px solid #dddddd; border-bottom:1px solid #dddddd;}
		#searchFormLocal input {height:26px; width:250px; border:1px solid #DDDDDD}
		#searchFormAPI {padding:8px 0px; border-top:1px solid #dddddd; border-bottom:1px solid #dddddd;}
		#searchFormAPI th {width:25%; text-align:left; padding-left:10px; background:#f2f2f2;}
		#searchFormAPI td {height:30px; padding-left:5px;}
		#searchFormAPI td select {height:26px; width:100%;}
		#addr2 .input {height:26px; width:100%; border:1px solid #DDDDDD}
		#roadBtn {text-align:center;}

		#addr1, #addr2 {margin-top:30px;}
		#searchResultLocal, #searchResultAPI {margin-top:30px;}
	</style>
</head>
<body>
	<article>
		<h1>우편번호 검색</h1>
		<section>
			<div id="searchWrap">
				<!-- <div id="menu_wrap"><a href="javascript:searchMenu(0);" class="searchMenu" class="black">지번주소 검색</a><a href="javascript:searchMenu(1);" class="white">도로명주소 검색</a></div> -->
				<div id="menu_wrap"><a href="javascript:searchMenu(0);" >지번주소 검색</a><a href="javascript:searchMenu(1);" >도로명주소 검색</a></div>
				<div style="margin:10px 8px; color:#888888;">
					시/도 및 시/군/구를 선택하신 후 "도로명" 혹은 "읍/면/동"을 입력하세요.
				</div>
				<div id="searchFormAPI">
					<form method="POST" name="apiForm" id="apiForm" action="">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<th>- 시/도</th>
								<td><select name="sidocode"><option value="">-- 목록 호출중 --</option></select></td>
							</tr>
							<tr>
								<th>- 군/구</th>
								<td><select name="sigunguname"><option value="">선택해주세요</option><option value="">선택해주세요</option><option value="">선택해주세요</option><option value="">선택해주세요</option></select></td>
							</tr>
							<tr>
								<th id="sendTitle"></th>
								<td id="sendFiled"></td>
							</tr>
							<tr>
								<th class="bildnumber"></th>
								<td class="bildnumber"></td>
							</tr>
						</table>
					</form>
					<div id="roadBtn" style="position:absolute;right:16px;top:220px;"><a href="javascript:callAPI();" class="button black">검색</a></div>
				</div>
				<div style="margin-top:10px; text-align:center;"><a href="javascript:close();" class="button white">닫기</a></div>

				<div id="searchResultAPI" style="display:;">
					<style type="text/css">
						#apiResultTbl{margin-top:10px;}
						#apiResultTbl td.noResult{ text-align:center;padding-top:10;color:#EE4900; font-weight:bold; }
						#apiResultTbl td.zipCodeStr{text-align:center; width:70px; color:#FF6C00; font-weight:bold; }
						#apiResultTbl td.zipCodeStr a:link {color:#FF6C00;}
						#apiResultTbl td.zipAddrStr{ font-weight:bold; padding:5px 0px; }
						#apiResultTbl td.oddItem{ background:#ffffff; }
						#apiResultTbl td.evenItem{ background:#F3F3F3; }
						#apiResultTbl .oldAddress{ font-weight:normal; }
					</style>
					<table cellpadding="0" cellspacing="0" width="100%" id="apiResultTbl">
						<tbody>
						</tbody>
					</table>
					<div id="APIpageStr"></div>
				</div>
			</div>
		</section>
	</article>


	<script type="text/javascript" src="./js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="./js/common.js"></script>
	<script>
		var form="<?=$form?>";
		var post="<?=$post?>";
		var addr="<?=$addr?>";
		var gbn="<?=$gbn?>";
		var zipXhr = null;
		var searchMode =0;
		
		$(function(){
			searchMenu(0);
			initSidoCode();
			$("select[name='sidocode']").on('change',function(e){	getSigunguname($(this).val());});
			/*$('#apiForm').submit(function(e){
				e.preventDefault();
				initFunc=false;
				findZipAPI();
			});*/
		});
		
		function stopApi(){
			if(zipXhr != null) zipXhr.abort();
		}

		function do_submit2(zipcode,straddr,ext){
			try {
				if(gbn=="2") {
					opener.document[form][post+'1'].value=zipcode;
				} else {
					opener.document[form][post].value=zipcode;
				}
				opener.document[form][addr].value=straddr;

				if(addr.substr(addr.length-1,1) == '1'){
					var addr2 = addr.substr(0,addr.length-1)+'2';
					if(opener.document[form][addr2]){
						opener.document[form][addr2].value = ext;
					}
				}
				stopApi();
				window.close();
			} catch (e) {
				alert("오류가 발생하였습니다.");
			}
		}

		function findZipAPI(){
			var sidocode = $("#apiForm").find("select[name='sidocode']").val();
			var sigunguEl = $("#apiForm").find("select[name='sigunguname']");
			var sigunguname = $(sigunguEl).val();
			var roadname = $("#apiForm").find("input[name='roadname']").val();
			var dongname = $("#apiForm").find("input[name='dongname']").val();
			var bldmainnum = $("#apiForm").find("input[name='bldmainnum']").val();
			var jibun      = $("#apiForm").find("input[name='jibun']").val();
			var apimode ='test';

			if($.trim(sidocode).length < 1){
				alert('시/도 를 선택해주세요');
			}else if($(sigunguEl).find('option:eq(0)').text() != '없음' &&  !$(sigunguEl).attr('disabled') &&  $.trim(sigunguname).length < 1){
				alert('시/군/구 를 선택해주세요');
			}else{
				if(searchMode == 0 && $.trim(dongname) < 1){
					alert("읍/면/동을 입력해주세요.");
					return;
				}
				if(searchMode == 1 && $.trim(roadname) < 1){
					alert("도로명을 입력해주세요.");
					return;
				}
				initResult();
				var obj = {'apiname':'roadzip_N','method':'search','sidocode':sidocode,'sigunguname':escape(sigunguname),'roadname':escape(roadname),'dongname':escape(dongname),'apimode':escape(apimode),'bldmainnum':escape(bldmainnum),'jibun':escape(jibun),'perpage':'20'};
				if(zipXhr && zipXhr.readystate != 4) zipXhr.abort(); // 실행중 쿼리 취소
				requestAPIsearch(obj);
			}
		}

		function requestAPIsearch(obj){
			var $page;
			var $totalpage;
			zipXhr = $.post('/lib/api.php',obj,
				function(data){
					var $emsg = $(data).find('msg:eq(0)').text();
					if($.trim($emsg).length){
						alert($emsg);
					}else{
						var $rst = $(data).find('result');
		
						$page = $($rst).attr('page');
						$totalpage = $($rst).attr('totalpage');
		
						var $cnt = $($rst).attr('itemcount');
						var $itm = $($rst).find('item');
						if(parseInt($cnt) <1){
							$('#searchResultAPI').find('table:eq(0)').find('tbody').append('<tr><td class="noResult">검색 결과가 없습니다.</td></tr>');
						}else{
							dispAPIresult($itm);
						}
					}
				},"xml").done(function(){
					if(parseInt($page) < parseInt($totalpage)){
						obj.page = parseInt($page)+1;
						requestAPIsearch(obj);
					}
				}).fail(function(jqXHR, textStatus){ if(textStatus != 'abort') alert('api 연동 부분에 오류가 있습니다. (1)');});
		}

		function dispAPIresult($itm){
			$($itm).each(function(idx,itm){
				var eclass = (idx > 0 && idx%2 == 1)?'evenItem':'oddItem',
					zipcode = $(itm).find('basicareanum').text(),
					addr = $(itm).find('sidoname').text()+' '+$(itm).find('sigunguname').text()+' '+$(itm).find('roadname').text(),
					bldmainnum = $(itm).find('bldmainnum').text(),
					bldsubnum  = $(itm).find('bldsubnum ').text(),
					bldname  = $(itm).find('bldname').text(),
					dbldname  = $(itm).find('dbldname').text(),
					bldname2  = $(itm).find('bldname2').text(),
					ext = '', makehtml = '',
					addrold = $(itm).find('sidoname').text()+' '+$(itm).find('sigunguname').text()+' '+$(itm).find('bymdongname').text(),
					jimain = $(itm).find('jibunmain').text(),
					jisub = $(itm).find('jibunsub').text();

				if($.trim(bldmainnum).length) ext+= bldmainnum;
				if($.trim(bldsubnum).length) ext+= '-'+bldsubnum ;
				if($.trim(dbldname).length) ext+= ' '+dbldname ;
				if($.trim(bldname2).length) ext+= ' ('+bldname2+')';

				if($.trim(jimain).length) addrold+= ' '+jimain;
				if($.trim(jisub).length) addrold+= '-'+jisub;

				if(searchMode ==0){ // 지번
					makehtml = '<tr><td class="zipCodeStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');">'+zipcode+'</a></td><td class="zipAddrStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');"><span class="oldAddress">'+addrold+'</span><br/>'+addr+' '+ext+'</a></td></tr>'
				}else{//도로명
					makehtml = '<tr><td class="zipCodeStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');">'+zipcode+'</a></td><td class="zipAddrStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');">'+addr+' '+ext+'<br><span class="oldAddress">'+addrold+'</span></a></td></tr>'
				}
				$('#searchResultAPI').find('table:eq(0)').find('tbody').append(makehtml);
			});
		}


		function getSigunguname(sidocode){
			initSigunguname();
			$.post('/lib/api.php',{'apiname':'roadzip_N','method':'getgugun','sidocode':sidocode},
				function(data){
					var $emsg = $(data).find('msg:eq(0)').text();
					if($.trim($emsg).length){
						alert($emsg);
					}else{
						var $rst = $(data).find('result');
						var $cnt = $($rst).attr('itemcount');
						var $itm = $($rst).find('item');
						var target = $("select[name='sigunguname']");
						if($cnt == '1' && $.trim($($itm[0]).find('sigunguname').text()).length < 1){
							$(target).find('option:eq(0)').text('없음');
							$(target).attr('disabled',true);
						}else{				
							$(target).attr('disabled',false);
							$(target).find('option:eq(0)').text('선택해주세요');
							$($itm).each(function(idx,opt){
								$(target).append('<option value="'+$(opt).find('sigunguname').text()+'">'+$(opt).find('sigunguname').text()+'</option>');
							});
						}
						
						
					}
				},"xml").done(function(){}
			).fail(function(){ alert('api 연동 부분에 오류가 있습니다. (2)');});
		}

		function initSigunguname(){
			$("select[name='sigunguname']").find('option:gt(0)').remove();
		}

		function initResult(){
			$('#searchResultAPI').find('table:eq(0)').find('tbody').html('');
		}

		function initSidoCode(){
			$("select[name='sidocode']").find('option:gt(0)').remove();
			initSigunguname();
			$.post('/lib/api.php',{'apiname':'roadzip_N','method':'getsido'},
			function(data){
				var $emsg = $(data).find('msg:eq(0)').text();
				if($.trim($emsg).length){
					alert($emsg);
				}else{
					var $rst = $(data).find('result');
					var $cnt = $($rst).attr('itemcount');
					var $itm = $($rst).find('item');
					var target = $("select[name='sidocode']");			
					$(target).find('option:eq(0)').html('선택해주세요');
					$($itm).each(function(idx,opt){
						$(target).append('<option value="'+$(opt).find('code').text()+'">'+$(opt).find('name').text()+'</option>');
					});
				}
			 }
			,"xml").done(function(){}
			).fail(function(){ alert('api 연동 부분에 오류가 있습니다. (3)');});
		}

		function searchMenu(idx){
			stopApi();
			initResult();
			if(idx.length>0) {alert("잘못된 접근입니다.");return;}
			var menuobj = $('#menu_wrap > a');
			var menulength = menuobj.length;
			for(i = 0; i<menulength;i++){
				if(i==idx)menuobj.eq(i).attr('class','black');
				else menuobj.eq(i).attr('class','white');
			}
			var act="", ext="", sn="", st="", mc="", bm="";
			var mh = new Array; 
			if(idx == 0){searchMode=0,sn='<input type="text" name="dongname"  value="" style="width:120px;" /><input type="hidden" name="roadname"  value="" style="width:120px;" />',st='읍/면/동',mh[0] = "번지",mh[1] ="<input type=\"text\" name=\"jibun\" value=\"\" style=\"width:120px;\">";}
			else{	searchMode=1,sn='<input type="text" name="roadname"  value="" style="width:120px;" /><input type="hidden" name="dongname" value="" style="width:120px;" />',st='도로명',mh[0] = "건물번호",mh[1] = "<input type=\"text\" name=\"bldmainnum\" value=\"\" style=\"width:120px;\">";}
			$('#sendFiled').html(sn);
			$('#sendTitle').text(st);
			mc = mh.length;
			for(m=0;m<mc;m++){$('.bildnumber').eq(m).html(mh[m]);}
			
		}

		function callAPI(){
			stopApi();
			findZipAPI();
		}
	</script>
</body>
</html>