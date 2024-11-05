<?php

$HOSTN = shell_exec('hostname');
$HTTP = shell_exec("netstat -nap | grep ':80' | grep ESTABLISHED | wc -l");
$UPT = shell_exec("uptime | awk '{print $3}'");
$UPav = shell_exec("uptime | awk -F'load average: ' '{print $2}'");
$HTTPS = shell_exec("netstat -nap | grep ':443' | grep ESTABLISHED | wc -l");
$SUM = shell_exec("netstat -nap | grep ':80\|:443' | grep ESTABLISHED | wc -l");
$HTTPMEM = shell_exec("ps aux | grep apache | awk '{print \$6}' | awk '{total = total + \$1} END {print total/1024}'");
$disk_root = shell_exec('df -h | grep -v boot | grep -v tmpfs | grep -v glusterfs | awk \'{for(i=2; i<NF; i++) printf $i " "; printf $NF "\n"}\' | sed "s/ on//g"');




echo "  $HOSTN / "; 
echo "UP Time : " . $UPT . "일<br>"; 
echo "Average load : " . $UPav . "<br>"; 
echo "080 Port 접속자 : " . $HTTP . "명<br>"; 
echo "443 Port 접속자 : " . $HTTPS . "명<br>"; 
echo "080+443 접속자 : " . $SUM . "명<br>"; 
echo "Apache Memory  : " . $HTTPMEM . "MB<br>"; 
#echo "root영역: " . $disk_root . "MB<br>";
echo "<pre>$disk_roots</pre>"; 
#echo "<pre>$HTTPS</pre>";
#echo "<pre>$SUM</pre>";

// 결과를 줄 단위로 분할
$lines = explode("\n", trim($disk_root));

// HTML 표 시작
echo "<table border='1' cellpadding='5' cellspacing='0'>";
#echo "<tr><th>Size</th><th>Used</th><th>Avail</th><th>Use%</th><th>Mounted on</th></tr>";

// 각 줄을 처리하여 표의 행으로 변환
foreach ($lines as $line) {
    if (trim($line) != "") {
        // 필드를 공백으로 분리
        $fields = preg_split('/\s+/', trim($line));
        
        // 행 출력
        echo "<tr>";
        foreach ($fields as $field) {
         #   echo "<td>" . htmlspecialchars($field) . "</td>";
            echo "<td style='padding: 3px; font-size: 14px;'>" . htmlspecialchars($field) . "</td>";
        }
        echo "</tr>";
    }
}

// 표 끝
echo "</table>";

?>
