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
    <title>Profile - MLM System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .navbar a:hover { text-decoration: underline; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .profile-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .profile-card h2 { color: #333; margin-bottom: 30px; text-align: center; }
        .profile-row { display: flex; padding: 15px 0; border-bottom: 1px solid #eee; }
        .profile-row:last-child { border-bottom: none; }
        .profile-label { font-weight: bold; color: #555; width: 200px; }
        .profile-value { color: #333; flex: 1; }
        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
        .back-btn:hover { background: #5568d3; }
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
        <div class="profile-card">
            <h2>My Profile</h2>
            
            <div class="profile-row">
                <div class="profile-label">Member Code:</div>
                <div class="profile-value"><?php echo htmlspecialchars($member['member_code']); ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Name:</div>
                <div class="profile-value"><?php echo htmlspecialchars($member['name']); ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Email:</div>
                <div class="profile-value"><?php echo htmlspecialchars($member['email']); ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Mobile:</div>
                <div class="profile-value"><?php echo htmlspecialchars($member['mobile']); ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Sponsor Code:</div>
                <div class="profile-value"><?php echo htmlspecialchars($member['sponsor_code']); ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Position:</div>
                <div class="profile-value"><?php echo htmlspecialchars($member['position']); ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Left Member:</div>
                <div class="profile-value"><?php echo $member['left_member'] ? htmlspecialchars($member['left_member']) : 'Empty'; ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Right Member:</div>
                <div class="profile-value"><?php echo $member['right_member'] ? htmlspecialchars($member['right_member']) : 'Empty'; ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Left Count:</div>
                <div class="profile-value"><?php echo $member['left_count']; ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Right Count:</div>
                <div class="profile-value"><?php echo $member['right_count']; ?></div>
            </div>
            
            <div class="profile-row">
                <div class="profile-label">Joined Date:</div>
                <div class="profile-value"><?php echo date('F d, Y', strtotime($member['created_at'])); ?></div>
            </div>
            
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
