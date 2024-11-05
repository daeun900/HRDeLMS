<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$noticeIdx = Replace_Check($data['idx']);
// 응답 초기화
$response = [];

$sql4update = "UPDATE Notice SET ViewCount=ViewCount+1 WHERE idx=$noticeIdx";
$query4update = mysqli_query($connect, $sql4update);

$sql4select = "SELECT * FROM Notice WHERE Del='N' AND idx = $noticeIdx";
$query4select = mysqli_query($connect, $sql4select);
$row = mysqli_fetch_array($query4select);


if($row){
    extract($row);
    $response['title'] =  $Title;
    $response['regDate'] =  $RegDate;
    $response['hit'] =  $ViewCount;
    $response['content'] = $Content;
    $response['fileName1'] = $FileName1;
    $response['realFileName1'] = $RealFileName1;
    $response['fileName2'] = $FileName2;
    $response['realFileName2'] = $RealFileName2;
    $response['fileName3'] = $FileName3;
    $response['realFileName3'] = $RealFileName3;
    $response['fileName4'] = $FileName4;
    $response['realFileName4'] = $RealFileName4;
    $response['fileName5'] = $FileName5;
    $response['realFileName5'] = $RealFileName5;
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>