<?
include "../include/include_function.php";
include "./include/include_admin_check.php";

$mode = Replace_Check($mode); //발송 유형
$seq_value = Replace_Check($seq_value); //발송할 Study Seq값

$seq_array = explode('|',$seq_value);

$SendCount = 0;

//개강문자 보내기
if($mode=="Start") {
    //발송할 메세지 확인
    $Sql = "SELECT * FROM SendMessage WHERE MessageMode='before01'";
    $Result = mysqli_query($connect, $Sql);
    $Row = mysqli_fetch_array($Result);
    if($Row) {
        $Massage = $Row['Massage'];
        $TemplateCode 	= $Row['TemplateCode'];
        $TemplateMessage 	= $Row['TemplateMessage'];
    }
    
    foreach($seq_array as $seq) {
        $Sql = "SELECT a.ServiceType, AES_DECRYPT(UNHEX(b.Mobile),'$DB_Enc_Key') AS Mobile, b.Name, c.CompanyName, a.LectureStart,a.LectureEnd, c.CyberEnabled, c.CyberURL, a.ID, d.ContentsName
                FROM Study AS a
        		LEFT OUTER JOIN Member AS b ON a.ID=b.ID
        		LEFT OUTER JOIN Course AS d ON a.LectureCode=d.LectureCode
        		LEFT OUTER JOIN Company AS c ON a.CompanyCode=c.CompanyCode
        		WHERE a.Seq=$seq";
        $Result = mysqli_query($connect, $Sql);
        $Row = mysqli_fetch_array($Result);
        if($Row) {
            $ServiceType = $Row['ServiceType'];
            $Mobile = $Row['Mobile'];
            $msg_mobile = str_replace("-","",$Mobile);
            $Name = $Row['Name'];
            $CompanyName = $Row['CompanyName'];
            $LectureStart = $Row['LectureStart'];
            $LectureEnd = $Row['LectureEnd'];
            $CyberEnabled = $Row['CyberEnabled'];
            $CyberURL = $Row['CyberURL'];
            $ID = $Row['ID'];
            $DayCheckMsg = date("Y-m-d");
            $indate_str1 = strtotime($DayCheckMsg."1 day");
            $DayCheckMsg2 = date("Y-m-d",$indate_str1);
            $ContentsName = $Row['ContentsName'];
            
            if($CyberEnabled=="Y") $LinkDomain = $CyberURL;
            else $LinkDomain = $SiteURL;
            
            $send_date = date('Y-m-d H:i:s');
            
            if($msg_mobile) {
                $phone = str_replace("-","",$Mobile);
                $TemplateMessage2 = str_replace("#{시작}",$LectureStart,$TemplateMessage);
                $TemplateMessage2 = str_replace("#{종료}",$LectureEnd,$TemplateMessage2);
                $TemplateMessage2 = str_replace("#{회사명}",$SiteName,$TemplateMessage2);
                $TemplateMessage2 = str_replace("#{소속업체명}",$CompanyName,$TemplateMessage2);
                $TemplateMessage2 = str_replace("#{도메인}",$LinkDomain,$TemplateMessage2);
                $TemplateMessage2 = str_replace("#{아이디}",$ID,$TemplateMessage2);
                $TemplateMessage2 = str_replace("#{이름}",$Name,$TemplateMessage2);
                $TemplateMessage2 = str_replace("#{과정명}",$ContentsName,$TemplateMessage2);
                
                //발송 로그 기록
                $maxno = max_number("idx","SmsSendLog");
                $etc1 = $maxno;
                $Sql = "INSERT INTO SmsSendLog(idx, ID, Study_Seq, Massage, Code, Mobile, InputID, RegDate) VALUES($maxno, '$ID', $seq, '$TemplateMessage2', '', '$phone', '$LoginAdminID', NOW())";
                $Row = mysqli_query($connect, $Sql);
                
                $kakaotalk_result = kakaotalk_send_before01($TemplateCode,$phone,$TemplateMessage2,$send_date);
                
                if($kakaotalk_result=="Y") {
                    $SendCount++;
                    $code = "0000";
                }else{
                    $code = "0001";
                }
                
                $Sql2 = "UPDATE SmsSendLog SET Code='$code' WHERE idx=$maxno";
                $Row2 = mysqli_query($connect, $Sql2);
            }
        }
    }
}


//본인인증문자보내기
if($mode=="Auth") {
    //발송할 메세지 확인
    $Sql = "SELECT * FROM SendMessage WHERE MessageMode='cronAuth'";
    $Result = mysqli_query($connect, $Sql);
    $Row = mysqli_fetch_array($Result);
    if($Row) {
        $Massage = $Row['Massage'];
        $TemplateCode 	= $Row['TemplateCode'];
        $TemplateMessage 	= $Row['TemplateMessage'];
    }
    
    foreach($seq_array as $seq) {
        $Sql = "SELECT a.ServiceType, AES_DECRYPT(UNHEX(b.Mobile),'$DB_Enc_Key') AS Mobile, b.Name, DATEDIFF(NOW(),a.LectureStart) AS diff_day, c.CyberEnabled, c.CyberURL
            FROM Study AS a
			LEFT OUTER JOIN Member AS b ON a.ID=b.ID
			LEFT OUTER JOIN Company AS c ON a.CompanyCode=c.CompanyCode
			WHERE a.Seq=$seq";
        $Result = mysqli_query($connect, $Sql);
        $Row = mysqli_fetch_array($Result);
        if($Row) {
            $ServiceType = $Row['ServiceType'];
            $Mobile = $Row['Mobile'];
            $msg_mobile = str_replace("-","",$Mobile);
            $Name = $Row['Name'];
            $diff_day = $Row['diff_day'];
            $CyberEnabled = $Row['CyberEnabled'];
            $CyberURL = $Row['CyberURL'];
            $ID = $Row['ID'];
            
            $DayCheckMsg = date("Y-m-d");
            
            if($msg_mobile) {
                $phone = str_replace("-","",$Mobile);
                $TemplateMessage2 = str_replace("#{이름}",$Name,$TemplateMessage);
                $TemplateMessage2 = str_replace("#{날짜}",$DayCheckMsg,$TemplateMessage2);
                $TemplateMessage2 = str_replace("#{인증URL}",$MobileAuthURL,$TemplateMessage2);
                
                //발송 로그 기록
                $maxno = max_number("idx","SmsSendLog");
                $etc1 = $maxno;
                $Sql = "INSERT INTO SmsSendLog(idx, ID, Study_Seq, Massage, Code, Mobile, InputID, RegDate) VALUES($maxno, '$ID', $seq, '$TemplateMessage2', '', '$phone', '$LoginAdminID', NOW())";
                $Row = mysqli_query($connect, $Sql);
                
                $kakaotalk_result = mts_mms_send($msg_mobile, $TemplateMessage2, $TRAN_CALLBACK2, "", $TemplateCode);
                
                if($kakaotalk_result=="Y") $SendCount++;
            }
        }
    }
}

echo $SendCount;

mysqli_close($connect);
?>