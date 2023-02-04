<?
    /* ============================================================================== */
    /* =   PAGE : 취소 요청 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   아래의 ※ 주의 ※ 부분을 꼭 참고하시여 연동을 진행하시기 바랍니다.       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do			        = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>*** [AX-HUB Version] ***</title>
    <meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
    <link href="css/style.css" rel="stylesheet" type="text/css"/>

    <script type="text/javascript">
    /* 취소 버튼을 눌렀을 때 호출 */
    function  jsf__go_cancel( form )
    {
        var RetVal = false ;
        if ( form.tno.value.length < 14 )
        {
            alert( "KCP 거래 번호를 입력하세요" );
            form.tno.focus();
            form.tno.select();
        }
        else
        {
            openwin = window.open( "proc_win.html", "proc_win", "width=449, height=209, top=300, left=300" );
            RetVal = true ;
        }
        return RetVal ;
    }
    </script>
</head>

<body>

    <div id="sample_wrap">
<?
    /* ============================================================================== */
    /* =   1. 취소 요청 정보 입력 폼(cancel_info)                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   취소 요청에 필요한 정보를 설정합니다.                                    = */
    /* = -------------------------------------------------------------------------- = */
?>
    <form name="cancel_info" method="post" action="pp_ax_hub.php">

                 <!-- 타이틀 Start-->
                    <h1>[취소요청] <span>이 페이지는 결제건에 대해 취소를 요청하는 샘플(예시) 페이지입니다.</span></h1>
                 <!-- 타이틀 End -->

                    <!-- 상단 테이블 Start -->
                    <div class="sample">
                    <p>
                        소스 수정 시 소스 안에 <span>※ 주의 ※</span> 표시가 포함된 문장은 가맹점의 상황에 맞게 적절히 수정 <br/>
                        적용하시길 바랍니다.<br/>
                        <span>이 페이지는 결제된 건에 대한 취소를 요청하는 페이지 입니다.</span><br/>
                        결제가 승인되면 결과값으로 KCP 거래번호(tno)값을 받으실 수 있습니다.<br/>
                        가맹점에서는 KCP 거래번호(tno)값으로 취소요청을 하실 수 있습니다.
                    </p>
                    <!-- 상단 테이블 End -->
                <!-- 취소 요청 정보 입력 테이블 Start -->
                    <h2>&sdot; 취소 요청</h2>
                    <table class="tbl" cellpadding="0" cellspacing="0">
                    <!-- 요청 구분 : 취소 -->
                    <tr>
                        <th>요청 구분</th>
                        <td>
                            <select name='mod_type' onChange="jsf__chk_mod_type()">
                                <option value='mod_type_not_sel' selected>선택하십시오</option>
                                <option value='STSC'>취소</option>
                                <option value='STPC'>부분취소</option>
                            </select>
                            <br/><b>※ 부분취소는 신용카드, 계좌이체만 가능합니다. ※</b>
                        </td>
                    </tr>
                    <!-- Input : 결제된 건의 거래번호(14 byte) 입력 -->
                    <tr>
                        <th>KCP 거래번호</th>
                        <td><input type="text" name="tno" value=""  class="w200" maxlength="14"/></td>
                    </tr>
                     <!-- Input : 변경 사유(mod_desc) 입력 -->
                    <tr>
                        <th>변경 사유</th>
                        <td><input type="text" name="mod_desc" value="" class="w200" maxlength="50"/></td>
                    </tr>
                     <!-- Input : 취소 요청 금액(mod_mny) 입력 -->
                    <tr>
                        <th>취소 요청 금액<br/>(부분취소시 사용)</th>
                        <td>
                            <input type='text' name='mod_mny' value='' class="w200" size='20' maxlength='14'>
                        </td>
                    </tr>
                     <!-- Input : 취소 가능 금액(rem_mny) 입력 -->
                    <tr>
                        <th>취소 가능 잔액<br/>(부분취소시 사용)</th>
                        <td>
                            <input type='text' name='rem_mny' value='' class="w200" size='20' maxlength='14'>
                        </td>
                    </tr>

                    <% /*
                    <!-- 복합과세 부분취소 설정시 주석을 풀어 주시기 바랍니다 -->
                    <!-- 공급가 부분취소 요청 금액 -->
                    <tr>
                        <th>공급가 부분취소 금액</th>
                        <td>
                            <input type="text" name="mod_tax_mny" value="" class="w200" size="20" maxlength="9"/>
                        </td>
                    </tr>
                    <!-- 부가세 부분취소 요청 금액  -->
                    <tr>
                        <th>부가세 부분취소 금액</th>
                        <td>
                            <input type="text" name="mod_vat_mny" value="" class="w200" size="20" maxlength="9"/>
                        </td>
                    </tr>
                    <!-- 비과세 부분취소 요청 금액 -->
                    <tr>
                        <th>비과세 부분취소 금액</th>
                        <td>
                            <input type="text" name="mod_free_mny" value="" class="w200" size="20" maxlength="9"/>
                        </td>
                    </tr>
                    */ %>

                    </table>

                <!-- 취소 요청 정보 입력 테이블 End -->

                    <!-- 결제 버튼 테이블 Start -->
                    <div class="btnset">
                    <input name="" type="submit" class="submit" value="취소요청" onclick="return jsf__go_cancel(this.form);"/>
                    <a href="../index.html" class="home">처음으로</a>
                    </div>
                    <!-- 결제 버튼 테이블 End -->
                </div>
            <div class="footer">
                Copyright (c) KCP INC. All Rights reserved.
            </div>

<?
    /* ============================================================================== */
    /* =   1-1. 취소 요청 필수 정보 설정                                            = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ※ 필수 - 반드시 필요한 정보입니다.                                      = */
    /* = -------------------------------------------------------------------------- = */
?>
        <input type="hidden" name="req_tx"   value="mod"  />
    </form>
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   1. 취소 요청 정보 END                                                    = */
    /* ============================================================================== */
?>
</div>
</body>
</html>
