<?
include "../include/include_function.php";
include "./include/include_admin_check.php";
$onlyNumPhone = str_replace('-', '',$phone);
$callback = str_replace("-","",$SitePhone);
// echo $id.'-'.$name.'-'.$phone.'-'.$email.'-'.$onlyNumPhone.'-'.$response.'-'.$callback;

//발송 로그 기록
$Sql = "UPDATE SimpleAsk SET Response='$response', Responder = '$LoginAdminID', ResponseDate = NOW() WHERE idx=$idx ";
$Row = mysqli_query($connect, $Sql);

$send = mts_mms_send($phone, $response, $callback, "", "simpleAsk");

if($send=="Y") $code = "1";
else $code = "0";

$Sql2 = "UPDATE SimpleAsk SET ResponseResult='$code' WHERE idx=$idx";
$Row2 = mysqli_query($connect, $Sql2);

if($Row && $send == 'Y') {
    $ProcessOk = "Y";
    $msg = "발송되었습니다.";
}else{
    $ProcessOk = "N";
    if($Row) $msg = "쿼리 실행 중 오류가 발생했습니다.";
    else $msg = $send."문자 발송 중 오류가 발생했습니다.";
}
?>
<script>
	alert("<?=$msg?>");
	history.back();
</script>