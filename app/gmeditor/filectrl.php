<?php
$ej_upload_temp		=	"data/editor_temp";	#	�̹��� ���ε� ��� - ������
$ej_limit_size		=	5120;				#	�̹��� ���ε� �뷮 ����
// ���� Ȯ����
function aChimFileExt($filename) {
	return substr(strrchr(strtolower($filename),"."),1);
}
switch($_POST['ctrlAct']){
	
	default:
		@exec("find ".$_SERVER["DOCUMENT_ROOT"]."/".$ej_upload_temp."/ -mtime +1 -exec rm -f {} \;");
		if(!empty($_POST['preview'])) {
			if(is_uploaded_file($_FILES['ej_edt_file']['tmp_name'])) {
				$gFileLimitSize		=	$ej_limit_size * 1024;
				if($_FILES['ej_edt_file']['size'] <= $gFileLimitSize) {
					$ext			=	aChimFileExt($_FILES['ej_edt_file']['name']);
					$saveFiles		=	time().md5(uniqid(mt_rand(0, 1000))).".".$ext;
					@move_uploaded_file($_FILES['ej_edt_file']['tmp_name'], $_SERVER["DOCUMENT_ROOT"]."/".$ej_upload_temp."/".$saveFiles);
					@copy($_FILES['ej_edt_file']['tmp_name'], $_SERVER["DOCUMENT_ROOT"]."/".$ej_upload_temp."/".$saveFiles);
					@unlink($_FILES['ej_edt_file']);
					$elink			=	$ej_upload_temp."/".$saveFiles;
				?>
		<script type="text/javascript">
				<!--
				parent.document.getElementById("<?=$_POST['preview'];?>").innerHTML	=	'<img src="/<?=$elink;?>" style="width:80px; height:80px;" border="0" alt="����� ��� �̹���" />';
				//-->
				</script>
				<?
						echo "success";
				}
				else	echo "filesize";
			}
			else		echo "nonefile";
		}
		else			echo "nonetarget";
		break;
}
?>