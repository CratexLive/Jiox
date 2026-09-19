<?php
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mobile'])) {
        $_SESSION['temp_mobile'] = $_POST['mobile'];
        $ch = curl_init('https://api.jio.com/v3/security/otp/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['identifier' => $_POST['mobile']]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'User-Agent: JioTV/4.0.2']);
        curl_exec($ch);
        curl_close($ch);
        $step = 2;
    } elseif (isset($_POST['otp'])) {
        $ch = curl_init('https://api.jio.com/v3/security/otp/verify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['identifier' => $_SESSION['temp_mobile'], 'otp' => $_POST['otp']]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'User-Agent: JioTV/4.0.2']);
        $res = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($res, true);
        if (isset($data['ssoToken'])) {
            $_SESSION['ssoToken'] = $data['ssoToken'];
            header("Location: index.html");
            exit;
        } else {
            $error = 'Invalid OTP. Try again.';
            $step = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>JioTV Login</title>
    <style>body{background:#0f0f0f;color:#fff;font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}form{background:#181818;padding:30px;border-radius:8px;display:flex;flex-direction:column;gap:15px;width:300px;}input,button{padding:12px;border:none;border-radius:4px;}input{background:#222;color:#fff;outline:none;}button{background:#00e676;font-weight:bold;cursor:pointer;}.error{color:#ff5252;font-size:12px;}</style>
</head>
<body>
<form method="POST">
    <?php if(!empty($error)): ?><span class="error"><?php echo $error; ?></span><?php endif; ?>
    <?php if (!isset($step)): ?>
        <h2>JioTV Login</h2>
        <input type="text" name="mobile" placeholder="Jio Mobile Number" required>
        <button type="submit">Send OTP</button>
    <?php else: ?>
        <h2>Enter OTP</h2>
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify OTP</button>
    <?php endif; ?>
</form>
</body>
</html>
