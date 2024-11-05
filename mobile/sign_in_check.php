<?php
include "../include/include_function.php";

//세션이 상실되었으면
if(empty($_SESSION['LoginMemberID'])) {
    $response['result'] = 'Empty';
}else{
    $id= $_SESSION['LoginMemberID'];
    $sessionID = session_id();
    
    $sql = "SELECT COUNT(*) FROM LoginIng WHERE ID='$id' AND SessionID='$sessionID' AND IP='$UserIP'";
    $result = mysqli_query($connect, $sql);
    $row = mysqli_fetch_array($result);
    $TOT_NO = $row[0];
    
    if($TOT_NO < 1) {
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