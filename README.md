# ☕ Jeevi's Cafe - Smart Canteen Architecture

A state-of-the-art, feature-rich Canteen Management System natively built with **PHP, MySQL, and Vanilla CSS/JS** with an exclusive **Gemini-Tailored UI Workflow**. It features dynamic role segregation bridging Students, College Faculty, and Canteen Administrators completely autonomously via an independent, custom-built AI Support Core.

---

## 🌟 Core Features & Modules

### 🤖 Intelligent AI Chatbot Core
Instead of solely relying on the dashboard, users possess a floating AI Assistant capable of bypassing all UI clicks!
*   **Contextual Understanding:** Tell the AI to *"Surprise Me"*, *"Order a Pizza"*, *"Cancel Order"*, or *"Check Balance"*.
*   **Database Integration:** The AI reads the live PHP environment, deducting funds, validating inventory, placing DB orders, and injecting responses autonomously.

### 👥 Hierarchical Triple Role Networking
The overarching system routes traffic into identical structural endpoints separated by heavy restriction barriers.
1.  **College Students:** Track live incoming food via dashboard banners, access community-driven polls, and view exclusive student subsidies/vouchers.
2.  **College Faculty:** Faculty operate on identical functionalities but are dynamically sandboxed to their own URL sub-system (`facultyIndex.php`). Vouchers targeted at Faculty stay strictly bound to them.
3.  **Canteen Admin:** Complete business overhead view. Admins manipulate food toggles, conclude community polls to upgrade highest-voted items to Live Menus natively, and serve generated orders directly from the Kitchen Display Server (KDS).

### 🎁 Voucher & Loyalty Engine
*   Every order grants dynamic mathematical **Loyalty Points**.
*   The generalized Canteen economy is mapped directly onto a **Virtual Wallet**.
*   Admins generate targeted Promos codes restricted by Demographic (`user` or `staff`) capped globally.

### 🎲 'Surprise Me' Roulette Core
An RNG engine triggers whenever a user selects 'Surprise Me' (or asks the Chatbot). The system cross-references wallet limits alongside available active inventory and seamlessly deduces funds to gamble an automated order directly to the kitchen line.

---

## 🛠 Technology Stack
*   **Frontend Endpoints:** HTML5, jQuery / AJAX (for zero-refresh live updating).
*   **Design Framework:** Fully custom 'Vanilla' CSS structured precisely on a strict architectural `--space` grid variable framework with heavy pill-radius shapes, utilizing Google's `Outfit` typography simulating a strict Gemini/Coffee hybrid aesthetic without gradients.
*   **Backend Server:** Raw PHP mapped through an asynchronous unified controller (`backend.php`).
*   **Database:** Relational MySQL architectures manipulating ENUM definitions natively.

---

## 🚀 Setup & Installation Documentation

### 1. Prerequisite Environment
Ensure you have a localized or hosted web stack capable of parsing standard PHP/MySQL. (For example, **XAMPP / WAMP**).
*   Ensure **Apache** & **MySQL** modules are both actively running.

### 2. Physical Deployment
1. Download or git-clone the `JeeviCafe` repository directly into your application mounting folder:
   *   For XAMPP: Drop inside `C:/xampp/htdocs/Projects/Cant/`
2. Never manipulate URLs past `localhost/Projects/Cant/` explicitly. The routing handles file dependencies statically.

### 3. Database Mounting
1. Launch **phpMyAdmin** `http://localhost/phpmyadmin/`.
2. Generate a fresh database named: `smart_canteen_db`.
3. Import the system's finalized SQL dump file. Ensure that your tables build cleanly. Notable integral tables include:
    *   `users` (Contains ENUM: `user`, `admin`, `staff` formatting)
    *   `orders`, `vouchers`, `menu_polls`, `feedback`

### 4. Default Login Hierarchies
If using the default testing DB, deploy these identities to analyze respective portal sandboxes:

| Role Type | Default ID | Native Portal Start-Point |
| :--- | :--- | :--- |
| **Canteen Admin** | `admin` | `/dashboard.php` |
| **College Student** | `student` | `/index.php` |
| **College Faculty** | `staff` | `/facultyIndex.php` |

*Password authentication executes purely against the generated raw database password mapping!*

---

## 🎨 Future Enhancements & Maintainability
*   **Machine Learning (AI Pipeline):** The administrator dashboard currently hosts an `AI & ML Pipelines` link natively triggering python executable modeling logic `shell_exec("python predict.py")` aimed at executing business-volume data predictions over active tables.
*   **Ledger Payouts:** Integrate deeper visualization endpoints tracking total individual item expenditure historically!

**Built proudly with precision routing and tailored UI Grid Alignments!**
