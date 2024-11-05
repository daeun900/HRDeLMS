<?
##############################################
# 문자 관리
# 사이트 : 개발서버
# 문자발송업체 : 알림톡(MTS)
##############################################

// 알림톡 문자발송함수
function mts_mms_send($phone, $msg, $callback, $etc1, $template_code="hrd01") {
    global $connect;
    global $auth_code;
    global $TRAN_SENDER_KEY;
    
    if ($phone)  {
        // 발신번호 /로 여러개 들어가 있는 경우 수정.
        $callback_arr = explode("/",$callback);
        $callback = trim($callback_arr[0]);
        
        $data=array(
            "auth_code"=>$auth_code,
            "sender_key"=>$TRAN_SENDER_KEY,
            "send_date"=>date("YmdHis"),
            "callback_number"=>$callback,
            "nation_phone_number"=>"82",
            "phone_number"=>$phone,
            "template_code"=>$template_code,
            "message"=>stripslashes($msg),
            "tran_type"=>"L",
            "tran_message"=>$msg,
            "add_etc1"=>$etc1
        );
        $data = json_encode($data);
        
        $url = "https://api.mtsco.co.kr";
        $url .= "/sndng/atk/sendMessage";
        
        $ch = curl_init();                                 //curl 초기화
        curl_setopt($ch,CURLOPT_URL, $url);               //URL 지정하기
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 10);      //connection timeout 10초
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);       //POST data
        curl_setopt($ch,CURLOPT_POST, true);              //true시 post 전송
        curl_setopt($ch,CURLOPT_HTTPHEADER, array("Accept: application/json","Content-Type: application/json"));
        $response = curl_exec($ch);
        
        curl_close($ch);
        
        $response = json_decode($response);
        if ($response->code != "0000") $result_code = $response->code;
        
        $report_date = $response->received_at;
        $result_date = $response->received_at;
        
        $query = "INSERT INTO MTS_MMS_MSG(TRAN_PHONE, TRAN_CALLBACK, TRAN_MSG,TRAN_DATE, TRAN_TYPE, TRAN_ETC1, TRAN_REPORTDATE, TRAN_RSLTDATE, TRAN_RSLT)
                  VALUES ('$phone','$callback','$msg',now(), 4,'$etc1', '$report_date', '$result_date', '$result_code')";
        if ($response->code != "0000") $result = mysqli_query($connect, $query);
        $result = $response->code;
        
        if ($result) return "Y";
        else if ($result == "0000") return "Y";
        else return "N2";
        
    }else{
        return "N1";
    }
}

