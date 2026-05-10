<?php

session_start();
require_once "config.php";

date_default_timezone_set("Europe/Istanbul");

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

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

$message = "Welcome! Please enter your account information.";
$messageType = "info";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email === "" || $password === "") {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } else {
        $passwordHash = hash("sha256", $password);

        $stmt = $pdo->prepare("
            SELECT id, fullname, email, role
            FROM tbl_user
            WHERE email = :email
            AND password_sha256 = :password_sha256
            LIMIT 1
        ");

        $stmt->execute([
            ":email" => $email,
            ":password_sha256" => $passwordHash
        ]);

        $user = $stmt->fetch();

        if ($user) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["fullname"] = $user["fullname"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            header("Location: dashboard.php");
            exit;
        }

        $ipAddress = $_SERVER["REMOTE_ADDR"] ?? "unknown";

        $fraudStmt = $pdo->prepare("
            INSERT INTO fraud
            (attempted_email, attempted_password_sha256, ip_address)
            VALUES
            (:attempted_email, :attempted_password_sha256, :ip_address)
        ");

        $fraudStmt->execute([
            ":attempted_email" => $email,
            ":attempted_password_sha256" => $passwordHash,
            ":ip_address" => $ipAddress
        ]);

        $_SESSION["last_attempt_email"] = $email;

        header("Location: fraud.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AuthNest Login</title>
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

        .form-panel {
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-panel h2 {
            font-size: 38px;
            margin: 0 0 8px;
        }

        .muted {
            color: #706b8f;
            margin-bottom: 22px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-size: 15px;
        }

        .alert.info {
            background: #eeeaff;
            color: #4b35a8;
        }

        .alert.error {
            background: #ffe9ed;
            color: #b0183d;
        }

        label {
            display: block;
            font-weight: 800;
            margin: 14px 0 8px;
        }

        .input-wrap {
            position: relative;
        }

        input {
            width: 100%;
            border: 2px solid #e8e3ff;
            background: #f7f5ff;
            border-radius: 16px;
            padding: 15px;
            font-size: 16px;
            outline: none;
        }

        input:focus {
            border-color: #725dff;
        }

        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            cursor: pointer;
            font-size: 18px;
        }

        .login-btn {
            width: 100%;
            border: 0;
            margin-top: 22px;
            padding: 16px;
            border-radius: 16px;
            color: white;
            background: linear-gradient(135deg, #5138d6, #7b68ee);
            font-size: 17px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.2s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(81, 56, 214, 0.3);
        }

        .test-user {
            margin-top: 18px;
            text-align: center;
            color: #706b8f;
            font-size: 14px;
        }

        .test-user strong {
            color: #5138d6;
        }

        @media (max-width: 800px) {
            .auth-card {
                grid-template-columns: 1fr;
            }

            .brand-panel,
            .form-panel {
                padding: 32px;
            }

            .brand-panel h1 {
                font-size: 34px;
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

                <h1>Your private entrance starts here.</h1>

                <p>
                    A clean and secure login experience designed for students,
                    staff and personal accounts.
                </p>
            </div>

            <div class="mini-card">
                Fast access. Simple design. Protected account area.
            </div>
        </section>

        <section class="form-panel">
            <h2><?php echo $greeting; ?></h2>
            <p class="muted">Sign in to continue to your workspace.</p>

            <div class="alert <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

            <form action="login.php" method="POST" onsubmit="return validateForm();">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    placeholder="admin@test.com"
                >

                <label for="password">Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                    >
                    <button type="button" class="eye-btn" onclick="togglePassword()">👁</button>
                </div>

                <button type="submit" class="login-btn">Enter Dashboard</button>
            </form>

            <p class="test-user">
                Demo account:
                <strong>admin@test.com</strong> /
                <strong>123456</strong>
            </p>
        </section>

    </main>
</div>

<script>
function validateForm() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (email === "" || password === "") {
        alert("Please fill in your email and password.");
        return false;
    }

    return true;
}

function togglePassword() {
    const password = document.getElementById("password");
    password.type = password.type === "password" ? "text" : "password";
}

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