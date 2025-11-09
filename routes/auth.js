const express = require('express');
const router = express.Router();
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const Member = require('../models/Member');

// @route   POST /api/auth/register
// @desc    Register new member
router.post('/register', async (req, res) => {
  try {
    const { name, email, mobile, sponsorCode, position, password } = req.body;

    // Check if email exists
    const existingEmail = await Member.findOne({ email });
    if (existingEmail) {
      return res.status(400).json({ message: 'Email already exists' });
    }

    // Validate sponsor code
    const sponsor = await Member.findOne({ memberCode: sponsorCode });
    if (!sponsor) {
      return res.status(400).json({ message: 'Invalid Sponsor Code.' });
    }

    // Find empty slot with spill logic
    const finalSponsor = await findEmptySlot(sponsorCode, position);
    if (!finalSponsor) {
      return res.status(400).json({ message: 'Unable to find available position' });
    }

    // Generate unique member code
    const memberCode = await generateMemberCode();

    // Hash password
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(password, salt);

    // Create new member
    const newMember = new Member({
      memberCode,
      name,
      email,
      mobile,
      password: hashedPassword,
      sponsorCode: finalSponsor,
      position
    });

    await newMember.save();

    // Update sponsor's left/right member reference
    const updateField = position === 'Left' ? 'leftMember' : 'rightMember';
    await Member.findOneAndUpdate(
      { memberCode: finalSponsor },
      { [updateField]: memberCode }
    );

    // Update counts recursively upward
    await updateCountsUpward(finalSponsor, position);

    res.json({ 
      message: 'Registration successful!', 
      memberCode 
    });

  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   POST /api/auth/login
// @desc    Login member
router.post('/login', async (req, res) => {
  try {
    const { memberCode, password } = req.body;

    const member = await Member.findOne({ memberCode });
    if (!member) {
      return res.status(400).json({ message: 'Invalid credentials' });
    }

    const isMatch = await bcrypt.compare(password, member.password);
    if (!isMatch) {
      return res.status(400).json({ message: 'Invalid credentials' });
    }

    const token = jwt.sign(
      { id: member._id, memberCode: member.memberCode },
      process.env.JWT_SECRET,
      { expiresIn: '7d' }
    );

    res.json({
      token,
      member: {
        memberCode: member.memberCode,
        name: member.name,
        email: member.email
      }
    });

  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
});

// Helper: Generate unique member code
async function generateMemberCode() {
  const lastMember = await Member.findOne().sort({ createdAt: -1 });
  let num = 1;
  
  if (lastMember && lastMember.memberCode !== 'ROOT001') {
    num = parseInt(lastMember.memberCode.substring(3)) + 1;
  }
  
  return 'MEM' + String(num).padStart(6, '0');
}

// Helper: Find empty slot with spill logic
async function findEmptySlot(sponsorCode, position) {
  const sponsor = await Member.findOne({ memberCode: sponsorCode });
  
  if (!sponsor) return null;

  // Check if position is available
  if (position === 'Left' && !sponsor.leftMember) {
    return sponsorCode;
  } else if (position === 'Right' && !sponsor.rightMember) {
    return sponsorCode;
  }

  // Spill logic: traverse recursively
  if (position === 'Left' && sponsor.leftMember) {
    return await findEmptySlot(sponsor.leftMember, position);
  } else if (position === 'Right' && sponsor.rightMember) {
    return await findEmptySlot(sponsor.rightMember, position);
  }

  return null;
}

// Helper: Update counts recursively upward
async function updateCountsUpward(memberCode, position) {
  if (memberCode === 'ROOT001') {
    const updateField = position === 'Left' ? 'leftCount' : 'rightCount';
    await Member.findOneAndUpdate(
      { memberCode },
      { $inc: { [updateField]: 1 } }
    );
    return;
  }

  const updateField = position === 'Left' ? 'leftCount' : 'rightCount';
  await Member.findOneAndUpdate(
    { memberCode },
    { $inc: { [updateField]: 1 } }
  );

  const member = await Member.findOne({ memberCode });
  if (member && member.sponsorCode) {
    await updateCountsUpward(member.sponsorCode, member.position);
  }
}

module.exports = router;
