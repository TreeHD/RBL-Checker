#!/usr/bin/php

<?php
$time_start = microtime(true); 

$rbls = [
    'b.barracudacentral.org',
    'spam.rbl.msrbl.net',
    'zen.spamhaus.org',
    'bl.deadbeef.com',
    'bl.emailbasura.org',
    'bl.spamcop.net',
    'blackholes.five-ten-sg.com',
    'blacklist.woody.ch',
    'bogons.cymru.com',
    'cbl.abuseat.org',
    'cdl.anti-spam.org.cn',
    'combined.abuse.ch',
    'combined.rbl.msrbl.net',
    'db.wpbl.info',
    'dnsbl-1.uceprotect.net',
    'dnsbl-2.uceprotect.net',
    'dnsbl-3.uceprotect.net',
    'dnsbl.inps.de',
    'dnsbl.njabl.org',
    'drone.abuse.ch',
    'drone.abuse.ch',
    'duinv.aupads.org',
    'dyna.spamrats.com',
    'dynip.rothen.com',
    'images.rbl.msrbl.net',
    'ips.backscatterer.org',
    'ix.dnsbl.manitu.net',
    'korea.services.net',
    'noptr.spamrats.com',
    'ohps.dnsbl.net.au',
    'omrs.dnsbl.net.au',
    'orvedb.aupads.org',
    'osps.dnsbl.net.au',
    'osrs.dnsbl.net.au',
    'owfs.dnsbl.net.au',
    'owps.dnsbl.net.au',
    'pbl.spamhaus.org',
    'phishing.rbl.msrbl.net',
    'probes.dnsbl.net.au',
    'proxy.bl.gweep.ca',
    'proxy.block.transip.nl',
    'psbl.surriel.com',
    'rdts.dnsbl.net.au',
    'relays.bl.gweep.ca',
    'relays.bl.kundenserver.,de',
    'relays.nether.net',
    'residential.block.trans,ip.nl',
    'ricn.dnsbl.net.au',
    'rmst.dnsbl.net.au',
    'sbl.spamhaus.org',
    'short.rbl.jp',
    'spam.abuse.ch',
    'spam.spamrats.com',
    'spamlist.or.kr',
    'spamrbl.imp.ch',
    't3direct.dnsbl.net.au',
    'ubl.lashback.com',
    'ubl.unsubscore.com',
    'virbl.bit.nl',
    'virus.rbl.jp',
    'virus.rbl.msrbl.net',
    'web.dnsbl.sorbs.net',
    'wormrbl.imp.ch',
    'xbl.spamhaus.org',
    'zombie.dnsbl.sorbs.net',
    '0spamurl.fusionzero.com',
];

$ip = [
    '199.254.84.0/24',

];

$rbl_count   = count($rbls);
$listed_rbls = [];
$arrStatus = [];
$ipRange = [];

foreach ($ip as $ipBlock) {
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

foreach($ipRange as $ipAddress) {
    foreach ($rbls as $rbl) {
        $rev = join('.', array_reverse(explode('.', trim($ipAddress))));
        $lookup = sprintf('%s.%s', $rev, $rbl);
        $listed = gethostbyname($lookup) !== $lookup;
        printf(' --- %s [%s] %s %s %s',$ipAddress, $listed ? 'LISTED' : 'OK', ' via ', $lookup, PHP_EOL);
    } if ($listed) {
        $arrStatus[$rbl][] = $ipAddress;
    }
}
$ip_count = count($ipRange);
if (count($arrStatus) == 0)
{
    echo "\n\nNo listings found - checked $ip_count IP's against $rbl_count RBL's! \n\n";
    
}

print_r($arrStatus);

$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;
echo "Total Execution Time: ".$execution_time." Mins \n\n";