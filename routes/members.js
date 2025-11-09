const express = require('express');
const router = express.Router();
const Member = require('../models/Member');
const auth = require('../middleware/auth');

// @route   GET /api/members/profile
// @desc    Get member profile
router.get('/profile', auth, async (req, res) => {
  try {
    const member = await Member.findOne({ memberCode: req.user.memberCode })
      .select('-password');
    
    if (!member) {
      return res.status(404).json({ message: 'Member not found' });
    }

    res.json(member);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   GET /api/members/downline
// @desc    Get downline tree
router.get('/downline', auth, async (req, res) => {
  try {
    const tree = await getDownlineTree(req.user.memberCode);
    res.json(tree);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Helper: Get downline tree recursively
async function getDownlineTree(memberCode, level = 0) {
  const member = await Member.findOne({ memberCode }).select('-password');
  
  if (!member) return null;

  const tree = {
    member: member,
    level: level,
    left: null,
    right: null
  };

  if (member.leftMember) {
    tree.left = await getDownlineTree(member.leftMember, level + 1);
  }

  if (member.rightMember) {
    tree.right = await getDownlineTree(member.rightMember, level + 1);
  }

  return tree;
}

module.exports = router;