//사업주훈련/내일배움카드>학습관리 - 본인인증문자발송
function kakaotalk_send($msg_type,$msg_mobile,$msg_var,$send_date) {
    global $connect;
    global $kakaotalk_array;
    global $TRAN_SENDER_KEY;
    global $TRAN_CALLBACK;
    global $TRAN_SUBJECT;
    
    if($msg_mobile) {
        $msg_var_arrary = explode("|",$msg_var);
        
        $kakaotalk_template = $kakaotalk_array[$msg_type];
        $kakaotalk_template_arrary = explode("|",$kakaotalk_template);
        $TRAN_TMPL_CD = $kakaotalk_template_arrary[0];
        
        switch ($msg_type) {
            case "mtm": // 1:1 문의 답변 등록 알림
                $TRAN_MSG = $kakaotalk_template_arrary[1];
                break;
            case "cronAuth": // 본인인증 알림 : 개강 다음날에 발송
                $TRAN_MSG = str_replace("#{이름}",$msg_var_arrary[0],$kakaotalk_template_arrary[1]);
                $TRAN_MSG = str_replace("#{날짜}",$msg_var_arrary[1],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{인증URL}",$msg_var_arrary[2],$TRAN_MSG);
                break;
            case "cronStart1": // 학습시작(개강알림) :비환급, 개강 당일 발송
                $TRAN_MSG = str_replace("#{회사명}",$msg_var_arrary[0],$TemplateMessage);
                $TRAN_MSG = str_replace("#{소속업체명}",$msg_var_arrary[1],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{시작일}",$msg_var_arrary[2],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{도메인}",$msg_var_arrary[3],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{아이디}",$msg_var_arrary[4],$TRAN_MSG);
                break;
            case "cronStart2": // 학습시작(개강알림) :환급, 개강 당일 발송
                $TRAN_MSG = str_replace("#{회사명}",$msg_var_arrary[0],$kakaotalk_template_arrary[1]);
                $TRAN_MSG = str_replace("#{소속업체명}",$msg_var_arrary[1],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{시작일}",$msg_var_arrary[2],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{도메인}",$msg_var_arrary[3],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{아이디}",$msg_var_arrary[4],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{날짜}",$msg_var_arrary[5],$TRAN_MSG);
                $TRAN_MSG = str_replace("#{인증URL}",$msg_var_arrary[6],$TRAN_MSG);
                break;
        }
        
        $TRAN_PHONE = $msg_mobile; //수신번호
        $TRAN_REPLACE_TYPE = "L"; //전환전송 타입 'S', 'L', 'N'  'S' : SMS 전환전송 'L' : LMS 전환전송 'N' : 전환전송 하지않음
        
        $maxno = max_number("idx","SmsSendLog");
        $etc1 = $maxno;
        
        $send = mts_mms_send($TRAN_PHONE, $TRAN_MSG, $TRAN_CALLBACK, $etc1, $TRAN_TMPL_CD);
        
        if($send=="Y") {
            $code = "0000";
            return $send;
        }else{
            $code = "0001";
            return "N2";
        }
    }else{
        return "N1";
    }
}

//아이디/PW 찾기
function find_mms_send($msg_type,$msg_mobile,$msg_var,$user_id,$input_id) {
    global $connect;
    global $auth_code;
    global $TRAN_SENDER_KEY;
    global $TRAN_CALLBACK;
    
    if ($msg_mobile)  {
        $Sql = "SELECT * FROM SendMessage WHERE MessageMode='$msg_type'";
        $Result = mysqli_query($connect, $Sql);
        $Row = mysqli_fetch_array($Result);
        if($Row) {
            $Message = $Row['Massage'];
            $TemplateCode   = $Row['TemplateCode'];
            $TemplateMessage    = $Row['TemplateMessage'];
            
            $msg_var_arrary = explode("|",$msg_var);
            
            if($msg_type == 'find_id'){
                $TRAN_MSG = str_replace("#{이름}",$msg_var_arrary[0],$TemplateMessage);
                $TRAN_MSG = str_replace("#{아이디}",$msg_var_arrary[1],$TRAN_MSG);
            }else if($msg_type == 'new_password'){
                $TRAN_MSG = str_replace("#{이름}",$msg_var_arrary[0],$TemplateMessage);
                $TRAN_MSG = str_replace("#{임시비밀번호}",$msg_var_arrary[1],$TRAN_MSG);
            }else{
                $TRAN_MSG = $TemplateMessage;
            }
            
            //발송 로그 기록
            $maxno = max_number("idx","SmsSendLog");
            $etc1 = $maxno;
            
            $Sql = "INSERT INTO SmsSendLog(idx, ID, Study_Seq, Massage, Code, Mobile, InputID, RegDate)
                   VALUES($maxno, '$user_id', 0, '$TRAN_MSG', '', '$msg_mobile', '$input_id', NOW())";
            $Row = mysqli_query($connect, $Sql);
            
            
            //메일발송
            // 발신번호 /로 여러개 들어가 있는 경우 수정.
            $callback_arr = explode("/",$TRAN_CALLBACK);
            $callback = trim($callback_arr[0]);
            
            $data=array(
                "auth_code"=>$auth_code,
                "sender_key"=>$TRAN_SENDER_KEY,
                "send_date"=>date("YmdHis"),
                "callback_number"=>$callback,
                "nation_phone_number"=>"82",
                "phone_number"=>$msg_mobile,
                "template_code"=>$TemplateCode,
                "message"=>stripslashes($TRAN_MSG),
                "tran_type"=>"L",
                "tran_message"=>$TRAN_MSG,
                "add_etc1"=>$etc1
            );
            $data = json_encode($data);
            
            $url = "https://api.mtsco.co.kr";
            $url .= "/sndng/atk/sendMessage";
            
            $ch = curl_init();                                 //curl 초기화
            curl_setopt($ch,CURLOPT_URL, $url);               //URL 지정하기
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 10);      //connection timeout 10초
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);       //POST data
            curl_setopt($ch,CURLOPT_POST, true);              //true시 post 전송
            curl_setopt($ch,CURLOPT_HTTPHEADER, array("Accept: application/json","Content-Type: application/json"));
            $response = curl_exec($ch);
            
            curl_close($ch);
            
            $response = json_decode($response);
            if ($response->code != "0000") $result_code = $response->code;
            
            $report_date = $response->received_at;
            $result_date = $response->received_at;
            
            $query = "INSERT INTO MTS_MMS_MSG(TRAN_PHONE, TRAN_CALLBACK, TRAN_MSG,TRAN_DATE, TRAN_TYPE, TRAN_ETC1, TRAN_REPORTDATE, TRAN_RSLTDATE, TRAN_RSLT)
                  VALUES ('$msg_mobile','$callback','$TRAN_MSG',now(), 4,'$etc1', '$report_date', '$result_date', '$result_code')";
            
            if ($response->code != "0000") $result = mysqli_query($connect, $query);
            $result = $response->code;
            
            if ($result) $code = "0000";
            else if ($result == "0000") $code = "0000";
            else $code = "0001";
            
            $Sql2 = "UPDATE SmsSendLog SET Code='$code' WHERE idx=$maxno";
            $Row2 = mysqli_query($connect, $Sql2);
            
            if($code=="0000") return "Y";
            else return "N2";
        }else{
            return "N3";
        }
    }else{
        return "N1";
    }
}
?>
