<?php
session_start();
if (!isset($_SESSION['ssoToken']) || !isset($_GET['id'])) {
    exit("Unauthorized");
}
$id = $_GET['id'];
$ch = curl_init("https://jiotv.jiocloud.com/apis/v1.3/getStream?channel_id=$id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: JioTV/4.0.2 (Linux; Android 13)',
    'ssoToken: ' . $_SESSION['ssoToken']
]);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
echo $data['url'] ?? '';
?>
