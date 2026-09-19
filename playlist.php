<?php
session_start();
if (!isset($_SESSION['ssoToken'])) {
    exit("Unauthorized");
}
header('Content-Type: audio/x-mpegurl');
header('Content-Disposition: attachment; filename="jiotv.m3u"');

$ch = curl_init("https://jiotv.jiocloud.com/apis/v1.3/getChannels?os=android&devicetype=phone");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: JioTV/4.0.2 (Linux; Android 13)',
    'ssoToken: ' . $_SESSION['ssoToken']
]);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

echo "#EXTM3U\n";
foreach ($data['result'] ?? [] as $channel) {
    $name = $channel['channel_name'] ?? '';
    $id = $channel['channel_id'] ?? '';
    $logo = $channel['logo'] ?? '';
    $host = $_SERVER['HTTP_HOST'];
    $proto = isset($_SERVER['HTTPS']) ? "https" : "http";
    echo "#EXTINF:-1 tvg-logo=\"https://jiotvimages.cdn.jio.com/dare_images/images/{$logo}\",{$name}\n";
    echo "{$proto}://{$host}/play.php?id={$id}\n";
}
?>
