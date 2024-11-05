<?
include "../../include/include_function.php";

$idx = Replace_Check_XSS2($idx);
$price = Replace_Check_XSS2($price); //환불금액-총판매금액
$subPrice = Replace_Check_XSS2($subPrice); //환불금액-정부지원금
$PointPrice = Replace_Check_XSS2($PointPrice); //환불금액-학습포인트
$CouponPrice = Replace_Check_XSS2($CouponPrice); //환불금액-쿠폰
$realPrice = Replace_Check_XSS2($realPrice); //환불금액-환불예정금액
$refundType = Replace_Check_XSS2($refundType); //환불사유유형
$refundContents = Replace_Check_XSS2($refundContents); //환불사유내용
$bankName = Replace_Check_XSS2($bankName); //환불계좌 은행명
$bankNumber = Replace_Check_XSS2($bankNumber); //환불계좌번호
$AcountName = Replace_Check_XSS2($AcountName); //환불계좌 예금주

if(empty($_SESSION['LoginMemberID'])) {
?>
<script type="text/javascript">
<!--
	alert("로그인후에 환불신청이 가능합니다.");
	top.location.reload();
//-->
</script>
<?
    exit;
}

//[1]해당 결제내역 상태를 '결제취소' 처리
$Sql = "UPDATE LectureRequest2 SET Status = 'C' WHERE idx='$idx' AND ID = '$LoginMemberID'";
$Row = mysqli_query($connect, $Sql);

//[2]결제취소 테이블에 INSERT
$maxno = max_number("idx","LectureCancel");
$SqlA = "INSERT INTO LectureCancel(idx, requestIdx, ID, BankName, BankNumber, AcountName, RefundType, RefundContents, Price, SubPrice, PointPrice, CouponPrice, RealPrice, RegDate)
         VALUES($maxno , $idx , '$LoginMemberID', '$bankName', '$bankNumber', '$AcountName', '$refundType', '$refundContents', $price, $subPrice, $PointPrice, $CouponPrice, $realPrice, now())";
$RowA = mysqli_query($connect, $SqlA);


if($Row && $RowA) echo $maxno; else echo "N";

mysqli_close($connect);
?>