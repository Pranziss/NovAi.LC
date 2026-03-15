// =====================
// Nova Learning Center
// Theme Toggle (Light/Dark)
// =====================

const html = document.documentElement;

// Apply saved theme on page load
const saved = localStorage.getItem('nlc-theme');
if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
  html.classList.add('dark');
}

// Toggle between light and dark
function toggleTheme() {
  html.classList.toggle('dark');
  localStorage.setItem('nlc-theme', html.classList.contains('dark') ? 'dark' : 'light');
}