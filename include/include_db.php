<?
##############################################
# DB Connect
##############################################

$db['host'] = "192.168.11.95";
$db['user'] = "root";
$db['pass'] = "qwer1234!@#$";
$db['db']   = "hrd";

$connect = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['db']);
mysqli_query($connect,"SET NAMES utf8");

if (!$connect) {
    echo "<BR>Error: Unable to connect to MySQL." . PHP_EOL;
    echo "<BR>Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "<BR>Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
?>
