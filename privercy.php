<? 
	include_once('header.php');
	
	$sql="SELECT shopname,info_tel,privercyname,privercyemail FROM tblshopinfo ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$shopname=$row->shopname;
		$privercytel=$row->info_tel;
		$privercyname=$row->privercyname;
		$privercyemail=$row->privercyemail;
		mysql_free_result($result);
	} else {
		exit;
	}

	$sql="SELECT privercy FROM tbldesign ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$privercy_exp = @explode("=", $row->privercy);
		$privercybody=$privercy_exp[0];
	}
	mysql_free_result($result);

	if(strlen($privercybody)==0) {
		$fp=fopen($Dir.AdminDir."privercy.txt", "r");
		$privercybody=fread($fp,filesize($Dir.AdminDir."privercy.txt"));
		fclose($fp);
	}

	$pattern=array("(\[SHOP\])","(\[NAME\])","(\[EMAIL\])","(\[TEL\])");
	$replace=array($shopname,$privercyname,"<a href=\"mailto:".$privercyemail."\">".$privercyemail."</a>",$privercytel);
	$privercybody = preg_replace($pattern,$replace,$privercybody);

	if(strlen($privercybody) <= 0){
		echo '<script>alert("개인정보취급방침 설정이 되어 있지 않습니다.");history.go(-1);</script>';exit;
	}
?>
<style>
	.sec_agreement_wrap div{
		margin: 20px;
	}
	div span{
		font-weight: 500;
		color: black;
	}
	div p{
		margin: 15px;
	}
	table{
		text-align: start;
		border: solid 1px;
		margin: 10px;
	}
	#sec_agreement_wrap .stitle{
		font-weight: 500;
		color: black;
	}
	#sec_agreement_wrap div{
		margin: 20px;
	}
	
