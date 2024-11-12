import React, { useState, useEffect } from 'react';
import Login from './Login.jsx';
import Dashboard from './Dashboard.jsx';

const App = () => {
  const [token, setToken] = useState(localStorage.getItem('token'));

  useEffect(() => {
    if (token) {
      localStorage.setItem('token', token);
    } else {
      localStorage.removeItem('token');
    }
  }, [token]);

  return (
    <div>
      {token ? (
        <Dashboard token={token} setToken={setToken} />
      ) : (
        <Login setToken={setToken} />
      )}
    </div>
  );
};

export default App;
