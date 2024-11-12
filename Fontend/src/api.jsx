import axios from 'axios';

const API = axios.create({
    baseURL: 'https://sunny.napver.com/api',
});

// Function to log in and save the token
export const login = async (email, password) => {
    const response = await API.post('/superadmin/login', {
        email,
        password
    });
    return response.data;
};

// Function to get the admin list with the token
export const getAdminList = async (token) => {
    const response = await API.get('/admin/list', {
        headers: {
            Authorization: `Bearer ${token}`
        },
    });
    return response.data;
};

// Function to log out
export const logout = async (token) => {
    await API.post('/superadmin/logout', {}, {
        headers: {
            Authorization: `Bearer ${token}`
        },
    });
};