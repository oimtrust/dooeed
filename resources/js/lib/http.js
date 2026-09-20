import axios from 'axios';

export const http = axios.create({
    baseURL: '/api/v1',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
});

http.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

http.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401 && localStorage.getItem('auth_token')) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');

            if (!['/login', '/register'].includes(window.location.pathname)) {
                window.location.href = '/login';
            }
        }

        return Promise.reject(error);
    },
);
