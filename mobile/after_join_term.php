<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$Agree01 = Replace_Check($data['Agree01']);
$Agree02 = Replace_Check($data['Agree02']);
$Agree03 = Replace_Check($data['Agree03']);
$Mailling = Replace_Check($data['Mailling']);
$Marketing = Replace_Check($data['Marketing']);
$chk4Email = Replace_Check($data['chk4Email']);
$chk4Sms = Replace_Check($data['chk4Sms']);
$id = Replace_Check($data['id']);

//필수항목에 하나라도 동의하지 않은 경우 체크하도록 안내
if($Agree01 != 'on' || $Agree02 != 'on' || $Agree03 != 'on' ){
    $response['result'] = 'E1'; //필수항목에 하나라도 동의하지 않은 경우
}else{
    if($Mailling == 'on'){
        $sendingAgreed = 'Y';
    }else{
        $sendingAgreed = 'N';
    }
    
    if($Marketing == 'on') {
        if($chk5Email == 'on' && $chk5Sms == 'on'){
            $marketingAgreed = 'Y';
        }else{
            if($chk5Email == 'on'){
                $marketingAgreed = 'E';
            }
            if($chk5Sms == 'on'){
                $marketingAgreed = 'S';
            }
        }
    }else{
        $marketingAgreed = 'N';
    }
    
    if (!$connect) {
        die('Connection failed: ' . mysqli_connect_error());
    }
    
    $query4Update = "UPDATE Member 
                        SET Mandatory = 'Y', MandatoryDate = NOW(), Mailling = '$sendingAgreed', MailingDate = NOW(),
                            Marketing = '$marketingAgreed', MarketingDate = NOW()
                     WHERE ID = '$id'";
    $row = mysqli_query($connect, $query4Update);
    
    if($row){
        $response['result'] = 'P';
    }
}
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>