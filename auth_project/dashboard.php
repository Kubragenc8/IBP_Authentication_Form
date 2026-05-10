<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

date_default_timezone_set("Europe/Istanbul");

$hour = (int) date("H");

if ($hour >= 5 && $hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour >= 12 && $hour < 17) {
    $greeting = "Good Afternoon";
} elseif ($hour >= 17 && $hour < 22) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}

$fullname = $_SESSION["fullname"] ?? "User";
$email = $_SESSION["email"] ?? "unknown";
$role = $_SESSION["role"] ?? "user";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AuthNest Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: radial-gradient(circle at top left, #5b4bff, #1d1a4f 45%, #090720);
            color: #201b45;
            overflow-x: hidden;
        }

        .scene {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            position: relative;
        }

        .bubble {
            position: fixed;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.35);
            animation: floatUp 7s linear infinite;
            pointer-events: none;
        }

        @keyframes floatUp {
            from {
                transform: translateY(30px) scale(0.7);
                opacity: 0;
            }

            30% {
                opacity: 1;
            }

            to {
                transform: translateY(-130vh) scale(1.5);
                opacity: 0;
            }
        }

        .auth-card {
            width: 100%;
            max-width: 980px;
            min-height: 580px;
            background: white;
            border-radius: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
            position: relative;
            z-index: 2;
        }

        .brand-panel {
            padding: 48px;
            background: linear-gradient(145deg, #27146b, #725dff);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .logo-row {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 900;
        }

        .logo-mark {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.16);
            display: grid;
            place-items: center;
            font-size: 24px;
        }

        .brand-panel h1 {
            font-size: 44px;
            line-height: 1.1;
            margin: 42px 0 18px;
        }

        .brand-panel p {
            color: #e4ddff;
            font-size: 16px;
            line-height: 1.6;
        }

        .mini-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 18px;
            color: #f3efff;
            font-weight: 700;
        }

        .dashboard-panel {
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .success-icon {
            width: 72px;
            height: 72px;
            border-radius: 24px;
            background: #dcfce7;
            color: #15803d;
            display: grid;
            place-items: center;
            font-size: 38px;
            font-weight: 900;
            margin-bottom: 22px;
        }

        .dashboard-panel h2 {
            font-size: 36px;
            line-height: 1.15;
            margin: 0 0 12px;
        }

        .muted {
            color: #706b8f;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .success-box {
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 16px;
            border-radius: 18px;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .info-grid {
            display: grid;
            gap: 12px;
            margin-bottom: 24px;
        }

        .info-box {
            background: #f7f5ff;
            border: 1px solid #e3ddff;
            border-radius: 16px;
            padding: 15px;
        }

        .info-box span {
            display: block;
            color: #706b8f;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .info-box strong {
            color: #201b45;
            word-break: break-word;
        }

        .logout-btn {
            display: block;
            text-align: center;
            text-decoration: none;
            padding: 16px;
            border-radius: 16px;
            color: white;
            background: linear-gradient(135deg, #5138d6, #7b68ee);
            font-size: 17px;
            font-weight: 900;
            transition: 0.2s;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(81, 56, 214, 0.3);
        }

        @media (max-width: 800px) {
            .auth-card {
                grid-template-columns: 1fr;
            }

            .brand-panel,
            .dashboard-panel {
                padding: 32px;
            }

            .brand-panel h1 {
                font-size: 34px;
            }

            .dashboard-panel h2 {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>

<div class="scene">
    <main class="auth-card">

        <section class="brand-panel">
            <div>
                <div class="logo-row">
                    <div class="logo-mark">✦</div>
                    <span>AuthNest</span>
                </div>

                <h1>Your workspace is ready.</h1>

                <p>
                    You are now inside your private account area.
                    Manage your session safely and continue your work.
                </p>
            </div>

            <div class="mini-card">
                Welcome back. Your access has been approved.
            </div>
        </section>

        <section class="dashboard-panel">
            <div class="success-icon">✓</div>

            <h2><?php echo $greeting; ?>, <?php echo htmlspecialchars($fullname); ?></h2>

            <p class="muted">
                You have successfully entered your personal workspace.
            </p>

            <div class="success-box">
                Your account is active and ready to use.
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <span>Full Name</span>
                    <strong><?php echo htmlspecialchars($fullname); ?></strong>
                </div>

                <div class="info-box">
                    <span>Email Address</span>
                    <strong><?php echo htmlspecialchars($email); ?></strong>
                </div>

                <div class="info-box">
                    <span>Account Type</span>
                    <strong><?php echo htmlspecialchars($role); ?></strong>
                </div>
            </div>

            <a href="logout.php" class="logout-btn">Logout</a>
        </section>

    </main>
</div>

<script>
for (let i = 0; i < 18; i++) {
    const bubble = document.createElement("span");
    bubble.className = "bubble";
    bubble.style.left = Math.random() * 100 + "vw";
    bubble.style.bottom = "-30px";
    bubble.style.animationDelay = Math.random() * 7 + "s";
    bubble.style.animationDuration = 5 + Math.random() * 5 + "s";
    document.body.appendChild(bubble);
}
</script>

</body>
</html>