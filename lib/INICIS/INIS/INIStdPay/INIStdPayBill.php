<?php
require_once('../libs/INIStdPayUtil.php');
$SignatureUtil = new INIStdPayUtil();


//############################################
// 1.전문 필드 값 설정(***가맹점 개발수정***)
//############################################
// 여기에 설정된 값은 Form 필드에 동일한 값으로 설정
$mid 			= "INIBillTst";  								// 가맹점 ID(가맹점 수정후 고정)					
//$mid 			= "edutok2kr1";
//인증
$signKey 		= "SU5JTElURV9UUklQTEVERVNfS0VZU1RS"; 			// 가맹점에 제공된 키(이니라이트키) (가맹점 수정후 고정) !!!절대!! 전문 데이터로 설정금지
//$signKey 		= "OXVWV3hua2taZnNlaG1NNm1nUkRCdz09";
$timestamp 		= $SignatureUtil->getTimestamp();   			// util에 의해서 자동생성
$orderNumber 	= $mid . "_" . $timestamp; 						// 가맹점 주문번호(가맹점에서 직접 설정)
$price 			= "1000";        								// 상품가격(특수기호 제외, 가맹점에서 직접 설정)

//
//###################################
// 2. 가맹점 확인을 위한 signKey를 해시값으로 변경 (SHA-256방식 사용)
//###################################
$mKey 					= $SignatureUtil->makeHash($signKey, "sha256");

/*
 **** 위변조 방지체크를 signature 생성 ***
 * oid, price, timestamp 3개의 키와 값을
 * key=value 형식으로 하여 '&'로 연결한 하여 SHA-256 Hash로 생성 된값
 * ex) oid=INIpayTest_1432813606995&price=819000&timestamp=2012-02-01 09:19:04.004
 * key기준 알파벳 정렬
 * timestamp는 반드시 signature생성에 사용한 timestamp 값을 timestamp input에 그데로 사용하여야함
 */
$params = array(
    "oid" => $orderNumber,
    "price" => $price,
    "timestamp" => $timestamp
);

$sign		= $SignatureUtil->makeSignature($params);

$http_host 	= $_SERVER['HTTP_HOST'];

/* 기타 */
$siteDomain = "http://".$_SERVER['HTTP_HOST']."/stdpay/INIStdPaySample"; //가맹점 도메인 입력

