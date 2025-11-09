# Multilevel Marketing (MLM) System - Binary Tree

A complete MLM application with binary tree structure where each member can have one left and one right downline member.

## Features

1. **Member Registration Form**
   - Name, Email, Mobile, Sponsor Code, Position (Left/Right)
   - Automatic member code generation
   - Password encryption

2. **Validation Logic**
   - Sponsor code verification
   - Position availability check
   - Automatic spill logic (recursive placement)

3. **Spill Logic**
   - If selected position is full, automatically finds next available slot
   - Traverses recursively through the tree

4. **Member Management**
   - Unique member code generation
   - Left/Right member tracking
   - Recursive count updates (left_count/right_count)

5. **Login System**
   - Secure authentication
   - Session management

6. **Dashboard Features**
   - Profile view with all member details
   - Downline tree visualization (Left & Right members)
   - Count tracking for both sides

## Installation

1. **Database Setup**
   - Import `database.sql` into MySQL
   - Default admin credentials:
     - Member Code: ROOT001
     - Password: password

2. **Configuration**
   - Update `config.php` with your database credentials
   - Default settings:
     - Host: localhost
     - User: root
     - Password: (empty)
     - Database: mlm_system

3. **Run Application**
   - Place files in your web server directory (htdocs/www)
   - Access via browser: http://localhost/your-folder/

## Usage

1. **Login**: Use ROOT001 / password for admin access
2. **Register New Members**: Use register.php with valid sponsor code
3. **View Profile**: See all member details and counts
4. **Check Downline**: Visual tree showing left and right members

## File Structure

- `index.php` - Entry point (redirects to login/dashboard)
- `login.php` - Login page
- `register.php` - Member registration with validation
- `dashboard.php` - Main dashboard
- `profile.php` - Member profile view
- `downline.php` - Downline tree visualization
- `logout.php` - Logout handler
- `config.php` - Database configuration
- `database.sql` - Database schema

## Technical Details

- PHP 7.4+
- MySQL 5.7+
- Session-based authentication
- Prepared statements for security
- Recursive tree traversal
- Transaction support for data integrity
