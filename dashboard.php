<?php
require_once __DIR__.'/includes/db.php';
requireAuth();
$pdo = db();
$total = $pdo->query("SELECT COUNT(*) FROM `keys`")->fetchColumn();
$active = $pdo->query("SELECT COUNT(*) FROM `keys` WHERE status=1 AND (expired_date IS NULL OR expired_date > NOW())")->fetchColumn();
$today = $pdo->query("SELECT COUNT(*) FROM `keys` WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$expired = $total - $active;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charan Panel - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Exo+2:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{background:#050000;color:#f0d080;font-family:'Exo 2',sans-serif;min-height:100vh;}
        body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse at 20% 30%,rgba(180,20,0,0.15) 0%,transparent 60%);pointer-events:none;}
        .topbar{
            display:flex;align-items:center;justify-content:space-between;
            padding:14px 24px;background:rgba(10,2,0,0.95);
            border-bottom:1px solid rgba(200,40,0,0.35);
            position:sticky;top:0;z-index:100;
        }
        .brand{font-family:'Cinzel',serif;font-size:14px;font-weight:900;letter-spacing:3px;
            background:linear-gradient(90deg,#ffaa00,#ff4400,#ffaa00);background-size:200%;
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;animation:shimmer 3s linear infinite;}
        @keyframes shimmer{0%{background-position:0%}100%{background-position:200%}}
        .nav-links{display:flex;gap:10px;}
        .nav-links a{
            padding:8px 18px;border-radius:8px;font-family:'Cinzel',serif;font-size:10px;font-weight:700;
            letter-spacing:2px;text-transform:uppercase;text-decoration:none;
            color:rgba(255,150,50,0.8);border:1px solid rgba(255,70,0,0.3);
            background:rgba(255,30,0,0.07);transition:all 0.25s;
        }
        .nav-links a:hover,.nav-links a.active{color:#ffcc00;border-color:rgba(255,80,0,0.65);background:rgba(255,60,0,0.16);}
        .nav-links a i{margin-right:4px;}
        .welcome{text-align:center;padding:40px 20px 10px;font-size:13px;color:rgba(255,140,60,0.5);letter-spacing:2px;}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;padding:20px 30px;max-width:900px;margin:0 auto;}
        .card{
            background:rgba(10,2,0,0.9);border:1px solid rgba(200,40,0,0.3);border-radius:14px;
            padding:24px;text-align:center;position:relative;overflow:hidden;
            transition:transform 0.3s;box-shadow:0 8px 30px rgba(0,0,0,0.5);
        }
        .card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;
            background:linear-gradient(90deg,transparent,rgba(255,100,0,0.8),rgba(255,200,0,0.5),rgba(255,100,0,0.8),transparent);}
        .card:hover{transform:translateY(-4px);}
        .card .icon{font-size:2rem;margin-bottom:10px;}
        .card .num{font-family:'Cinzel',serif;font-size:2.2rem;font-weight:900;
            background:linear-gradient(135deg,#ffaa00,#ff4400);-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        .card .label{font-size:11px;color:rgba(255,140,60,0.5);letter-spacing:2px;text-transform:uppercase;margin-top:4px;}
        .quick{max-width:900px;margin:30px auto;padding:0 30px;}
        .quick a{
            display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:10px;
            font-family:'Cinzel',serif;font-size:12px;font-weight:900;letter-spacing:3px;text-transform:uppercase;
            text-decoration:none;color:#fff;background:linear-gradient(135deg,#cc2200,#ff4400,#ff8800,#ff4400,#cc2200);
            background-size:300%;box-shadow:0 4px 24px rgba(255,40,0,0.4);
            animation:btnFlow 4s linear infinite;transition:transform 0.2s,box-shadow 0.2s;margin-right:12px;margin-bottom:12px;
        }
        @keyframes btnFlow{0%{background-position:0%}100%{background-position:300%}}
        .quick a:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(255,40,0,0.6);}
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">&#x1F451; CHARAN PANEL</div>
        <div class="nav-links">
            <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i>Dashboard</a>
            <a href="generate.php"><i class="bi bi-plus-circle"></i>Generate</a>
            <a href="keys.php"><i class="bi bi-key"></i>Keys</a>
            <a href="logout.php"><i class="bi bi-box-arrow-left"></i>Logout</a>
        </div>
    </div>
    <div class="welcome">&#x1F44B; Welcome, <?php echo htmlspecialchars($_SESSION['admin']['username']); ?></div>
    <div class="grid">
        <div class="card">
            <div class="icon">&#x1F511;</div>
            <div class="num"><?php echo $total; ?></div>
            <div class="label">Total Keys</div>
        </div>
        <div class="card">
            <div class="icon">&#x2705;</div>
            <div class="num"><?php echo $active; ?></div>
            <div class="label">Active Keys</div>
        </div>
        <div class="card">
            <div class="icon">&#x1F4C5;</div>
            <div class="num"><?php echo $today; ?></div>
            <div class="label">Today's Keys</div>
        </div>
        <div class="card">
            <div class="icon">&#x274C;</div>
            <div class="num"><?php echo $expired; ?></div>
            <div class="label">Expired/Banned</div>
        </div>
    </div>
    <div class="quick">
        <a href="generate.php"><i class="bi bi-plus-circle"></i> Generate Key</a>
        <a href="keys.php"><i class="bi bi-key"></i> View All Keys</a>
    </div>
</body>
</html>
