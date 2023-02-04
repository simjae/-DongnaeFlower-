<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	exit;
}
$deliAbleArea = $_REQUEST["deliAbleArea"];
if(strlen($deliAbleArea)==0) {
	$deliAbleArea="전국";
}
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=420, user-scalable=no" />

	<title>과거 배송지 검색</title>

	<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
	<link rel="stylesheet" type="text/css" href="<?=$Dir?>css/common.css">
	<link rel="stylesheet" type="text/css" href="<?=$Dir?>css/basket.css">

	<SCRIPT LANGUAGE="JavaScript">
		<!--

		function choice_addr(name,tel1,tel2,post,addr1,addr2) {
			if(post.length != 5){
				alert('해당 주소지는 우편번호가 갱신되지 않아 사용할 수 없습니다.');
				return;
			}
			parent.addAddrDetail(name,tel1,tel2,post,addr1,addr2);

			parent.ReceiverClose();
		}
		function disable_addr() {
			alert('배송이 불가능한 주소입니다.\n[<?=$deliAbleArea?>]지역 배송이 가능합니다.');
			return;
		}
		//-->
	</SCRIPT>

	<style>
		html,body{margin:0;padding:0;font-family: 'Spoqa Han Sans Neo';}
		div,table,tr,td,ul,li,p,span,h1{color:inherit;font-size:inherit;}
		a{color:inherit;font-size:inherit;}
		.h_area2 {
			display: block;
			position: relative;
			border-bottom:1px solid #9e9e9e66;
		}
		.h_area2 h2 {
			display: block;
			text-align: center;
			font-size: 1.2em;
			padding: 20px;
			line-height: 1.2em;
			color:#000000
		}
		.itemList td{border-bottom:1px solid #9e9e9e36;}
		.adrBox{
			margin:14px 14px 4px 14px;
			width:auto;
			border-radius: 10px;
			border:solid #b9b8b8 1px;
			overflow:hidden;
			box-shadow: 3px 3px 5px 0px RGBA(0,0,0,0.05);
		}
		.adrBox .content01{
			display: flex;
			justify-content: space-between;
			margin: 20px;
		}
		.adrBox .content01 .textBox .title{
			font-weight:600;
			font-size:1.1em;
			line-height: 1.1em;
			padding: 5px 0px 0px 0px;
			color: #1b1c1d;
		}
	</style>
</HEAD>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
	<div class="h_area2">
		<h2>배송지 선택</h2>
	</div>
	<div class="adrBox" onclick="location.href='/app/mypage_delivery_modal.php?type=insert&deliAbleArea=<?=$deliAbleArea?>'">
		<div class="content01">
			<div class="imageBox">
				<img src="/app/skin/basic/svg/adr_add.svg" style="width: 20px;">
			</div>
			<div class="textBox" style="position: relative;right: 60px;">
				<div class="title">
					새로운 배송 주소 추가 
				</div>
			</div>
			<div class="imageBox" style="position: relative;top: 2px;">
				<img src="/app/skin/basic/svg/adr_arrow.svg" style="width: 12px;">
			</div>
		</div>
	</div>
	<table cellpadding="0" cellspacing="0" width="92%" class="itemList" style="margin:10px auto;">
		<?
			$sql="SELECT * FROM tblorderreceiver WHERE member_id='".$_ShopInfo->getMemid()."' ORDER BY idx DESC ";
			$result=mysql_query($sql,get_db_conn());
			$cnt=0;
			while($row=mysql_fetch_object($result)) {
				$nick=$row->receiver_nick;
				$name=$row->receiver_name;
				$email=$row->receiver_email;
				$post=$row->receiver_post;

				$receiver_tel_temp=explode("-",$row->receiver_tel1);
				$tel11=$receiver_tel_temp[0];
				$tel12=$receiver_tel_temp[1];
				$tel13=$receiver_tel_temp[2];

				$receiver_tel2_temp=explode("-",$row->receiver_tel2);
				$tel21=$receiver_tel2_temp[0];
				$tel22=$receiver_tel2_temp[1];
				$tel23=$receiver_tel2_temp[2];

				$receiver_addr_temp=explode("=",$row->receiver_addr);
				$receiver_addr1=$receiver_addr_temp[0];
				$receiver_addr2=$receiver_addr_temp[1];
				
				$actScript = "disable_addr();";

				if(strpos( $deliAbleArea, "전국" ) !== false){
					$actScript = "choice_addr('".$name."','".$tel11."-".$tel12."-".$tel13."','".$tel21.$tel22.$tel23."','".$post."','".$receiver_addr1."','".$receiver_addr2."');";
				}
				else{
					$data = explode( ",", $deliAbleArea );
					$i = 0;
					for ( ; $i < sizeof( $data ); ++$i )
					{
						if(strpos( $receiver_addr1, trim($data[$i])) !== false){
							$actScript = "choice_addr('".$name."','".$tel11."-".$tel12."-".$tel13."','".$tel21.$tel22.$tel23."','".$post."','".$receiver_addr1."','".$receiver_addr2."');";
						//	break;
						}
					}
				}
			?>
				<tr>
					<td style="display: flex;padding: 25px 0px 25px 0px;" onclick="choice_addr('<?=$name?>','<?=$tel11.$tel12.$tel13?>','<?=$tel21.$tel22.$tel23?>','<?=$post?>','<?=$receiver_addr1?>','<?=$receiver_addr2?>')">
						<div style="padding:0px 18px 0px 17px;">
							<img src="/app/skin/basic/svg/adr_list.svg" style="width: 20px;">
						</div>
						<div>
							<p class="title" style="padding: 0px; font-size:1.1em; font-weight: 600; color:#1b1c1d"><?="".$nick." "?></p>
							<p class="title" style="margin-top: 5px; color:#777777"><?=$receiver_addr1?> <?=$receiver_addr2?></p>
							<p class="writer" style="padding: 0px; color:#aaaaaa""><?=$name?> <?=$tel11?> <?=$tel12?> <?=$tel13?></p>
						</div>
					</td>
				</tr>
			<?
				$cnt++;
			}
			mysql_free_result($result);

			if($cnt==0){
		?>
		
				<tr>
					<td height="30" colspan="3" align="center">등록된 배송지가 없습니다.</td>
				</tr>
		<?
			}
		?>
	</table>
</body>
</html>