<?
##############################################
# 교육원 고유 정보
##############################################

if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") $Protocol_SSL = "http://";
else $Protocol_SSL = "https://";

$Sql = "SELECT InfoName, InfoValue FROM SiteInfomation2 WHERE UseYN = 'Y' ORDER BY idx";
$Query = mysqli_query($connect, $Sql);
$Row = mysqli_fetch_array($Query);
if($Query && mysqli_num_rows($Query)){
    while($Row = mysqli_fetch_array($Query)){
        $InfoName  = $Row['InfoName'];
        $InfoValue = $Row['InfoValue'];
        
        $SiteCode = "HRDe";
        
        if($InfoName == "SiteCode")         $SiteCode = $InfoValue;
        if($InfoName == "SiteName")         $SiteName = $InfoValue;
        if($InfoName == "SiteName2")        $SiteName2 = $InfoValue;
        if($InfoName == "CertSiteName")     $CertSiteName = $InfoValue;
        if($InfoName == "CertSiteName2")    $CertSiteName2 = $InfoValue;
        if($InfoName == "CertSiteName3")    $CertSiteName3 = $InfoValue;
        if($InfoName == "UPLOAD_DIR")       $UPLOAD_DIR = $InfoValue;
        if($InfoName == "HomeDirectory")    $HomeDirectory = $InfoValue;
        if($InfoName == "SiteURL")          $SiteURL = $Protocol_SSL.$InfoValue;
        if($InfoName == "MobileSiteURL")    $MobileSiteURL = $Protocol_SSL.$InfoValue; //모바일 인증 도메인
        if($InfoName == "MobileAuthURL")    $MobileAuthURL = $Protocol_SSL.$InfoValue;
        if($InfoName == "FlashServerURL")   $FlashServerURL = $Protocol_SSL.$InfoValue;
        if($InfoName == "MovieServerURL")   $MovieServerURL = $Protocol_SSL.$InfoValue;
        if($InfoName == "MobileServerURL")  $MobileServerURL = $Protocol_SSL.$InfoValue;
        if($InfoName == "Auth_Mobile_path") $Auth_Mobile_path = $HomeDirectory.$InfoValue; //휴대폰 본인인증 모듈 경로
        if($InfoName == "Auth_IPIN_path")   $Auth_IPIN_path = $HomeDirectory.$InfoValue; //아이핀 본인인증 모듈 경로
        if($InfoName == "CheckPlus_sitecode")    $CheckPlus_sitecode = $InfoValue;
        if($InfoName == "CheckPlus_sitepasswd")  $CheckPlus_sitepasswd = $InfoValue;
        if($InfoName == "IPIN_CheckPlus_sitecode")    $IPIN_CheckPlus_sitecode = $InfoValue;
        if($InfoName == "IPIN_CheckPlus_sitepasswd")  $IPIN_CheckPlus_sitepasswd = $InfoValue;
        if($InfoName == "DB_Enc_Key")                 $DB_Enc_Key = $InfoValue;
        if($InfoName == "captcha_agent_id")           $captcha_agent_id = $InfoValue; //CAPTCHA 인증
        if($InfoName == "TRAN_SENDER_KEY")            $TRAN_SENDER_KEY = $InfoValue; //발신프로필키
        if($InfoName == "TRAN_CALLBACK")              $TRAN_CALLBACK = $InfoValue; //발신번호
        if($InfoName == "TRAN_CALLBACK2")             $TRAN_CALLBACK2 = $InfoValue; //발신번호2
        if($InfoName == "TRAN_SUBJECT")               $TRAN_SUBJECT = $InfoValue; //MMS전환시 문자 제목
        if($InfoName == "auth_code")                  $auth_code = $InfoValue; //메시지발송 인증코드
        if($InfoName == "storeId")                    $storeId = $InfoValue; //결제 - 고객사 store ID
        if($InfoName == "channelKey")                 $channelKey = $InfoValue; //결제 key
        if($InfoName == "paymentOkURL")               $paymentOkURL = $InfoValue; //결제 승인 URL
    }
}
//mysqli_free_result($Result);
?>
