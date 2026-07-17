<?php
require_once __DIR__.'/includes/db.php';
requireAuth();
$pdo = db();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game = trim($_POST['game'] ?? 'BGMI');
    $duration = (int)($_POST['duration'] ?? 24);
    $maxDevices = (int)($_POST['max_devices'] ?? 1);
    $qty = min((int)($_POST['qty'] ?? 1), 100);
    $note = trim($_POST['note'] ?? '');

    $generated = [];
    for ($i = 0; $i < $qty; $i++) {
        $key = strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4)));
        $expiry = date('Y-m-d H:i:s', time() + $duration * 3600);
        $pdo->prepare("INSERT INTO `keys` (user_key,game,duration,max_devices,expired_date,note) VALUES (?,?,?,?,?,?)")
            ->execute([$key, $game, $duration, $maxDevices, $expiry, $note]);
        $generated[] = $key;
    }
    $msg = '<div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-left:4px solid #22c55e;border-radius:10px;padding:16px;margin-bottom:20px;color:#4ade80;font-size:13px;letter-spacing:1px;">'
        . '&#x2705; Generated ' . $qty . ' key(s):<br><br>'
        . '<div style="background:rgba(0,0,0,0.3);border-radius:8px;padding:12px;font-family:monospace;font-size:13px;color:#f0d080;word-break:break-all;">'
        . implode('<br>', $generated)
        . '</div></div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charan Panel - Generate Key</title>
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
        .container{max-width:500px;margin:30px auto;padding:0 20px;}
        .card{background:rgba(10,2,0,0.95);border:1px solid rgba(200,40,0,0.35);border-radius:16px;overflow:hidden;box-shadow:0 0 50px rgba(255,40,0,0.1),0 30px 80px rgba(0,0,0,0.7);}
        .card::before{content:'';display:block;height:2px;background:linear-gradient(90deg,transparent,rgba(255,100,0,0.8),rgba(255,200,0,0.5),rgba(255,100,0,0.8),transparent);}
        .card-head{padding:18px 24px;border-bottom:1px solid rgba(200,40,0,0.2);display:flex;align-items:center;justify-content:space-between;}
        .card-title{font-family:'Cinzel',serif;font-size:13px;font-weight:900;letter-spacing:3px;color:rgba(255,180,80,0.9);text-transform:uppercase;}
        .badge{padding:4px 12px;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:1px;background:rgba(255,40,0,0.15);border:1px solid rgba(255,80,0,0.3);color:#ff8c00;}
        .card-body{padding:24px;}
        .field{margin-bottom:18px;}
        .field label{display:block;font-family:'Cinzel',serif;font-size:10px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:rgba(255,140,60,0.5);margin-bottom:6px;}
        .field input,.field select,.field textarea{
            width:100%;padding:12px 16px;background:rgba(20,3,0,0.9);border:1px solid rgba(180,40,0,0.35);
            border-radius:10px;color:#f0d080;font-size:14px;font-family:'Exo 2',sans-serif;outline:none;transition:border-color 0.3s;
        }
        .field input:focus,.field select:focus,.field textarea:focus{border-color:rgba(255,80,0,0.7);}
        .field select{cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23ff6622' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;}
        .field select option{background:#1a0300;color:#f0d080;}
        .row2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .btn{width:100%;padding:14px;border:none;border-radius:10px;cursor:pointer;font-family:'Cinzel',serif;font-size:13px;font-weight:900;letter-spacing:4px;text-transform:uppercase;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,0.5);background:linear-gradient(135deg,#cc2200,#ff4400,#ff8800,#ff4400,#cc2200);background-size:300%;box-shadow:0 4px 24px rgba(255,40,0,0.4);animation:btnFlow 4s linear infinite;transition:transform 0.2s,box-shadow 0.2s;}
        @keyframes btnFlow{0%{background-position:0%}100%{background-position:300%}}
        .btn:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(255,40,0,0.6);}
        .result{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:12px;padding:16px;margin-bottom:20px;color:#4ade80;font-size:12px;letter-spacing:1px;}
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">&#x1F451; CHARAN PANEL</div>
        <div class="nav-links">
            <a href="dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a>
            <a href="generate.php" class="active"><i class="bi bi-plus-circle"></i>Generate</a>
            <a href="keys.php"><i class="bi bi-key"></i>Keys</a>
            <a href="logout.php"><i class="bi bi-box-arrow-left"></i>Logout</a>
        </div>
    </div>
    <div class="container">
        <?php if ($msg) echo $msg; ?>
        <div class="card">
            <div class="card-top" style="height:2px;background:linear-gradient(90deg,transparent,rgba(255,100,0,0.8),rgba(255,200,0,0.5),rgba(255,100,0,0.8),transparent);"></div>
            <div class="card-head">
                <div class="card-title">&#x2694; Generate Key</div>
                <span class="badge">NEW</span>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row2">
                        <div class="field">
                            <label>&#x1F3AE; Game</label>
                            <select name="game">
                                <option value="BGMI">BGMI</option>
                                <option value="PUBG">PUBG</option>
                                <option value="BGMI/PUBG">Both</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>&#x23F1; Duration</label>
                            <select name="duration">
                                <option value="1">1 Hour</option>
                                <option value="6">6 Hours</option>
                                <option value="24" selected>1 Day</option>
                                <option value="72">3 Days</option>
                                <option value="168">7 Days</option>
                                <option value="720">30 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="row2">
                        <div class="field">
                            <label>&#x1F4BB; Max Devices</label>
                            <input type="number" name="max_devices" value="1" min="1" max="10">
                        </div>
                        <div class="field">
                            <label>&#x1F4E6; Quantity</label>
                            <input type="number" name="qty" value="1" min="1" max="100">
                        </div>
                    </div>
                    <div class="field">
                        <label>&#x1F4DD; Note (optional)</label>
                        <input type="text" name="note" placeholder="e.g. Customer name, order #">
                    </div>
                    <button type="submit" class="btn">&#x1F511; Generate Key</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
