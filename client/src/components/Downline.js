import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';
import './Downline.css';

function Downline() {
  const navigate = useNavigate();
  const [tree, setTree] = useState(null);
  const [loading, setLoading] = useState(true);
  const storedMember = JSON.parse(localStorage.getItem('member'));

  useEffect(() => {
    fetchDownline();
  }, []);

  const fetchDownline = async () => {
    try {
      const token = localStorage.getItem('token');
      const res = await axios.get('/api/members/downline', {
        headers: { 'x-auth-token': token }
      });
      setTree(res.data);
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

  const renderTree = (node) => {
    if (!node) return null;

    const member = node.member;
    const hasChildren = node.left || node.right;

    return (
      <ul>
        <li>
          <div className="member-node">
            <div className="member-name">{member.name}</div>
            <div className="member-code">{member.memberCode}</div>
            <div className="member-count">
              L: {member.leftCount} | R: {member.rightCount}
            </div>
          </div>
          
          {hasChildren && (
            <ul>
              {node.left ? (
                renderTree(node.left)
              ) : (
                <li>
                  <div className="member-node empty-slot">
                    <div className="member-name">Empty</div>
                    <div className="member-code">Left Slot</div>
                  </div>
                </li>
              )}
              
              {node.right ? (
                renderTree(node.right)
              ) : (
                <li>
                  <div className="member-node empty-slot">
                    <div className="member-name">Empty</div>
                    <div className="member-code">Right Slot</div>
                  </div>
                </li>
              )}
            </ul>
          )}
        </li>
      </ul>
    );
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
        <Link to="/dashboard" className="btn" style={{ marginBottom: '20px', display: 'inline-block', textAlign: 'center', textDecoration: 'none', width: 'auto', padding: '10px 20px' }}>
          Back to Dashboard
        </Link>
        
        <div className="card">
          <h2>Downline Members (Left & Right)</h2>
          
          <div className="tree-container">
            <div className="tree">
              {tree ? renderTree(tree) : <p>No downline members found.</p>}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Downline;
