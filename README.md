# 📚 NovaLC — Nova Learning Center

A web-based quiz management platform with an AI-powered assistant, built as a thesis project. NovaLC lets students and educators create, share, and take quizzes — all in a clean, modern interface with dark mode support.

---

## ✨ Features

- **User Authentication** — Register and log in with secure bcrypt-hashed passwords
- **Dashboard** — View personal stats: quiz sets created, quizzes taken, average score, and best score
- **Quiz Builder** — Create multi-question quiz sets with multiple-choice answers, subject tags, and public/private visibility
- **Quiz Discovery** — Browse and take publicly shared quizzes from other users
- **Nova Assistant** — Floating AI chatbot powered by a local Python backend for in-app help
- **Dark Mode** — System-aware theme toggle across all pages
- **Admin Role** — Role-based access control (`is_admin` flag) for privileged users

---

## 🛠️ Tech Stack

| Layer      | Technology                          |
|------------|--------------------------------------|
| Frontend   | HTML, Tailwind CSS, Vanilla JS       |
| Backend    | PHP (REST API)                       |
| Database   | MySQL via XAMPP / phpMyAdmin         |
| AI Chatbot | Python Flask server (`/ask` endpoint)|
| Fonts      | Plus Jakarta Sans, Inter (Google Fonts) |

---

## 📁 Project Structure

```
NovaLC/
├── index.html                  # Landing page
├── api/
│   ├── config.php              # Database connection
│   ├── auth/
│   │   ├── login.php           # POST /api/auth/login
│   │   ├── register,php        # POST /api/auth/register
│   │   ├── logout.php          # Session logout
│   │   └── session.php         # Session check
│   ├── dashboard/
│   │   └── stats.php           # GET user stats
│   └── quiz/
│       ├── create.php          # POST create quiz set
│       └── get_mine.php        # GET user's quiz sets
├── assets/
│   ├── css/
│   │   ├── style.css           # Custom styles
│   │   └── tailwind.min.css    # Tailwind (local fallback)
│   └── js/
│       ├── auth.js             # Login/register logic
│       ├── chatbot.js          # Nova Assistant chatbot UI
│       ├── main.js             # General utilities
│       └── theme.js            # Dark mode toggle
└── pages/
    ├── login.html              # Login & registration page
    ├── dashboard.html          # User dashboard
    └── quiz-create.html        # Quiz creation form
```

---

## ⚙️ Setup & Installation

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- PHP 7.4+
- A Python environment for the Nova Assistant chatbot (optional)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/NovaLC.git
   ```

2. **Move to your XAMPP `htdocs` folder**
   ```
   C:/xampp/htdocs/NovaLC/
   ```

3. **Create the database**
   - Open phpMyAdmin (`http://localhost/phpmyadmin`)
   - Create a database named `nova_lc`
   - Import the SQL schema (if provided) or create the following tables manually:
     - `users` — `id`, `username`, `email`, `password`, `is_admin`
     - `quiz_sets` — `id`, `user_id`, `title`, `description`, `subject`, `is_public`
     - `questions` — `id`, `quiz_set_id`, `question_text`, `choices` (JSON), `correct_index`
     - `attempts` — `id`, `user_id`, `quiz_set_id`, `score`, `total`

4. **Configure the database** in `api/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');        // Set your MySQL password if any
   define('DB_NAME', 'nova_lc');
   ```

5. **Start Apache and MySQL** in XAMPP Control Panel.

6. **Open the app** at `http://localhost/NovaLC/`

### Nova Assistant (Chatbot)

The chatbot connects to a local Python Flask server. Start your backend and update the API URL in `assets/js/chatbot.js`:

```js
const NOVA_API_URL = "http://127.0.0.1:5000/ask";
// Or replace with your ngrok URL for demos:
// const NOVA_API_URL = "https://your-id.ngrok-free.app/ask";
```

---

## 🔐 Default Credentials

> No default credentials are seeded. Register a new account via the Sign Up page.
> To make a user an admin, manually set `is_admin = 1` in the `users` table via phpMyAdmin.

---

## 🚧 Status

This project is currently under active development as a thesis. Some features (e.g., Discover page, Progress tracking) are still being implemented.

---

## 👨‍💻 Authors

- **[Your Name]** — Developer
- Built as a thesis project

---

## 📄 License

For academic use only. All rights reserved.
