<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
include "../include/include_function.php";
$data = json_decode(file_get_contents('php://input'), true);

// 아이디와 비밀번호 추출
$id = Replace_Check_XSS2($data['id']);
session_set_cookie_params([
    'lifetime' => 0,              // 브라우저 종료 시 쿠키 만료
    'path' => '/',
    'domain' => 'yourdomain.com', // 클라이언트와 서버의 공통 도메인
    'secure' => true,             // HTTPS만 허용
    'httponly' => true,           // JS에서 쿠키 접근 차단
    'samesite' => 'None'          // CORS 환경에서 동작 가능하도록 설정
]);
session_start();
// 응답 초기화
$response = [];
$result = '';

// 세션에서 `compareResult` 조회
// if (isset($_SESSION['compareResult']) && is_array($_SESSION['compareResult'])) {
//     $sessionId = $_SESSION['compareResult']['id'];
//     $status = $_SESSION['compareResult']['status'];
    
//     // ID 비교
//     if ($id === $sessionId) {
//         // 일치하면 status 반환
//         $response['result'] = $status; //N 또는 Y 보냄, (N은 DB와 인증 정보가 일치하지 않을 때.)
//     } else {
//         // ID가 일치하지 않으면 에러 메시지 반환
//         $response['result'] = 'N1'; //id가 세션 id와 일치하지 않을때
//     }
// } else {
//     // 세션 값이 없거나 유효하지 않은 경우
//     $response['result'] = 'No Session';
// }
$response['log1'] = $_SESSION['compareResult'];
$response['log2'] = $_SESSION['id'];
// $response['log3'] = $_SESSION['compareResult']['status'];
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>