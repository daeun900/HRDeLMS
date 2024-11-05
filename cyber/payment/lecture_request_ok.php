<?
include "../../include/include_function.php";
$payment_id = $_POST['payment_id'];
$code= $_POST['orderId '];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터를 받아옵니다.
    $data = file_get_contents("php://input");
    
    // JSON 데이터를 배열로 디코딩합니다.
    $decodedData = json_decode($data, true);
    
    $paymentId = $decodedData['paymentId']; //주문고유번호    
    $applyType = $decodedData['applyType']; //신청유형
    $supportType = $decodedData['supportType']; //지원유형
    $LectureStart = $decodedData['LectureStart']; //학습시작일자
    $LectureEnd = $decodedData['LectureEnd']; //학습종료일자
    $LectureCode = $decodedData['LectureCode']; //과정코드
    $ContentsName = $decodedData['ContentsName']; //과정명
    $Price = $decodedData['Price']; //판매가
    $SubPrice = $decodedData['SubPrice']; //정부지원금
    $PointPrice = $decodedData['PointPrice']; //학습포인트
    $CouponPrice = $decodedData['CouponPrice']; //쿠폰
    $RealPrice = $decodedData['RealPrice']; //자비부담금
    
    if (json_last_error() === JSON_ERROR_NONE) {
        // 필요한 데이터를 가져옵니다.
        $paymentId = $decodedData['paymentId']; //주문고유번호
        $applyType = $decodedData['applyType']; //신청유형
        $supportType = $decodedData['supportType']; //지원유형
        $LectureStart = $decodedData['LectureStart']; //학습시작일자
        $LectureEnd = $decodedData['LectureEnd']; //학습종료일자
        $LectureCode = $decodedData['LectureCode']; //과정코드
        $ContentsName = $decodedData['ContentsName']; //과정명
        $Price = $decodedData['Price']; //판매가
        $SubPrice = $decodedData['SubPrice']; //정부지원금
        $PointPrice = $decodedData['PointPrice']; //학습포인트
        $CouponPrice = $decodedData['CouponPrice']; //쿠폰
        $RealPrice = $decodedData['RealPrice']; //자비부담금
        
        // 가져온 데이터를 사용할 수 있습니다.
        error_log("Payment ID: $paymentId");
        error_log("apply Type: $applyType");
        error_log("IMP: $supportType");
        error_log("Lecture Start: $LectureStart");
        error_log("Lecture End: $LectureEnd");
        error_log("Lecture Code: $LectureCode");
        error_log("Contents Name: $ContentsName");
        error_log("Price : $Price");
        error_log("Sub Price: $SubPrice");
        error_log("Point Price: $PointPrice");
        error_log("Coupon Pirce: $CouponPrice");
        error_log("Real Price: $RealPrice");
    }
}else {
    echo "POST request required.";
}

if(!empty($paymentId)){
    $maxno = max_number("idx","LectureRequest2");
    $Sql = "INSERT INTO LectureRequest2
                (idx, ID, paymentId, ApplyType, SupportType, LectureCode, ContentsName, LectureStart, LectureEnd,
                 Price, SubPrice, PointPrice, CouponPrice, RealPrice, Status, Payment, Del, RegDate)
            VALUES($maxno, '$LoginMemberID', '$paymentId', '$applyType', '$supportType', '$LectureCode', '$ContentsName', '$LectureStart', '$LectureEnd',
                   $Price, $SubPrice, $PointPrice, $CouponPrice, $RealPrice, 'A', 'A', 'N', NOW())";
    $Row = mysqli_query($connect, $Sql);
    if($Row) echo $maxno; else echo "N";
}

mysqli_close($connect);
?>