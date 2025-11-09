<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $sponsor_code = trim($_POST['sponsor_code']);
    $position = $_POST['position'];
    $password = $_POST['password'];
    
    // Validate sponsor code
    $stmt = $conn->prepare("SELECT member_code, left_member, right_member FROM members WHERE member_code = ?");
    $stmt->bind_param("s", $sponsor_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $error = "Invalid Sponsor Code.";
    } else {
        $sponsor = $result->fetch_assoc();
        
        // Check position availability and find empty slot
        $final_sponsor = findEmptySlot($conn, $sponsor_code, $position);
        
        if ($final_sponsor === false) {
            $error = "Unable to find available position. Please try again.";
        } else {
            // Generate unique member code
            $member_code = generateMemberCode($conn);
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Insert new member
                $stmt = $conn->prepare("INSERT INTO members (member_code, name, email, mobile, password, sponsor_code, position) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $member_code, $name, $email, $mobile, $hashed_password, $final_sponsor, $position);
                $stmt->execute();
                
                // Update sponsor's left/right member reference
                if ($position == 'Left') {
                    $stmt = $conn->prepare("UPDATE members SET left_member = ? WHERE member_code = ?");
                } else {
                    $stmt = $conn->prepare("UPDATE members SET right_member = ? WHERE member_code = ?");
                }
                $stmt->bind_param("ss", $member_code, $final_sponsor);
                $stmt->execute();
                
                // Update counts recursively upward
                updateCountsUpward($conn, $final_sponsor, $position);
                
                $conn->commit();
                $success = "Registration successful! Your member code is: <strong>$member_code</strong>";
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
    $stmt->close();
}

function generateMemberCode($conn) {
    $stmt = $conn->prepare("SELECT member_code FROM members ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $last = $result->fetch_assoc();
        $num = intval(substr($last['member_code'], 3)) + 1;
    } else {
        $num = 1;
    }
    
    return 'MEM' . str_pad($num, 6, '0', STR_PAD_LEFT);
}

function findEmptySlot($conn, $sponsor_code, $position) {
    $stmt = $conn->prepare("SELECT member_code, left_member, right_member FROM members WHERE member_code = ?");
    $stmt->bind_param("s", $sponsor_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    $sponsor = $result->fetch_assoc();
    
    // Check if position is available
    if ($position == 'Left' && $sponsor['left_member'] == null) {
        return $sponsor_code;
    } elseif ($position == 'Right' && $sponsor['right_member'] == null) {
        return $sponsor_code;
    }
    
    // Spill logic: traverse recursively
    if ($position == 'Left' && $sponsor['left_member'] != null) {
        return findEmptySlot($conn, $sponsor['left_member'], $position);
    } elseif ($position == 'Right' && $sponsor['right_member'] != null) {
        return findEmptySlot($conn, $sponsor['right_member'], $position);
    }
    
    return false;
}

function updateCountsUpward($conn, $member_code, $position) {
    if ($member_code == 'ROOT001') {
        // Update root and stop
        if ($position == 'Left') {
            $conn->query("UPDATE members SET left_count = left_count + 1 WHERE member_code = '$member_code'");
        } else {
            $conn->query("UPDATE members SET right_count = right_count + 1 WHERE member_code = '$member_code'");
        }
        return;
    }
    
    // Update current member's count
    if ($position == 'Left') {
        $conn->query("UPDATE members SET left_count = left_count + 1 WHERE member_code = '$member_code'");
    } else {
        $conn->query("UPDATE members SET right_count = right_count + 1 WHERE member_code = '$member_code'");
    }
    
    // Get parent and position
    $stmt = $conn->prepare("SELECT sponsor_code, position FROM members WHERE member_code = ?");
    $stmt->bind_param("s", $member_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
        updateCountsUpward($conn, $member['sponsor_code'], $member['position']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration - MLM System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 500px; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        input:focus, select:focus { outline: none; border-color: #667eea; }
        .btn { width: 100%; padding: 12px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px; }
        .btn:hover { background: #5568d3; }
        .error { background: #fee; color: #c33; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #fcc; }
        .success { background: #efe; color: #3c3; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #cfc; }
        .link { text-align: center; margin-top: 20px; }
        .link a { color: #667eea; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Member Registration</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Mobile</label>
                <input type="text" name="mobile" required>
            </div>
            
            <div class="form-group">
                <label>Sponsor Code</label>
                <input type="text" name="sponsor_code" required>
            </div>
            
            <div class="form-group">
                <label>Position</label>
                <select name="position" required>
                    <option value="">Select Position</option>
                    <option value="Left">Left</option>
                    <option value="Right">Right</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
        
        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
