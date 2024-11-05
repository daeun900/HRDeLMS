<?
include "../../include/include_function.php";

$LectureCode = Replace_Check_XSS2($LectureCode);
$Price = Replace_Check_XSS2($Price);
$LectureDate = Replace_Check_XSS2($LectureDate);

if(empty($_SESSION['LoginMemberID'])) {
?>
<script type="text/javascript">
<!--
	alert("로그인후에 수강신청이 가능합니다.");
	top.location.reload();
//-->
</script>
<?
exit;
}

//[1]신청내역이 존재하는지 확인
$Sql = "SELECT COUNT(*) FROM LectureRequest WHERE ID='$LoginMemberID' AND LectureCode='$LectureCode' AND Del='N' AND (Status='A' OR Status='B')";
$Result = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Result);
$Requst_Cpunt = $Row[0];

if($Requst_Cpunt>0) {
    echo "exist";
}else{
    //[2]신청내역이 없을 경우 INSERT(LectureRequest)
    $maxno = max_number("idx","LectureRequest");
    
    $LectureDate_Array = explode("|",$LectureDate);
    $LectureStart = $LectureDate_Array[0];
    $LectureEnd = $LectureDate_Array[1];
    
    $Sql = "INSERT INTO LectureRequest(idx, ID, LectureCode, LectureStart, LectureEnd, Price, Status, Payment, Del, RegDate) 
            VALUES($maxno, '$LoginMemberID', '$LectureCode', '$LectureStart', '$LectureEnd', $Price, 'A', '', 'N', NOW())";
    $Row = mysqli_query($connect, $Sql);
    
    if($Row) echo "Y"; else echo "N";
}

mysqli_close($connect);
?>