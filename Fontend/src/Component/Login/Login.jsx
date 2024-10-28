// src/LoginForm.jsx

import React, { useState } from 'react';
import axios from 'axios';

function LoginForm() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const response = await axios.post('http://127.0.0.1:8000/api/superadmin/login', {
        email,
        password,
      });

      console.log('API Response:', response.data);

      // Save token to localStorage
      const token = response.data.token;
      localStorage.setItem('authToken', token);
      
      alert('Login successful! Token saved to localStorage.');

    } catch (error) {
      console.error('Error during login:', error);
      alert('Login failed. Please check your credentials.');
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h2>Login</h2>

      <label>Email:</label>
      <input
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        required
      />

      <label>Password:</label>
      <input
        type="password"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        required
      />

      <button type="submit">Login</button>
    </form>
  );
}

export default LoginForm;
