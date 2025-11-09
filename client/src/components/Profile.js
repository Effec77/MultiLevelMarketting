import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';

function Profile() {
  const navigate = useNavigate();
  const [member, setMember] = useState(null);
  const [loading, setLoading] = useState(true);
  const storedMember = JSON.parse(localStorage.getItem('member'));

  useEffect(() => {
    fetchProfile();
  }, []);

  const fetchProfile = async () => {
    try {
      const token = localStorage.getItem('token');
      const res = await axios.get('/api/members/profile', {
        headers: { 'x-auth-token': token }
      });
      setMember(res.data);
      setLoading(false);
    } catch (err) {
      console.error(err);
      setLoading(false);
    }
  };

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('member');
    navigate('/login');
  };

  if (loading) {
    return <div className="loading">Loading...</div>;
  }

  return (
    <div>
      <div className="navbar">
        <h1>MLM System</h1>
        <div className="navbar-right">
          <span>Welcome, {storedMember?.name}</span>
          <button onClick={handleLogout}>Logout</button>
        </div>
      </div>
      
      <div className="container">
        <div className="card">
          <h2>My Profile</h2>
          
          {member && (
            <>
              <div className="profile-row">
                <div className="profile-label">Member Code:</div>
                <div className="profile-value">{member.memberCode}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Name:</div>
                <div className="profile-value">{member.name}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Email:</div>
                <div className="profile-value">{member.email}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Mobile:</div>
                <div className="profile-value">{member.mobile}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Sponsor Code:</div>
                <div className="profile-value">{member.sponsorCode}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Position:</div>
                <div className="profile-value">{member.position}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Left Member:</div>
                <div className="profile-value">{member.leftMember || 'Empty'}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Right Member:</div>
                <div className="profile-value">{member.rightMember || 'Empty'}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Left Count:</div>
                <div className="profile-value">{member.leftCount}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Right Count:</div>
                <div className="profile-value">{member.rightCount}</div>
              </div>
              
              <div className="profile-row">
                <div className="profile-label">Joined Date:</div>
                <div className="profile-value">
                  {new Date(member.createdAt).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                  })}
                </div>
              </div>
            </>
          )}
          
          <Link to="/dashboard" className="btn" style={{ marginTop: '20px', display: 'inline-block', textAlign: 'center', textDecoration: 'none' }}>
            Back to Dashboard
          </Link>
        </div>
      </div>
    </div>
  );
}

export default Profile;
