# 🎓 ScholarSphere – Scholarship & Financial Aid Portal

**ScholarSphere** is a simple and beginner-friendly portal designed to help students find and apply for scholarships with ease!

---

## 🌟 Project Overview

A full-stack web project that allows students to:
- 🔍 Browse available scholarships
- 📝 Submit scholarship applications with documents
- 📬 Track application status using their email

---

## 🛠️ Tech Stack

| Layer      | Technology               |
|------------|---------------------------|
| 💻 Frontend | HTML, CSS, JavaScript, Bootstrap (optional) |
| ⚙️ Backend  | PHP                       |
| 🗄️ Database | SQL Server (via SSMS)     |
| 🌐 Server   | XAMPP/WAMP (for local use)  |

---

## 📂 Project Structure

```
ScholarSphere/
├── index.php             # Homepage – dynamic scholarship list
├── apply.html            # Application form
├── apply.php             # Handles form submission
├── status.html           # Email input to check application status
├── status.php            # Displays application status
├── thankyou.html         # Confirmation after application
├── db.php                # Database connection file
├── css/
│   └── style.css         # Custom styles
├── uploads/              # Stores uploaded documents
```

---

## 🚀 How to Run

1. 🔧 Install [XAMPP](https://www.apachefriends.org/)
2. ⚙️ Enable SQLSRV driver in your `php.ini` if using SQL Server
3. 📥 Clone or download this repository
4. 🛠️ Place the folder inside `htdocs/` (XAMPP)
5. 🖥️ Visit `http://localhost/ScholarSphere/index.php` in your browser
6. ✅ Make sure your SQL Server database is ready and the connection in `db.php` is correct

---

## ✨ Features

- Responsive homepage listing active scholarships
- Easy-to-use scholarship application form
- Upload functionality for documents (e.g., transcripts, certificates)
- Email-based application status checking (no login required)
- Clean UI and simple backend logic for beginners

---

## 🧑‍💻 Developed By

- **Muneeb Aamir**  
- BS Computer Science Student
- 3 years of Experience with Frontend dev
- Passionate about building helpful tools

---

## ❤️ Contributions

Want to help? Fork the repo, suggest improvements, or report issues.  
**Together, let's simplify access to education!**
