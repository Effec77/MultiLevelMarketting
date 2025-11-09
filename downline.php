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

function getDownlineTree($conn, $member_code, $level = 0) {
    $stmt = $conn->prepare("SELECT * FROM members WHERE member_code = ?");
    $stmt->bind_param("s", $member_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return null;
    }
    
    $member = $result->fetch_assoc();
    $stmt->close();
    
    $tree = [
        'member' => $member,
        'level' => $level,
        'left' => null,
        'right' => null
    ];
    
    if ($member['left_member']) {
        $tree['left'] = getDownlineTree($conn, $member['left_member'], $level + 1);
    }
    
    if ($member['right_member']) {
        $tree['right'] = getDownlineTree($conn, $member['right_member'], $level + 1);
    }
    
    return $tree;
}

$tree = getDownlineTree($conn, $member_code);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downline Members - MLM System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .navbar a:hover { text-decoration: underline; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .tree-container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto; }
        .tree-container h2 { color: #333; margin-bottom: 30px; text-align: center; }
        .tree { display: flex; justify-content: center; }
        .tree ul { padding-top: 20px; position: relative; transition: all 0.5s; }
        .tree li { float: left; text-align: center; list-style-type: none; position: relative; padding: 20px 5px 0 5px; transition: all 0.5s; }
        .tree li::before, .tree li::after { content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #ccc; width: 50%; height: 20px; }
        .tree li::after { right: auto; left: 50%; border-left: 2px solid #ccc; }
        .tree li:only-child::after, .tree li:only-child::before { display: none; }
        .tree li:only-child { padding-top: 0; }
        .tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
        .tree li:last-child::before { border-right: 2px solid #ccc; border-radius: 0 5px 0 0; }
        .tree li:first-child::after { border-radius: 5px 0 0 0; }
        .tree ul ul::before { content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #ccc; width: 0; height: 20px; }
        .tree li a { border: 2px solid #667eea; padding: 10px 15px; text-decoration: none; color: #333; font-family: arial, verdana, tahoma; font-size: 12px; display: inline-block; border-radius: 5px; transition: all 0.5s; background: #fff; }
        .tree li a:hover, .tree li a:hover+ul li a { background: #667eea; color: #fff; border: 2px solid #667eea; }
        .tree li a:hover+ul li::after, .tree li a:hover+ul li::before, .tree li a:hover+ul::before, .tree li a:hover+ul ul::before { border-color: #667eea; }
        .member-box { min-width: 120px; }
        .member-name { font-weight: bold; margin-bottom: 5px; }
        .member-code { font-size: 11px; color: #666; }
        .member-count { font-size: 10px; color: #999; margin-top: 5px; }
        .back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
        .back-btn:hover { background: #5568d3; }
        .empty-slot { border: 2px dashed #ccc !important; color: #999 !important; }
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
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        
        <div class="tree-container">
            <h2>Downline Members (Left & Right)</h2>
            
            <div class="tree">
                <?php
                function renderTree($node) {
                    if (!$node) return '';
                    
                    $member = $node['member'];
                    $hasChildren = ($node['left'] || $node['right']);
                    
                    echo '<ul>';
                    echo '<li>';
                    echo '<a href="#">';
                    echo '<div class="member-box">';
                    echo '<div class="member-name">' . htmlspecialchars($member['name']) . '</div>';
                    echo '<div class="member-code">' . htmlspecialchars($member['member_code']) . '</div>';
                    echo '<div class="member-count">L: ' . $member['left_count'] . ' | R: ' . $member['right_count'] . '</div>';
                    echo '</div>';
                    echo '</a>';
                    
                    if ($hasChildren) {
                        echo '<ul>';
                        
                        // Left child
                        if ($node['left']) {
                            renderTree($node['left']);
                        } else {
                            echo '<li><a href="#" class="empty-slot"><div class="member-box"><div class="member-name">Empty</div><div class="member-code">Left Slot</div></div></a></li>';
                        }
                        
                        // Right child
                        if ($node['right']) {
                            renderTree($node['right']);
                        } else {
                            echo '<li><a href="#" class="empty-slot"><div class="member-box"><div class="member-name">Empty</div><div class="member-code">Right Slot</div></div></a></li>';
                        }
                        
                        echo '</ul>';
                    }
                    
                    echo '</li>';
                    echo '</ul>';
                }
                
                if ($tree) {
                    renderTree($tree);
                } else {
                    echo '<p>No downline members found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
