import React from 'react';
import { Link, useNavigate } from 'react-router-dom';

function Dashboard() {
  const navigate = useNavigate();
  const member = JSON.parse(localStorage.getItem('member'));

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('member');
    navigate('/login');
  };

  return (
    <div>
      <div className="navbar">
        <h1>MLM System</h1>
        <div className="navbar-right">
          <span>Welcome, {member?.name}</span>
          <button onClick={handleLogout}>Logout</button>
        </div>
      </div>
      
      <div className="container">
        <div className="card">
          <h2>Dashboard</h2>
          <p>Member Code: <strong>{member?.memberCode}</strong></p>
        </div>
        
        <div className="menu">
          <Link to="/profile">Profile View</Link>
          <Link to="/downline">Check Downline Members</Link>
        </div>
      </div>
    </div>
  );
}

export default Dashboard;
