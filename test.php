<?php
include ('Log.php');

$LOG_PATTERN = "/(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")/";

$logFileName = 'access_log.txt';

$file = fopen($logFileName, 'r') or die ('Не удалось открыть файл!');

/**
 * @type Log[] $logs
 */
$logs = [];

$views = null;
$countUniqueUrls = null;
$traffic = null;
$urls = [];
$statusCodes = [];

while (!feof($file)) {
    $log = fgets($file);
    $result = [];
    preg_match($LOG_PATTERN, $log, $result);

    $line = [
        'ip' => $result [1],
        'identity' => $result [2],
        'user' => $result [3],
        'date' => $result [4],
        'time' => $result [5],
        'timezone' => $result[6],
        'method' => $result [7],
        'path' => $result[8],
        'protocol' => $result[9],
        'status' => $result[10],
        'bytes' => $result[11],
        'url' => $result[12],
        'agent' => $result[13]
    ];

    if (array_key_exists($line['status'], $statusCodes)){
        $statusCodes[$line['status']]++;
    }
    else{
        $statusCodes[$line['status']] = 1;
    }

    if (!in_array($line['path'], $urls)){
        $urls[] = $line['path'];
        $countUniqueUrls++;
    }

    $logs[] = new Log($line);
}
fclose($file);

$crawlers = countingCrawlers($logs);

foreach ($logs as $log){
    $views++;
    if($log->method() == "POST")
        $traffic += $log->bytes();
}

$json = ['views' => $views, 'urls' => $countUniqueUrls, 'traffic' => $traffic, 'crawlers' => $crawlers, 'statusCodes' => $statusCodes];

$resultJson = json_encode($json, JSON_PRETTY_PRINT);
echo '<pre>';
    echo  $resultJson;
echo '</pre>';

/**
 * @param Log[] $logs
 * @return array crawlers
 */
function countingCrawlers(array $logs): array
{
    $YANDEX_PATTERN = '(YandexBot)';
    $BAIDU_PATTERN = '(Baiduspider)';
    $BING_PATTERN = '(msnbot)';
    $GOOGLE_PATTERN = '(Googlebot)';

    $crawlers = [
        'Google' => 0,
        'Bing' => 0,
        'Baidu' => 0,
        'Yandex' => 0
    ];

    foreach ($logs as $log){
        if(preg_match($GOOGLE_PATTERN, $log->agent())){
            $crawlers['Google'] ++;
        }
        else if(preg_match($BAIDU_PATTERN, $log->agent())){
            $crawlers['Baidu'] ++;
        }
        else if(preg_match($YANDEX_PATTERN, $log->agent())){
            $crawlers['Yandex'] ++;
        }
        else if(preg_match($BING_PATTERN, $log->agent())){
            $crawlers['Bing'] ++;
        }
    }
    return $crawlers;
}