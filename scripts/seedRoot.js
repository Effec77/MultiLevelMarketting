const mongoose = require('mongoose');
const bcrypt = require('bcryptjs');
require('dotenv').config();
const Member = require('../models/Member');

async function seedRoot() {
  try {
    await mongoose.connect(process.env.MONGODB_URI);
    console.log('MongoDB Connected');

    // Check if root exists
    const existingRoot = await Member.findOne({ memberCode: 'ROOT001' });
    if (existingRoot) {
      console.log('Root member already exists');
      process.exit(0);
    }

    // Create root member
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash('password', salt);

    const rootMember = new Member({
      memberCode: 'ROOT001',
      name: 'Admin',
      email: 'admin@mlm.com',
      mobile: '1234567890',
      password: hashedPassword,
      sponsorCode: 'ROOT001',
      position: 'Left'
    });

    await rootMember.save();
    console.log('Root member created successfully');
    console.log('Member Code: ROOT001');
    console.log('Password: password');
    
    process.exit(0);
  } catch (error) {
    console.error('Error:', error);
    process.exit(1);
  }
}

seedRoot();
