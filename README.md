<div align="center">
  <img src="https://img.icons8.com/color/96/000000/task--v1.png" alt="Logo">
  <h1>PAKKAdesK 🚀</h1>
  <p>A modern, high-performance Task Management System built with PHP & MySQL.</p>

  <p>
    <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
    <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
  </p>
</div>

---

## 📌 Context
**PAKKAdesK** was developed as an internal project during my **Internship at Pakka Limited**. The objective of this project is to streamline organizational workflow, offering a centralized hub where administrators can assign, track, and manage responsibilities, while users can stay updated on their deadlines without noise.

## ✨ Key Features
- **Dual-Role Architecture**: Distinct interfaces and authentication logic for App Administrators and Standard Users.
- **Admin Command Center**:
  - Track global productivity via analytical dashboard widgets.
  - Dynamically register and manage employee accounts.
  - Create tasks, define priorities (Low/Medium/High), and set hard deadlines.
- **User Dashboard**:
  - Personal kanban-style task tracking.
  - One-click task status updates (Pending ➔ In Progress ➔ Completed).
  - Unread notification bubbling when new tasks are assigned.
- **Lively Modern UI**: Designed purely in Vanilla CSS with glassmorphic effects, robust Inter typography, bouncy micro-animations, and a professional Smoky Pink color palette.

## 🛠️ Technology Stack
- **Backend**: Procedural / Object-Oriented PHP (PDO)
- **Database**: MySQL (Relational Schema)
- **Frontend**: HTML5, Vanilla CSS3, Vanilla JavaScript
- **Security**: BCrypt Password Hashing, Session Hijacking Prevention

## 🚀 Local Setup Instructions
To run this application locally, you will need a PHP server environment like **XAMPP**, **WAMP**, or **Laragon**.

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ashhabakhtar/PakkaDesk.git
   ```
2. **Move to Server Directory:** 
   Move the cloned folder into your server's public directory (e.g., `htdocs` for XAMPP).
3. **Database Initialization:**
   - Open `phpMyAdmin` at `http://localhost/phpmyadmin/`.
   - Import the `database.sql` file located in the root of the project. This will automatically generate the schema and the default administrator account.
4. **Access the platform:**
   - Navigate to `http://localhost/PakkaDesk/index.php`.
   
**Default Administrator Credentials:**
- **Username**: `admin`
- **Password**: `admin123`

---

<div align="center">
  <i>Designed and Built by <b>Ashhab Akhtar</b> | Intern @ Pakka Limited</i>
</div>