</style>
<div id="content">
	<div class="h_area2">
		<h2>개인정보취급방침</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	
	<section id="sec_agreement_wrap">
		<div>
			<p>동네꽃집 서비스(이하 ‘서비스’라 한다)를 운영하는 (주)디어플로리스트(이하 ‘회사’라 한다)는 개인정보보호법 등 관련 법령에 따라 이용자의 개인정보를 보호하고, 이와 관련한 고충을 신속하고 원활하게 처리할 수 있도록 다음과 같이 개인정보 처리방침을 수립하여 공개합니다.</p>
			<br>
		</div>
		<div>
			<p class="stitle">1. 개인정보의 수집·이용</p>
			<p>(1) 회사는 회원님의 서비스 이용과정에서 다음과 같이 개인정보를 수집합니다.</p>
			<p>- 회원가입 시 : 이메일 주소, 비밀번호, 이름, 성별, 생년월일, 휴대전화번호<br> - 주문 및 배송 요청 시 : 주문자 정보(이름, 연락처, 이메일주소) 및 배송정보(수령인 이름, 배송지 주소, 휴대전화번호,)</p><br>
			<p>(2) 서비스 이용과정에서 아래 정보가 자동 생성되어 수집, 저장, 조합, 분석될 수 있습니다.</p><p>- IP Address, 쿠키, 방문기록, 서비스 이용기록, 기기정보(기기고유번호, OS, 버전, 모델명 등)</p>
		</div>
		<div>
			<p class="stitle">2. 개인정보의 수집·이용 목적</p>
			<p>회사는 서비스 제공을 위하여 수집한 모든 개인정보와 생성정보를 아래의 목적으로 이용합니다.</p>
			<p>- 회원제 서비스 제공, 회원 식별, 휴대폰 본인인증, 회원관리<br> - 서비스 제공, 서비스 개선, 신규 서비스 개발, 맞춤 서비스 제공<br> - 주문 및 배송서비스 제공<br> - 문의 상담 및 불만 처리<br> - 불법 및 부정이용 방지(부정거래 기록 확인)<br> - 서비스 방문 및 이용기록 통계 및 분석<br> - 서비스 만족도 조사 및 관리<br> - 고지사항 전달</p>
		</div>
		<div>
			<p class="stitle">3. 개인정보의 제 3자 제공</p>
			<p>회사는 이용자의 개인정보를 수집·이용 목적의 범위 내에서 사용하며, 이용자의 사전 동의 없이는 동 범위를 초과하여 이용하거나 원칙적으로 이용자의 개인정보를 외부에 제공하지 않습니다. 다만, 아래의 경우에는 예외로 합니다.</p>
			<p>① 이용자가 사전에 동의 한 경우<br>② 법령의 규정에 의거하거나, 수사 목적으로 법령에 정해진 절차와 방법에 따라 수사기관의 요구가 있는 경우</p>
		</div>
		<div>
			<p class="stitle">4. 개인정보의 처리위탁</p>
			<p>(1) 회사는 서비스 향상을 위해 아래와 같이 이용자의 개인정보를 위탁하고 있으며, 관계 법령에 따라 위탁계약 시 개인정보가 안전하게 관리될 수 있도록 필요한 사항을 규정하고 있습니다.</p>
			<p>(2) 회사가 이용자의 개인정보를 위탁하는 업체 및 업무 내용은 아래와 같습니다.</p>
			<table>
				<tbody>
					<tr>
						<th>수탁업체</th>
						<th>위탁업무의 내용</th>
						<th>수집항목 </th>
						<th>보유기간  </th>
					</tr>
					<tr>
						<td>나이스페이먼츠 주식회사</td>
						<td>신용카드, 실시간계좌이체 결제대행</td>
						<td>주문정보</td>
						<td>회원 탈퇴 시 혹은 법정 보유기간</td>
					</tr>
					<tr>
						<td>(주)루나소프트
							<br>개인정보제공처 : 주식회사 카카오 
						<td>카카오톡 알림톡(정보성메시지) 발송</td>
						<td>휴대폰번호, 주문정보, 배송정보</td>
						<td>회원 탈퇴 시 혹은 법정 보유기간</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div>
			<p class="stitle">5. 개인정보의 보유 및 이용 기간, 개인정보의 파기절차 및 방법</p>
			<p>(1) 회사는 원칙적으로 이용자의 개인정보를 수집·이용 목적을 달성한 경우, 개인정보의 보유 및 이용기간이 끝난 경우 지체없이 해당 개인정보를 파기합니다.</p>
			<p>(2) 이용자로부터 동의 받은 개인정보 보유 기간이 경과하거나 처리 목적이 달성되었음에도 불구하고 다른 법령 또는 회사 내부정책에 따라 개인정보를 일정기간 보존하여야 하는 경우에는 개인정보를 별도의 데이터베이스(DB)로 옮기거나 보관장소를 달리하여 보존합니다.</p>
		</div>
		<div>
			<p class="stitle">가. 법령에 의하여 수집·이용되는 이용자의 정보</p>
			<table>
				<tbody>
					<tr>
						<th>법령</th>
						<th>보유·이용목적</th>
					</tr>
					<tr>
						<td>통신비밀보호법</td>
						<td>법원의 영장을 받아 수사기관이 요청 시 제공</td>
					</tr>
					<tr>
						<td rowspan="\&quot;4\&quot;">전자상거래 등에서의 소비자 보호에 관한 법률</td>
						<td>표시·광고에 관한 기록</td>
					</tr>
					<tr>
						<td>대금결제 및 재화 등의 공급에 관한 기록</td>
					</tr>
					<tr>
						<td>계약 또는 청약철회 등에 관한 기록</td>
					</tr>
					<tr>
						<td>소비자 불만 또는 분쟁처리에 관한 기록</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div>
			<p>(3) 회사는 [개인정보보호법 제 39조의 6]에 근거하여 1년 동안 회사의 서비스를 이용하지 않은 이용자의 개인정보를 이용자에게 사전통지하고 개인정보를 파기하거나 별도로 분리하여 저장합니다.</p>
			<p>* 회사는 개인정보가 파기되거나 분리되어 저장•관리된다는 사실, 서비스 미이용 기간 만료일, 해당 개인정보의 항목을 공지사항, 전자우편 등의 방법으로 미이용 기간 30일 전에 이용자에게 알립니다. 이를 위해 이용자는 회사에게 정확한 연락처 정보를 알리거나 수정하여야 합니다.</p>
		</div>
		<div>
			<p>(4) 개인정보의 파기절차 및 방법은 다음과 같습니다.</p>
				<p>① 파기절차
				<br>회사는 파기 사유가 발생한 시점부터 별도 지정한 보유·이용기간이 지난 시점에 지체없이 파기합니다.
				<br>② 파기방법
				<br>회사는 전자적 파일형태로 기록·저장된 개인정보는 기록을 재생할 수 없도록 기술적인 방법 또는 물리적인 방법을 이용하여 파기하며, 종이에 출력된 개인정보는 분쇄기로 분쇄하거나 소각 등을 통하여 파기합니다.
			</p>
		</div>
		<div>
			<p class="stitle">6. 아동의 개인정보보호</p>
			<p>회사는 아동의 개인정보를 보호하기 위하여 만 14세 미만 아동의 개인정보를 수집하지 않습니다.</p>
		</div>
		<div>
			<p class="stitle">이용자의 권리와 그 행사방법, 이용자의 의무</p>
			<p>(1) 이용자가 직접 자신의 개인정보를 조회, 수정, 삭제하는 것을 원칙으로 하며, 회사는 이를 위한 기능을 제공합니다.
				<br>(2) 이용자 및 법정대리인은 개인정보의 조회, 수정, 삭제를 요청할 수 있으며, 회사는 정책에 따라 본인확인 절차를 거쳐 이를 조치하겠습니다.
				<br>(3) 이용자가 개인정보의 오류 정정을 요청한 경우에는 정정을 완료하기 전까지 회사는 당해 개인정보를 이용 또는 제공하지 않습니다. 또한, 회사는 잘못된 개인정보를 제3자에게 이미 제공한 경우에는 제3자에게 지체 없이 정정 처리요청을 하겠습니다.
				<br>(4) 이용자는 자신의 개인정보를 최신의 상태로 유지해야 하며, 이용자의 부정확한 정보 입력으로 발생하는 문제의 책임은 이용자 자신에게 있습니다.
				<br>(5) 타인의 개인정보를 도용한 회원가입의 경우 이용자 자격을 상실하거나 개인정보보호 관련 법령에 의해 처벌 받을 수 있습니다.
				<br>(6) 이용자는 아이디, 비밀번호, 전자우편 등 이용자의 개인정보에 대한 보안을 유지할 책임이 있으며 제3자에게 이를 양도하거나 대여 할 수 없습니다. 이용자가 이용자의 개인정보를 제3자에게 양도하거나 대여하여 발생하는 문제의 책임은 이용자에게 있습니다.
			</p>
		</div>
		<div>
			<p class="stitle">8. 개인정보 자동 수집 장치의 설치/운영 및 거부에 관한 사항</p>
			<p>(1) 쿠키란
				<br>① 회사는 개인화되고 맞춤화된 서비스를 제공하기 위해서 이용자의 정보를 저장하고 수시로 불러오는 ‘쿠키(cookie)’를 사용합니다.
				<br>② 쿠키는 웹사이트를 운영하는데 이용되는 서버가 이용자의 브라우저에게 보내는 아주 작은 텍스트 파일로 이용자 컴퓨터의 하드디스크에 저장됩니다.
				<br>③ 이후 이용자가 웹 사이트에 방문할 경우 웹 사이트 서버는 이용자의 하드 디스크에 저장되어 있는 쿠키의 내용을 읽어 이용자의 환경설정을 유지하고 맞춤화된 서비스를 제공하기 위해 이용됩니다.
				<br>④ 쿠키는 개인을 식별하는 정보를 자동적/능동적으로 수집하지 않으며, 이용자는 언제든지 이러한 쿠키의 저장을 거부하거나 삭제할 수 있습니다.
				<br>
				<br>(2) 쿠키의 사용 목적
				<br>이용자들이 방문한 회사의 각 서비스와 웹 사이트들에 대한 방문 및 이용형태, 인기 검색어, 이용자 규모 등을 파악하여 이용자에게 광고를 포함한 최적화된 맞춤형 정보를 제공을 위해 사용합니다.
				<br>
				<br>(3) 쿠키의 설치/운영 및 거부
				<br>① 이용자는 쿠키 설치에 대한 선택권을 가지고 있습니다. 따라서 이용자는 웹브라우저에서 옵션을 설정함으로써 모든 쿠키를 허용하거나, 쿠키가 저장될 때마다 확인을 거치거나, 아니면 모든 쿠키의 저장을 거부할 수도 있습니다.
				<br>② 다만, 쿠키의 저장을 거부할 경우에는 로그인이 필요한 회사의 일부 서비스는 이용에 어려움이 있을 수 있습니다.
				<br>③ 쿠키 설치 허용 여부를 지정하는 방법(Internet Explorer의 경우)은 다음과 같습니다. 
				<br>가. [도구] 메뉴에서 [인터넷 옵션]을 선택합니다.
				<br>나. [개인정보 탭]을 클릭합니다.
				<br>다. [개인정보처리 수준]을 설정하시면 됩니다.
				<br>(4) 웹로그 분석
				<br>① 회사는 웹로그 분석도구인 Google Analytics를 통해 이용자의 서비스 이용형태(이동, 클릭, 전환 등)를 수집하고 분석합니다.
				<br>② 웹로그 분석을 중단하고 싶으신 경우, 아래 안내페이지 내 설정을 통해 차단할 수 있습니다.
				<br>- Google Analytics 차단 설정 안내 (링크연결-https://tools.google.com/dlpage/gaoptout/)
			</p>
		</div>
		<div>
			<p class="stitle">9. 개인정보의 기술적·관리적 보호대책</p>
			<p>회사는 이용자의 개인정보를 처리함에 있어 개인정보가 분실, 도난, 유출, 변조, 훼손 등이 되지 아니하도록 안전성을 확보하기 위하여 다음과 같이 기술적·관리적 보호대책을 강구하고 있습니다.</p>
			<p>(1) 중요 개인정보의 암호화
			<br>이용자의 비밀번호는 일방향 암호화하여 저장 및 관리되고 있으며, 개인정보의 확인, 변경은 비밀번호를 알고 있는 본인에 의해서만 가능합니다. 이용자의 계좌번호 등 금융정보는 강력한 양방향 암호알고리즘을 적용하여 암호화하여 저장 및 관리되고 있습니다.
			<br>(2) 해킹 등에 대비한 대책
			<br>① 회사는 해킹, 컴퓨터 바이러스 등 정보통신망 침입에 의해 이용자의 개인정보가 유출되거나 훼손되는 것을 막기 위해 최선을 다하고 있습니다.
			<br>② 최신 바이러스 백신프로그램을 이용하여 바이러스 감염에 의해 이용자의 개인정보나 자료가 유출되거나 손상되지 않도록 방지하고 있습니다.
			<br>③ 개인정보에 대한 불법적인 접근을 차단하기 위한 침입차단시스템 등 접근 통제장치를 설치·운영하고 있습니다.
			<br>④ 민감한 개인정보는 암호화 통신 등을 통하여 네트워크상에서 개인정보를 안전하게 전송할 수 있도록 하고 있습니다.
			<br>
			<br>(3) 개인정보 처리 최소화 및 교육
			<br>회사는 개인정보 관련 처리 담당자를 최소한으로 제한하며, 개인정보 취급자(처리자)에 대한 교육 등 관리적 조치를 통해 법령 및 내부방침 등의 준수를 강조하고 있습니다.
			<br>(4) 개인정보보호 담당부서 운영
			<br>회사는 이용자의 개인정보 보호를 위해 개인정보보호 담당부서를 운영하고 있으며, 개인정보 처리방침의 이행사항 및 처리 담당자의 법령 준수여부를 확인하여 문제가 발견될 경우 즉시 해결하고 바로 잡을 수 있도록 최선을 다하고 있습니다.
			</p>
		</div>
		<div>
			<p class="stitle">10. 개인정보 보호책임자</p>
			<p><br>(1) 회사는 개인정보 처리에 관한 업무를 총괄해서 책임지고, 개인정보 처리와 관련한 이용자의 불만처리 및 피해구제 등을 위하여 아래와 같이 개인정보보호책임자 및 담당부서를 지정하고 있습니다.
			<br>• 개인정보 보호책임자: 김성식
			<br>• 담당부서: 정보보안팀 
			<br>• 연락처 : 010-4104-3231
			<br>• 전자우편 : dearflorist.ss@gmail.com
			<br>(2) 이용자는 회사의 서비스(또는 사업)을 이용하시면서 발생한 모든 개인정보와 관련된 문의, 불만처리, 피해구제 등에 관한 사항을 개인정보보호책임자 및 담당부서에 문의하실 수 있습니다. 회사는 이용자의 문의에 대해 지체 없이 답변 및 처리하겠습니다.
			</p>
		</div>	
		<div>
			<p class="stitle">11. 기타 개인정보 침해에 대한 신고 및 상담</p>
			<p>이용자는 아래의 기관에 대해 개인정보 침해에 대한 피해구제, 상담 등을 문의할 수 있습니다. 아래의 기관은 정부기관 소속으로서, 회사의 자체적인 개인정보 불만처리 또는 개인정보 피해구제 결과에 만족하지 못할 경우, 자세한 도움이 필요할 경우에 문의하여 주시기 바랍니다. <br>
			1.개인분쟁조정위원회 (<a href="http://www.1336.or.kr" target="_blank" class="docs-creator">http://www.1336.or.kr/1336</a>) <br>
			2.정보보호마크인증위원회 (<a href="http://www.eprivacy.or.kr" target="_blank" class="docs-creator">http://www.eprivacy.or.kr/02-580-0533~4</a>) <br>
			3.대검찰청 인터넷범죄수사센터 (<a href="http://icic.sppo.go.kr" target="_blank" class="docs-creator">http://icic.sppo.go.kr/02-3480-3600</a>) <br>
		</div>
	</section>
</div>
<? 
	include_once('footer.php'); 
?>