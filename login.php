<?php
session_start();
require_once __DIR__.'/includes/db.php';
if (!empty($_SESSION['admin'])) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if ($u === OWNER_USER && $p === OWNER_PASS) {
        $_SESSION['admin'] = ['id' => 0, 'username' => $u];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charan Panel - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Exo+2:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            min-height:100vh; display:flex; align-items:center; justify-content:center;
            background:#050000;
            font-family:'Exo 2',sans-serif;
        }
        body::before {
            content:''; position:fixed; inset:0;
            background: radial-gradient(ellipse at 20% 30%, rgba(180,20,0,0.2) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 70%, rgba(120,10,0,0.15) 0%, transparent 60%);
            pointer-events:none;
        }
        .login-box {
            width:380px; padding:40px 32px; position:relative;
            background:rgba(10,2,0,0.95);
            border:1px solid rgba(200,40,0,0.35);
            border-radius:16px;
            box-shadow:0 0 60px rgba(255,40,0,0.12), 0 30px 80px rgba(0,0,0,0.8);
        }
        .login-box::before {
            content:''; position:absolute; top:0; left:0; right:0; height:2px;
            background:linear-gradient(90deg, transparent, rgba(255,100,0,0.8), rgba(255,200,0,0.5), rgba(255,100,0,0.8), transparent);
        }
        .logo { text-align:center; margin-bottom:8px; font-size:2rem; }
        .title {
            text-align:center; font-family:'Cinzel',serif; font-size:18px; font-weight:900;
            letter-spacing:4px; margin-bottom:6px;
            background:linear-gradient(90deg,#ffaa00,#ff4400,#ffaa00);
            background-size:200%;
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            animation:shimmer 3s linear infinite;
        }
        @keyframes shimmer { 0%{background-position:0%} 100%{background-position:200%} }
        .sub { text-align:center; color:rgba(255,140,60,0.4); font-size:11px; letter-spacing:3px; margin-bottom:28px; }
        .field { margin-bottom:18px; }
        .field label {
            display:block; font-size:10px; font-weight:700; letter-spacing:3px;
            text-transform:uppercase; color:rgba(255,140,60,0.5); margin-bottom:6px;
            font-family:'Cinzel',serif;
        }
        .field input {
            width:100%; padding:12px 16px; background:rgba(20,3,0,0.9);
            border:1px solid rgba(180,40,0,0.35); border-radius:10px;
            color:#f0d080; font-size:14px; font-family:'Exo 2',sans-serif;
            outline:none; transition:border-color 0.3s;
        }
        .field input:focus { border-color:rgba(255,80,0,0.7); }
        .field input::placeholder { color:rgba(255,140,60,0.25); }
        .btn {
            width:100%; padding:13px; border:none; border-radius:10px;
            font-family:'Cinzel',serif; font-size:13px; font-weight:900;
            letter-spacing:4px; text-transform:uppercase; cursor:pointer;
            background:linear-gradient(135deg,#cc2200,#ff4400,#ff8800,#ff4400,#cc2200);
            background-size:300%; color:#fff; text-shadow:0 1px 3px rgba(0,0,0,0.5);
            box-shadow:0 4px 24px rgba(255,40,0,0.4);
            animation:btnFlow 4s linear infinite;
            transition:transform 0.2s, box-shadow 0.2s;
        }
        @keyframes btnFlow { 0%{background-position:0%} 100%{background-position:300%} }
        .btn:hover { transform:translateY(-2px); box-shadow:0 8px 32px rgba(255,40,0,0.6); }
        .btn:active { transform:scale(0.98); }
        .error {
            background:rgba(255,40,40,0.1); border:1px solid rgba(255,40,40,0.3);
            border-radius:8px; padding:10px; margin-bottom:16px; text-align:center;
            color:#ff6666; font-size:12px; letter-spacing:1px;
        }
        .support { text-align:center; margin-top:20px; font-size:11px; color:rgba(255,140,60,0.3); }
        .support a { color:rgba(255,140,60,0.5); text-decoration:none; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">&#x1F451;</div>
        <div class="title">CHARAN PANEL</div>
        <div class="sub">BGMI / PUBG KEY MANAGER</div>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="field">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required autofocus>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn">&#x1F511; LOGIN</button>
        </form>
        <div class="support">Support: <a href="https://t.me/saicharan1537">@saicharan1537</a></div>
    </div>
</body>
</html>
