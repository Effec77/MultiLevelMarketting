<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['member_code'])) {
    header('Location: login.php');
    exit();
}

$member_code = $_SESSION['member_code'];

// Get member details
$stmt = $conn->prepare("SELECT * FROM members WHERE member_code = ?");
$stmt->bind_param("s", $member_code);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MLM System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .navbar a:hover { text-decoration: underline; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .menu { display: flex; gap: 20px; margin-bottom: 30px; }
        .menu a { padding: 12px 24px; background: white; color: #667eea; text-decoration: none; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .menu a:hover { background: #667eea; color: white; }
        .welcome { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .welcome h2 { color: #333; margin-bottom: 10px; }
        .welcome p { color: #666; font-size: 16px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>MLM System</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($member['name']); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome">
            <h2>Dashboard</h2>
            <p>Member Code: <strong><?php echo htmlspecialchars($member['member_code']); ?></strong></p>
        </div>
        
        <div class="menu">
            <a href="profile.php">Profile View</a>
            <a href="downline.php">Check Downline Members</a>
        </div>
    </div>
</body>
</html>
