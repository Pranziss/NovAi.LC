// =============================================
// Nova Learning Center — auth.js
// Shared frontend auth utility
// Handles: localStorage user, auth guard, logout
// =============================================

const NLC_USER_KEY = 'nlc_user';

// --- Save user to localStorage after login/register ---
function nlcSaveUser(user) {
  localStorage.setItem(NLC_USER_KEY, JSON.stringify(user));
}

// --- Get current user object (or null) ---
function nlcGetUser() {
  try {
    const raw = localStorage.getItem(NLC_USER_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

// --- Clear user (logout) ---
function nlcClearUser() {
  localStorage.removeItem(NLC_USER_KEY);
}

// --- Auth guard: redirect to login if not logged in ---
// Call at top of every protected page
function nlcRequireAuth(redirectTo = '../pages/login.html') {
  const user = nlcGetUser();
  if (!user || !user.id) {
    window.location.href = redirectTo;
    return null;
  }
  return user;
}

// --- Admin guard: redirect to dashboard if not admin ---
function nlcRequireAdmin(redirectTo = 'dashboard.html') {
  const user = nlcGetUser();
  if (!user || !user.is_admin) {
    window.location.href = redirectTo;
    return null;
  }
  return user;
}

// --- Logout: call API + clear local + redirect ---
async function nlcLogout() {
  nlcClearUser();
  try {
    await fetch('../api/auth/logout.php', { method: 'POST' });
  } catch (_) {}
  window.location.href = '../index.html';
}

// --- Attach logout to any element with data-logout ---
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-logout]').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      nlcLogout();
    });
  });
});