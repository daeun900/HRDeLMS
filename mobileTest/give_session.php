<?php 
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$studySeq = $data['seq'] ?? null;

$response = [];

// 발급된 세션 id가 있다면 세션의 id를, 없다면 false 반환
if(!session_id()) {
    // id가 없을 경우 세션 시작
    session_start();
}

$_SESSION['loginID'] = $id;

$response['sessionValue'] = $_SESSION['loginID'];

if (!$connect) {
    $response['title'] = 'include_function.php 파일 확인해야하는 경우';
}
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>