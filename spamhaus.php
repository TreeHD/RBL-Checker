#!/usr/bin/php

<?php
$time_start = microtime(true); 


$rbls = [
    'sbl.spamhaus.org',
    'xbl.spamhaus.org',
    'pbl.spamhaus.org',
];

$mq = [
    '46.174.204.101',
    '45.59.128.121',
    '45.59.128.220',
    '45.59.129.11',
];

$rbl_count   = count($rbls);
$listed_rbls = [];
$arrStatus = [];
$ipRange = [];
$cmdDig = '/usr/bin/dig';

if (file_exists($cmdDig)) {
    $digCheck = true;
} else {
    echo "\n*** ERROR *** ||| $cmdDig not found, please install dig\n" . PHP_EOL;
    exit();
}

foreach ($mq as $ipBlock) {
    if (strpos($ipBlock, '/') !== false) {
    $split = explode('/', $ipBlock);
    $ipRangeStart = ip2long($split[0]) & ((-1 << (32 - (int)$split[1])));
    $ipRangeEnd = ip2long($split[0]) + pow(2, (32 - (int)$split[1])) - 1;
        for ($i = $ipRangeStart; $i <= $ipRangeEnd; $i++) {
            $ipRange[] = long2ip($i);
            }
        } elseif (strpos($ipBlock, '/') !== true) {
            $ipRange[] = $ipBlock;
    }
}

$ip_count = count($ipRange);
foreach ($rbls as $rbl) {
    echo "CHECKING $rbl FOR $ip_count IP's\n";
    foreach($ipRange as $ipAddress) {
        $rev = join('.', array_reverse(explode('.', trim($ipAddress))));
        $lookup = "$cmdDig @1.1.1.1 $rev.$rbl +short";
        $strLookup = `$lookup`;
        $strLookup = trim($strLookup);
        $listed = (strpos($strLookup, '127') !== false);
        $responseCodes = explode("\n", $strLookup);
        printf('    --- %s [%s] %s %s %s',$ipAddress, $listed ? 'LISTED' : 'OK', ' via ', $rev.'.'.$rbl, PHP_EOL);
        if ($listed) {
            $arrStatus[$rbl][$ipAddress] = $responseCodes;
            //print_r("\n".$strLookup."\n\n");
            //$arrStatus[$rbl][] = $ipAddress;
        }
    }
}

if (count($arrStatus) == 0)
{
    echo "\n\nNo listings found - checked $ip_count IP's against $rbl_count RBL's! \n\n";
    
}

print_r($arrStatus);

$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;
echo "\n\nTotal Execution Time: ".$execution_time." Mins \n\n";