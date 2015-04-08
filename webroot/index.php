<?php
include_once('../config/config.php');

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Protected Area"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}

$server = $CONFIG['server'];
$host = $_SERVER['PHP_AUTH_USER'];
$secret = $_SERVER['PHP_AUTH_PW'];
$ip = $_SERVER['REMOTE_ADDR'];

if (!isset($CONFIG['hosts'][$host]) || $CONFIG['hosts'][$host] !== $secret) {
    return;
}

$hostParts = explode('.', $host);
array_shift($hostParts);
$zone = implode('.', $hostParts);
$key = $CONFIG['zones'][$zone];

$data = "<<EOF
server $server
zone $zone
update delete $host A
update add $host 300 A $ip
send
EOF";

exec("nsupdate -y '$key' $data", $commandOutput, $returnCode);