// 페이지 URL에서 고정된 부분을 적는다. 
// Ex) returnURL이 http://localhost:8082/demo/INIpayStdSample/INIStdPayReturn.jsp 라면
//                 http://localhost:8082/demo/INIpayStdSample 까지만 기입한다.
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>INIStd 웹표준결제</title>
        <style type="text/css">
            body { background-color: #efefef;}
            body, tr, td {font-size:9pt; font-family:굴림,verdana; color:#433F37; line-height:19px;}
            table, img {border:none}
        </style>

        <!-- 이니시스 표준결제 js -->
         <script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
<!--        <script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script> -->

        <script type="text/javascript">
            function paybtn() {
                INIStdPay.pay('SendPayForm_id');
            }
			
			function cardShow(){
				document.getElementById("acceptmethod").value = "BILLAUTH(card):FULLVERIFY";
			}

			function hppShow(){
				document.getElementById("acceptmethod").value = "BILLAUTH(HPP):HPP(1)";
			}
        </script>
    </head>
    <body bgcolor="#FFFFFF" text="#242424" leftmargin=0 topmargin=15 marginwidth=0 marginheight=0 bottommargin=0 rightmargin=0>
        <div style="padding:10px;background-color:#f3f3f3;width:100%;font-size:13px;color: #ffffff;background-color: #000000;text-align: center">
            이니시스 표준결제 신용카드 빌링키 발급 샘플
        </div>
        <table width="650" border="0" cellspacing="0" cellpadding="0" style="padding:10px;" align="center">
            <tr>
                <td bgcolor="6095BC" align="center" style="padding:10px">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" style="padding:20px">
                        <tr>
                            <td>이 페이지는 INIpay Standard 결제요청을 위한 예시입니다.<br/><br/>
	                                결제처리를 위한 action등의 모든 동작은 Import 된 스크립트에 의해 자동처리됩니다.<br/><br/>
	                            Form에 설정된 모든 필드의 name은 대소문자 구분하며,<br/>
	                                이 Sample은 결제를 위해서 설정된 Form은 테스트 / 이해돕기를 위해서 모두 type="text"로 설정되어 있습니다.<br/>
	                                운영에 적용시에는 일부 가맹점에서 필요에 의해 사용자가 변경하는 경우를 제외하고<br/>
	                                모두 type="hidden"으로 변경하여 사용하시기 바랍니다.<br/><br/>
                                <font color="#336699"><strong>함께 제공되는 메뉴얼을 참조하여 작성 개발하시기 바랍니다.</strong></font>
                                <br/><br/>
                            </td>
                        </tr>
						<tr>
                            <td>
								<input type="radio" id="card" name="payTypeChk" value="card" onclick="cardShow()" checked="checked">카드빌링
								<input type="radio" id="hpp"  name="payTypeChk" value="hpp"  onclick="hppShow()" >휴대폰빌링
							</td>
                        </tr>
                        <tr>
                            <td >
                                <button onclick="paybtn()" style="padding:10px">결제요청</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table>
                                    <tr>
                                        <td style="text-align:left;">
                                            <form id="SendPayForm_id" name="" method="POST" >
                                                <!-- 필수 -->
                                                <br/><b>***** 필 수 *****</b>
                                                <div style="border:2px #dddddd double;padding:10px;background-color:#f3f3f3;">
                                                    <br/><b>version</b> :
                                                    <br/><input  style="width:100%;" name="version" value="1.0" >

                                                    <br/><b>mid</b> :
                                                    <br/><input  style="width:100%;" name="mid" value="<?php echo $mid ?>" >

                                                    <br/><b>goodname</b> :
                                                    <br/><input  style="width:100%;" name="goodname" value="테스트" >

                                                    <br/><b>oid</b> :
                                                    <br/><input  style="width:100%;" name="oid" value="<?php echo $orderNumber ?>" >

                                                    <br/><b>price</b> :
                                                    <br/><input  style="width:100%;" name="price" value="<?php echo $price ?>" >

                                                    <br/><b>currency</b> :
                                                    <br/>[WON|USD]
                                                    <br/><input  style="width:100%;" name="currency" value="WON" >

                                                    <br/><b>buyername</b> :
                                                    <br/><input  style="width:100%;" name="buyername" value="홍길동" >

                                                    <br/><b>buyertel</b> :
                                                    <br/><input  style="width:100%;" name="buyertel" value="010-1234-5678" >

                                                    <br/><b>buyeremail</b> :
                                                    <br/><input  style="width:100%;" name="buyeremail" value="test@inicis.com" >

                                                    <!-- <br/><b>timestamp</b> : -->
                                                    <input type="hidden"  style="width:100%;" name="timestamp" value="<?php echo $timestamp ?>" >
                                                    <br/>

                                                    <!-- <br/><b>signature</b> : -->
                                                    <input type="hidden" style="width:100%;" name="signature" value="<?php echo $sign ?>" >
                                                    <br/><b>returnUrl</b> :
                                                    <br/><input  style="width:100%;" name="returnUrl" value="<?php echo $siteDomain ?>/INIStdPayReturn.php" >

                                                    <input type="hidden"  name="mKey" value="<?php echo $mKey ?>" >
                                                </div>

                                                <br/><br/>
                                                <b>***** 기본 옵션 *****</b>
                                                <div style="border:2px #dddddd double;padding:10px;background-color:#f3f3f3;">
                                                    <br/><input type="hidden" style="width:100%;" name="gopaymethod" value="" >
                                                    <b>offerPeriod</b> : 제공기간
                                                    <br/>ex)20150101-20150331, [Y2:년단위결제, M2:월단위결제, yyyyMMdd-yyyyMMdd : 시작일-종료일]
                                                    <br/><input  style="width:100%;" name="offerPeriod" value="20160101-20161231" >
                                                    <br/>
                                                    <br/><b>acceptmethod</b> : 
                                                    <br/>ex)  billauth(card) , billauth(hpp) 
                                                    <br/><input style="width:100%;" id="acceptmethod" name="acceptmethod" value="billauth(card)" >
													<br/>
													<b>결제일 알림 메세지</b> : 결제일 알림 메세지
												<br/><input  style="width:100%;" id="billPrint_msg" name="billPrint_msg" value="고객님의 매월 결제일은 24일 입니다." >
                                                </div>

                                                <br/><br/>
                                                <b>***** 표시 옵션 *****</b>
                                                <div style="border:2px #dddddd double;padding:10px;background-color:#f3f3f3;">
                                                    <br/><b>languageView</b> : 초기 표시 언어
                                                    <br/>[ko|en] (default:ko)
                                                    <br/><input style="width:100%;" name="languageView" value="" >
													<br/>
                                                    <br/><b>charset</b> : 리턴 인코딩
                                                    <br/>[UTF-8|EUC-KR] (default:UTF-8)
                                                    <br/><input style="width:100%;" name="charset" value="" >
													<br/>
                                                    <br/><b>payViewType</b> : 결제창 표시방법
                                                    <br/>[overlay] (default:overlay)
                                                    <br/><input style="width:100%;" name="payViewType" value="" >
													<br/>
                                                    <br/><b>closeUrl</b> : payViewType='overlay','popup'시 취소버튼 클릭시 창닥기 처리 URL(가맹점에 맞게 설정)
                                                    <br/>close.jsp 샘플사용(생략가능, 미설정시 사용자에 의해 취소 버튼 클릭시 인증결과 페이지로 취소 결과를 보냅니다.)
                                                    <br/><input style="width:100%;" name="closeUrl" value="<?php echo $siteDomain ?>/close.php" >
													<br/>
                                                    <br/><b>popupUrl</b> : payViewType='popup'시 팝업을 띄울수 있도록 처리해주는 URL(가맹점에 맞게 설정)
                                                    <br/>popup.jsp 샘플사용(생략가능,payViewType='popup'으로 사용시에는 반드시 설정)
                                                    <br/><input style="width:100%;" name="popupUrl" value="<?php echo $siteDomain ?>/popup.php" >

                                                </div>

                                                <br/><br/>
                                                <b>***** 추가 옵션 *****</b>
                                                <div style="border:2px #dddddd double;padding:10px;background-color:#f3f3f3;">
                                                    <br/><b>merchantData</b> : 가맹점 관리데이터(2000byte)
                                                    <br/>인증결과 리턴시 함께 전달됨
                                                    <br/><input  style="width:100%;" name="merchantData" value="" >
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
