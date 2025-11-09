const mongoose = require('mongoose');

const memberSchema = new mongoose.Schema({
  memberCode: {
    type: String,
    required: true,
    unique: true
  },
  name: {
    type: String,
    required: true
  },
  email: {
    type: String,
    required: true,
    unique: true
  },
  mobile: {
    type: String,
    required: true
  },
  password: {
    type: String,
    required: true
  },
  sponsorCode: {
    type: String,
    required: true
  },
  position: {
    type: String,
    enum: ['Left', 'Right'],
    required: true
  },
  leftMember: {
    type: String,
    default: null
  },
  rightMember: {
    type: String,
    default: null
  },
  leftCount: {
    type: Number,
    default: 0
  },
  rightCount: {
    type: Number,
    default: 0
  },
  createdAt: {
    type: Date,
    default: Date.now
  }
});

module.exports = mongoose.model('Member', memberSchema);
