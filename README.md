# MLM System - MERN Stack (Binary Tree)

A complete Multilevel Marketing application built with MERN stack (MongoDB, Express, React, Node.js) featuring a binary tree structure where each member can have one left and one right downline member.

## Features

### Backend (Node.js + Express + MongoDB)
- RESTful API with JWT authentication
- Member registration with validation
- Sponsor code verification
- Automatic spill logic (recursive placement)
- Position availability checking
- Recursive count updates (left_count/right_count)
- Secure password hashing with bcrypt

### Frontend (React)
- Modern React with Hooks
- React Router for navigation
- Responsive design
- Login/Register pages
- Dashboard
- Profile view
- Visual downline tree

## Project Structure

```
mlm-mern-system/
├── server.js              # Express server
├── package.json           # Backend dependencies
├── .env                   # Environment variables
├── models/
│   └── Member.js          # MongoDB Member schema
├── routes/
│   ├── auth.js            # Authentication routes
│   └── members.js         # Member routes
├── middleware/
│   └── auth.js            # JWT authentication middleware
├── scripts/
│   └── seedRoot.js        # Create root member
└── client/                # React frontend
    ├── package.json
    ├── public/
    │   └── index.html
    └── src/
        ├── App.js
        ├── App.css
        ├── index.js
        ├── index.css
        └── components/
            ├── Login.js
            ├── Register.js
            ├── Dashboard.js
            ├── Profile.js
            ├── Downline.js
            └── Downline.css
```

## Installation & Setup

### Prerequisites
- Node.js (v14 or higher)
- MongoDB (local or Atlas)
- npm or yarn

### Step 1: Install MongoDB
Download and install MongoDB from: https://www.mongodb.com/try/download/community

Or use MongoDB Atlas (cloud): https://www.mongodb.com/cloud/atlas

### Step 2: Clone/Setup Project
```bash
# Navigate to project directory
cd mlm-mern-system

# Install backend dependencies
npm install

# Install frontend dependencies
cd client
npm install
cd ..
```

### Step 3: Configure Environment
Edit `.env` file with your settings:
```
PORT=5000
MONGODB_URI=mongodb://localhost:27017/mlm_system
JWT_SECRET=your_jwt_secret_key_change_this
```

### Step 4: Create Root Member
```bash
node scripts/seedRoot.js
```

This creates the admin account:
- Member Code: ROOT001
- Password: password

### Step 5: Run the Application

**Option 1: Run both servers separately**

Terminal 1 (Backend):
```bash
npm run dev
```

Terminal 2 (Frontend):
```bash
cd client
npm start
```

**Option 2: Run both together**
```bash
npm run dev:full
```

### Step 6: Access Application
- Frontend: http://localhost:3000
- Backend API: http://localhost:5000

## Usage

1. **Login**: Use ROOT001 / password
2. **Register New Members**: Click "Register here" and use ROOT001 as sponsor code
3. **View Profile**: See all member details and counts
4. **Check Downline**: Visual tree showing left and right members

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new member
- `POST /api/auth/login` - Login member

### Members (Protected)
- `GET /api/members/profile` - Get member profile
- `GET /api/members/downline` - Get downline tree

## Features Explained

### Validation Logic
1. **Sponsor Code Check**: Verifies sponsor exists in database
2. **Position Availability**: Checks if left/right position is filled
3. **Spill Logic**: If position full, recursively finds next available slot

### Spill Logic
- If sponsor's left position is full and user selected Left → traverse to sponsor's left child
- Check that child's left position
- Continue until finding first empty left slot
- Same logic applies for Right side

### Count Updates
- When new member joins, update left_count or right_count
- Recursively update counts upward through the tree
- Maintains accurate count at each level

## Technologies Used

### Backend
- Node.js
- Express.js
- MongoDB with Mongoose
- JWT for authentication
- bcryptjs for password hashing

### Frontend
- React 18
- React Router v6
- Axios for API calls
- CSS3 for styling

## Default Credentials

- Member Code: ROOT001
- Password: password

## Troubleshooting

**MongoDB Connection Error**
- Make sure MongoDB is running
- Check MONGODB_URI in .env file

**Port Already in Use**
- Change PORT in .env file
- Or stop other applications using port 5000/3000

**Cannot Register Members**
- Make sure you ran seedRoot.js to create ROOT001
- Verify MongoDB is connected

## Security Notes

- Change JWT_SECRET in production
- Use strong passwords
- Enable HTTPS in production
- Add rate limiting for API endpoints
