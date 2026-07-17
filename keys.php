<?php
require_once __DIR__.'/includes/db.php';
requireAuth();
$pdo = db();

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM `keys` WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: keys.php?msg=deleted');
    exit;
}
if (isset($_GET['ban'])) {
    $pdo->prepare("UPDATE `keys` SET status=0 WHERE id=?")->execute([(int)$_GET['ban']]);
    header('Location: keys.php?msg=banned');
    exit;
}
if (isset($_GET['unban'])) {
    $pdo->prepare("UPDATE `keys` SET status=1 WHERE id=?")->execute([(int)$_GET['unban']]);
    header('Location: keys.php?msg=unbanned');
    exit;
}

$keys = $pdo->query("SELECT * FROM `keys` ORDER BY id DESC LIMIT 200")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charan Panel - All Keys</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Exo+2:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{background:#050000;color:#f0d080;font-family:'Exo 2',sans-serif;min-height:100vh;}
        body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse at 20% 30%,rgba(180,20,0,0.15) 0%,transparent 60%);pointer-events:none;}
        .topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 24px;background:rgba(10,2,0,0.95);border-bottom:1px solid rgba(200,40,0,0.35);position:sticky;top:0;z-index:100;}
        .brand{font-family:'Cinzel',serif;font-size:14px;font-weight:900;letter-spacing:3px;background:linear-gradient(90deg,#ffaa00,#ff4400,#ffaa00);background-size:200%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;animation:shimmer 3s linear infinite;}
        @keyframes shimmer{0%{background-position:0%}100%{background-position:200%}}
        .nav-links{display:flex;gap:10px;}
        .nav-links a{padding:8px 18px;border-radius:8px;font-family:'Cinzel',serif;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;text-decoration:none;color:rgba(255,150,50,0.8);border:1px solid rgba(255,70,0,0.3);background:rgba(255,30,0,0.07);transition:all 0.25s;}
        .nav-links a:hover,.nav-links a.active{color:#ffcc00;border-color:rgba(255,80,0,0.65);background:rgba(255,60,0,0.16);}
        .nav-links a i{margin-right:4px;}
        .container{max-width:1100px;margin:24px auto;padding:0 20px;}
        .msg{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#4ade80;font-size:12px;letter-spacing:1px;}
        .table-wrap{overflow-x:auto;border-radius:14px;border:1px solid rgba(200,40,0,0.3);background:rgba(10,2,0,0.95);}
        table{width:100%;border-collapse:collapse;min-width:800px;}
        th{font-family:'Cinzel',serif;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,140,60,0.6);padding:14px 16px;text-align:left;border-bottom:1px solid rgba(200,40,0,0.25);background:rgba(20,3,0,0.5);}
        td{padding:12px 16px;font-size:13px;border-bottom:1px solid rgba(200,40,0,0.1);}
        tr:hover td{background:rgba(255,40,0,0.05);}
        .key-text{font-family:'Courier New',monospace;font-weight:700;color:#f0d080;font-size:12px;cursor:pointer;}
        .key-text:hover{color:#ffcc00;}
        .badge-active{background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.3);color:#4ade80;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .badge-banned{background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#f87171;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .badge-expired{background:rgba(255,200,0,0.12);border:1px solid rgba(255,200,0,0.3);color:#ffcc00;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .actions a{display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;transition:all 0.2s;margin-right:4px;}
        .btn-copy{background:rgba(255,150,0,0.12);border:1px solid rgba(255,150,0,0.3);color:#ffcc00;}
        .btn-copy:hover{background:rgba(255,150,0,0.25);}
        .btn-ban{background:rgba(255,50,50,0.12);border:1px solid rgba(255,50,50,0.3);color:#ff6666;}
        .btn-ban:hover{background:rgba(255,50,50,0.25);}
        .btn-unban{background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.3);color:#4ade80;}
        .btn-unban:hover{background:rgba(34,197,94,0.25);}
        .btn-delete{background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#f87171;}
        .btn-delete:hover{background:rgba(239,68,68,0.25);}
        .empty{text-align:center;padding:60px 20px;color:rgba(255,140,60,0.3);font-size:14px;letter-spacing:2px;}
        .toast{position:fixed;bottom:24px;right:24px;background:rgba(10,2,0,0.95);border:1px solid rgba(255,100,0,0.5);border-radius:10px;padding:12px 20px;color:#ffcc00;font-size:12px;font-weight:700;letter-spacing:1px;display:none;z-index:999;}
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">&#x1F451; CHARAN PANEL</div>
        <div class="nav-links">
            <a href="dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a>
            <a href="generate.php"><i class="bi bi-plus-circle"></i>Generate</a>
            <a href="keys.php" class="active"><i class="bi bi-key"></i>Keys</a>
            <a href="logout.php"><i class="bi bi-box-arrow-left"></i>Logout</a>
        </div>
    </div>
    <div class="container">
        <?php if (isset($_GET['msg'])): ?>
            <div class="msg">&#x2705; <?php echo ucfirst(htmlspecialchars($_GET['msg'])); ?> successfully!</div>
        <?php endif; ?>
        <?php if (empty($keys)): ?>
            <div class="empty">&#x1F511; No keys generated yet.<br><a href="generate.php" style="color:#ff8c00;">Generate your first key</a></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Key</th>
                        <th>Game</th>
                        <th>Duration</th>
                        <th>Devices</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($keys as $i => $k): ?>
                    <?php
                        $isExpired = $k['expired_date'] && strtotime($k['expired_date']) < time();
                        $status = $k['status'] == 0 ? 'banned' : ($isExpired ? 'expired' : 'active');
                    ?>
                    <tr>
                        <td style="color:rgba(255,140,60,0.4);"><?php echo $i + 1; ?></td>
                        <td><span class="key-text" onclick="copyKey(this)" title="Click to copy"><?php echo htmlspecialchars($k['user_key']); ?></span></td>
                        <td><?php echo htmlspecialchars($k['game']); ?></td>
                        <td><?php echo $k['duration'] >= 24 ? round($k['duration']/24).'d' : $k['duration'].'h'; ?></td>
                        <td><?php echo $k['max_devices']; ?></td>
                        <td style="font-size:11px;color:rgba(255,140,60,0.5);"><?php echo $k['expired_date'] ? date('d M y', strtotime($k['expired_date'])) : '-'; ?></td>
                        <td>
                            <?php if ($status === 'active'): ?>
                                <span class="badge-active">Active</span>
                            <?php elseif ($status === 'banned'): ?>
                                <span class="badge-banned">Banned</span>
                            <?php else: ?>
                                <span class="badge-expired">Expired</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a class="btn-copy" onclick="copyKey(document.querySelector('[data-key=<?php echo $k[\'id\']; ?>]'))">&#x1F4CB;</a>
                            <?php if ($k['status'] == 1): ?>
                                <a class="btn-ban" href="?ban=<?php echo $k['id']; ?>" onclick="return confirm('Ban this key?')">&#x1F6AB;</a>
                            <?php else: ?>
                                <a class="btn-unban" href="?unban=<?php echo $k['id']; ?>">&#x2705;</a>
                            <?php endif; ?>
                            <a class="btn-delete" href="?delete=<?php echo $k['id']; ?>" onclick="return confirm('Delete this key forever?')">&#x1F5D1;</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <div class="toast" id="toast">&#x2705; Key copied!</div>
    <script>
    function copyKey(el) {
        var key = el.textContent || el.innerText;
        navigator.clipboard.writeText(key).then(function() {
            var t = document.getElementById('toast');
            t.style.display = 'block';
            setTimeout(function(){ t.style.display = 'none'; }, 2000);
        });
    }
    </script>
</body>
</html>
