<?php
session_start();
if (!isset($_SESSION['ssoToken'])) {
    header("Location: login.php");
    exit;
}
$ch = curl_init("https://jiotv.jiocloud.com/apis/v1.3/getChannels?os=android&devicetype=phone");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: JioTV/4.0.2 (Linux; Android 13)',
    'ssoToken: ' . $_SESSION['ssoToken']
]);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
$channels =$data['result'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JioTV Dashboard</title>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        body { margin: 0; background: #0f0f0f; color: #fff; font-family: sans-serif; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 340px; background: #181818; border-right: 1px solid #282828; display: flex; flex-direction: column; height: 100%; }
        .sidebar-header { padding: 15px; border-bottom: 1px solid #282828; display: flex; justify-content: space-between; align-items: center; }
        .search-box { width: 100%; padding: 10px; background: #222; border: none; color: #fff; box-sizing: border-box; }
        .channel-list { flex: 1; overflow-y: auto; }
        .channel-item { display: flex; align-items: center; padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #1f1f1f; gap: 12px; }
        .channel-item:hover { background: #252525; }
        .channel-item img { width: 40px; height: 40px; object-fit: contain; border-radius: 4px; background: #000; }
        .main-content { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px; }
        video { width: 100%; max-width: 900px; border-radius: 8px; background: #000; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>JioTV Channels</h3>
            <a href="playlist.php" style="color:#00e676;text-decoration:none;font-weight:bold;">M3U</a>
        </div>
        <input type="text" class="search-box" id="search" placeholder="Search channels..." onkeyup="filterChannels()">
        <div class="channel-list" id="list">
            <?php foreach ($channels as$ch): ?>
                <div class="channel-item" onclick="loadStream('<?php echo $ch['channel_id']; ?>')">
                    <img src="https://jiotvimages.cdn.jio.com/dare_images/images/<?php echo $ch['logo']; ?>" loading="lazy">
                    <span><?php echo htmlspecialchars($ch['channel_name']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="main-content">
        <video id="player" controls crossorigin playsinline></video>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    <script>
        let player = new Plyr('#player');
        let hls;
        function loadStream(id) {
            fetch(`play.php?id=${id}`).then(res => res.text()).then(url => {
                if (url.startsWith("http")) {
                    const video = document.querySelector('#player');
                    if (Hls.isSupported()) {
                        if (hls) hls.destroy();
                        hls = new Hls();
                        hls.loadSource(url);
                        hls.attachMedia(video);
                        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
                    }
                }
            });
        }
        function filterChannels() {
            let q = document.getElementById('search').value.toLowerCase();
            document.querySelectorAll('.channel-item').forEach(item => {
                item.style.display = item.innerText.toLowerCase().includes(q) ? 'flex' : 'none';
            });
        }
    </script>
</body>
</html>
