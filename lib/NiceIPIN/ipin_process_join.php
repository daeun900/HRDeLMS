<?
include "../../include/include_function.php"; //DB연결 및 각종 함수 정의


//보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 

	/********************************************************************************************************************************************
		NICE평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
		
		서비스명 : 가상주민번호서비스 (IPIN) 서비스
		페이지명 : 가상주민번호서비스 (IPIN) 사용자 인증 정보 처리 페이지
		
				   수신받은 데이터(인증결과)를 메인화면으로 되돌려주고, close를 하는 역활을 합니다.
	*********************************************************************************************************************************************/
	
	// 인증 결과 데이터를 암호화한 데이타입니다. 
	// ipin_main.php 페이지에서 생성된 암호화 데이타와는 다릅니다.
	$sResponseData = Replace_Check_XSS2($enc_data);
	$sReservedParam1 = Replace_Check_XSS2($param_r1);
	$sReservedParam2 = Replace_Check_XSS2($param_r2);
	$sReservedParam3 = Replace_Check_XSS2($param_r3);


	//$sResponseData = $_GET['enc_data'];
	
	// ipin_main.php 페이지에서 설정한 데이타가 있다면, 아래와 같이 확인가능합니다.
	//$sReservedParam1  = $_GET['param_r1'];
	//$sReservedParam2  = $_GET['param_r2'];
	//$sReservedParam3  = $_GET['param_r3'];
	
	//////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
    if(preg_match('~[^0-9a-zA-Z+/=]~', $sResponseData, $match)) {echo "입력 값 확인이 필요합니다"; exit;}
		if(base64_encode(base64_decode($sResponseData))!= $sResponseData) {echo " 입력 값 확인이 필요합니다"; exit;}	
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam1, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam2, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam3, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// 암호화된 사용자 정보가 존재하는 경우
	if ($sResponseData != "") {

?>

<html>
<head>
	<title>NICE평가정보 가상주민번호 서비스</title>
	<script language='javascript'>
		function fnLoad()
		{
	
			// 당사에서는 최상위를 설정하기 위해 'parent.opener.parent.document.'로 정의하였습니다.
			// 따라서 귀사에 프로세스에 맞게 정의하시기 바랍니다.
			document.vnoform.enc_data.value = "<?= $sResponseData ?>";
			
			document.vnoform.param_r1.value = "<?= $sReservedParam1 ?>";
			document.vnoform.param_r2.value = "<?= $sReservedParam2 ?>";
			document.vnoform.param_r3.value = "<?= $sReservedParam3 ?>";
			
			//opener.document.vnoform.target = "Parent_window";
			
			// 인증 완료시에 인증결과를 수신하게 되는 귀사 클라이언트 결과 페이지 URL
			document.vnoform.action = "ipin_result_join.php";
			document.vnoform.submit();
			
		}
	</script>
</head>
<body onLoad="fnLoad()">
	<form name="vnoform" method="post">
		<input type="hidden" name="enc_data">								<!-- 인증받은 사용자 정보 암호화 데이타입니다. -->
		<!-- 업체에서 응답받기 원하는 데이타를 설정하기 위해 사용할 수 있으며, 인증결과 응답시 해당 값을 그대로 송신합니다.
			 해당 파라미터는 추가하실 수 없습니다. -->
		<input type="hidden" name="param_r1" value="">
		<input type="hidden" name="param_r2" value="">
		<input type="hidden" name="param_r3" value="">
	</form>
<?
	} else {
?>

<html>
<head>
	<title>NICE평가정보 가상주민번호 서비스</title>
	<body>
	<script type="text/javascript">
	<!--
		alert("인증 실패");
		self.close();
	//-->
	</script>
<?
	}
?>

</body>
</html>