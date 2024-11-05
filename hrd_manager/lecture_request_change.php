<?
include "../include/include_function.php";
include "./include/include_admin_check.php";

$ID = Replace_Check($ID);
$idx = Replace_Check($idx);
$Status = Replace_Check($Status);

$error_count = 0;

//[1]상태값 변경
$Sql = "UPDATE LectureRequest SET Status='$Status' WHERE ID='$ID' AND idx=$idx";
$Row = mysqli_query($connect, $Sql);

//[2]상태를 '신청취소'로 했을 경우, 신청자에게 문자발송
if($Status == "C"){
    //[2-]발송할 메세지 확인
    $SqlA = "SELECT * FROM SendMessage WHERE MessageMode='cancel_message'";
    $ResultA = mysqli_query($connect, $SqlA);
    $RowA = mysqli_fetch_array($ResultA);
    if($RowA) {
        $Massage         = $RowA['Massage'];
        $TemplateCode 	 = $RowA['TemplateCode'];
        $TemplateMessage = $RowA['TemplateMessage'];
    }
    
    //[2-2]문자발송할 정보확인
    $SqlB = "SELECT a.*, b.Name, AES_DECRYPT(UNHEX(b.Mobile),'$DB_Enc_Key') AS Mobile, d.ContentsName
             FROM LectureRequest AS a
             LEFT OUTER JOIN Member AS b ON a.ID=b.ID
             LEFT OUTER JOIN Course AS d ON a.LectureCode=d.LectureCode
             WHERE a.Del='N' AND a.ID='$ID'";
    $ResultB = mysqli_query($connect, $SqlB);
    $RowB = mysqli_fetch_array($ResultB);
    if($RowB) {
        $Mobile = $RowB['Mobile'];
        $msg_mobile = str_replace("-","",$Mobile);
        $Name = $RowB['Name'];
        $ContentsName = $RowB['ContentsName'];
        
        $send_date = date('Y-m-d H:i:s');
        
        if($msg_mobile) {
            $phone = str_replace("-","",$Mobile);
            $TemplateMessage2 = str_replace("#{이름}",$Name,$TemplateMessage);
            $TemplateMessage2 = str_replace("#{과정명}",$ContentsName,$TemplateMessage2);
            
            //발송 로그 기록
            $maxno = max_number("idx","SmsSendLog");
            $etc1 = $maxno;
            $Sql1 = "INSERT INTO SmsSendLog(idx, ID, Study_Seq, Massage, Code, Mobile, InputID, RegDate) VALUES($maxno, '$ID', 0, '$TemplateMessage2', '', '$phone', '$LoginAdminID', NOW())";
            $Row1 = mysqli_query($connect, $Sql1);
            if(!$Sql1)  $error_count++;
            
            $kakaotalk_result = mts_mms_send($phone, $TemplateMessage2, $TRAN_CALLBACK, "", $TemplateCode);
            
            if($kakaotalk_result=="Y") $code = "0000";
            else $code = "0001";
            
            $Sql2 = "UPDATE SmsSendLog SET Code='$code' WHERE idx=$maxno";
            $Row2 = mysqli_query($connect, $Sql2);
            if(!$Sql2)  $error_count++;
        }
    }
}

if($error_count>0) echo "Fail";
else echo "Success";

mysqli_close($connect);
?>