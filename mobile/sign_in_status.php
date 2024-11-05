<?php
include "../include/include_function.php";

// 응답 초기화
$response = [];

//세션이 상실되었으면
if(empty($_SESSION['LoginMemberID'])) {
    $response['result'] = 'Empty';
}else{
    $id= $_SESSION['LoginMemberID'];
    $sessionID = session_id();
    
    $sql4select = "SELECT COUNT(*) FROM LoginIng WHERE ID='$id' AND SessionID='$sessionID' AND IP='$UserIP'";
    $query4select = mysqli_query($connect, $sql4select);
    $row = mysqli_fetch_array($query4select);    
    $exist = $row[0];
    
    
    if($exist < 1) { //위의 쿼리로 조회되는 데이터가 없으면
        $response['result'] = 'N';
    }else{
        $response['result'] = 'Y';
    }
}

mysqli_close($connect);
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>