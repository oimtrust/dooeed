const TOKEN_KEY = 'auth_token';
const USER_KEY = 'auth_user';

function readUser() {
    try {
        return JSON.parse(localStorage.getItem(USER_KEY));
    } catch {
        return null;
    }
}

export const authStore = {
    get token() {
        return localStorage.getItem(TOKEN_KEY);
    },

    get user() {
        return readUser();
    },

    get isAuthenticated() {
        return localStorage.getItem(TOKEN_KEY) !== null;
    },

    save({ user, token }) {
        localStorage.setItem(TOKEN_KEY, token);
        localStorage.setItem(USER_KEY, JSON.stringify(user));
    },

    saveUser(user) {
        localStorage.setItem(USER_KEY, JSON.stringify(user));
    },

    clear() {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_KEY);
    },
};
