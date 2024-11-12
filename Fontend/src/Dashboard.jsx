import React, { useEffect, useState } from 'react';
import { getAdminList, logout } from './api';

const Dashboard = ({ token, setToken }) => {
  const [admins, setAdmins] = useState([]);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchAdmins = async () => {
      try {
        const data = await getAdminList(token);
        setAdmins(data.admins);
      } catch (error) {
        setError('Failed to retrieve admin list');
      }
    };

    fetchAdmins();
  }, [token]);

  const handleLogout = async () => {
    await logout(token);
    setToken(null);
  };

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100">
      <div className="w-full max-w-3xl p-6 bg-white shadow-md rounded-lg">
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-2xl font-bold text-gray-800">Admin Dashboard</h2>
          <button
            onClick={handleLogout}
            className="px-4 py-2 bg-red-500 text-white font-semibold rounded-md hover:bg-red-600 transition duration-200"
          >
            Logout
          </button>
        </div>
        {error && <p className="text-red-600 text-sm mb-4">{error}</p>}
        <ul className="space-y-4">
  {admins.map((admin) => (
    <li
      key={admin.id}
      className="p-4 border border-gray-200 rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300"
    >
      <div className="flex items-center space-x-4">
        {/* <img
          src={`https://sunny.napver.com/${admin.admin_image}`}
          alt={`${admin.name}'s profile`}
          className="w-20 h-20 rounded-full border border-gray-300"
        /> */}
        <div>
          <h3 className="text-lg font-semibold text-gray-700">{admin.name}</h3>
          <p className="text-gray-600">Email: {admin.email}</p>
          <p className="text-gray-600">Phone: {admin.phone}</p>
          <p className="text-gray-600">Address: {admin.address}</p>
          <p className="text-gray-600">NID: {admin.nid}</p>
          <p className="text-gray-600">Status: {admin.status === "1" ? "Active" : "Inactive"}</p>
          <p className="text-gray-600">SMS Count: {admin.sms_count}</p>
          <p className="text-gray-600">Created At: {new Date(admin.created_at).toLocaleDateString()}</p>
          <p className="text-gray-600">Updated At: {new Date(admin.updated_at).toLocaleDateString()}</p>
        </div>
      </div>
    </li>
  ))}
</ul>

      </div>
    </div>
  );
};

export default Dashboard;
