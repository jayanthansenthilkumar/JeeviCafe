# Smart Canteen Demand Prediction & Management System

Welcome to the Smart Canteen Project! I have generated all the necessary code files step-by-step for you to understand how frontend, backend, database, and AI (basic logic) connect in a modern application.

---

## 📁 Where Files Are Located
All files have been successfully written into your project directory `s:\Host\htdocs\Projects\Cant`.
Here is what each file does:
1. **`setup.sql`** - The MySQL database code to build our tables.
2. **`db.php`** - Connects the PHP code to the XAMPP MySQL database.
3. **`style.css`** - Contains all styling rules (Card-based layout, purely solid colors like white, grey, light blue). Absolutely zero gradients are used.
4. **`index.html`** - The User Order Page. Contains the form for students/staff to order food.
5. **`order.php`** - Backend logic receiving User Orders. Instead of redirecting, it replies with a JSON message to show a beautiful **SweetAlert**.
6. **`dashboard.php`** - Admin panel displaying totally clean cards. Shows total orders, most popular food, and a button to run AI predictions. 
7. **`orders.php`** - A simple HTML table that pulls all historical orders from the DB.
8. **`prediction.php`** - Summarizes the Python AI's final output demand. 
9. **`run_prediction.php`** - The bridge! PHP triggers your system's Python locally.
10. **`predict.py`** - The "AI Brain". Reads database orders, calculates average quantities, and saves predicted demand into the database.

---

## 🚀 How to Run the Project

### Step 1: Start XAMPP & Import Database
1. Open up your **XAMPP Control Panel**.
2. Start the **Apache** and **MySQL** modules.
3. Open your browser and go to `http://localhost/phpmyadmin/`.
4. Click on **SQL** tab.
5. Copy the contents of your `setup.sql` file and hit **Go**. *Alternatively, manually create a database named `smart_canteen_db` and import the SQL file.*

### Step 2: Install Python Connector
For Python to talk to XAMPP, open your VS Code terminal and type:
```bash
pip install mysql-connector-python
```

### Step 3: Run in Browser
Since the files are in `htdocs/Projects/Cant`, open your browser to:
**`http://localhost/Projects/Cant/index.html`**

1. Place a few orders to build up data. You will see the SweetAlerts trigger automatically!
2. Click **Dashboard** at the top right to visit the Admin side.
3. Click **Run AI Demand Model** on the dashboard. A loading SweetAlert will appear, PHP will tell Python to run, and wait for Python's result.
4. Python saves the prediction to DB, and you get sent to the prediction page!

---

## 📝 Project Summary (For Submission)
**Project Title:** Smart Canteen Demand Prediction & Management System
**Technologies Used:** HTML/CSS (Frontend), PHP (Backend), MySQL (Database), Python (Data processing unit).
**Description:**
The system is designed to streamline canteen ordering and reduce food waste using basic preventative analytics. Users can place food orders through a responsive graphic interface integrated with SweetAlert2 status messages. The admin dashboard provides real-time tracking of operations (total orders, popular items). A background Python process functions as our logical component; it extracts historical databases, calculates average consumer behavior, and projects an estimated daily requirement (demand prediction) for food items. This assists the kitchen in optimizing their inventory preparations for the next day.

---

## 🎓 Viva / Interview Questions & Answers

**Q1: Why did you use PHP and Python together for this project?**
**Answer:** PHP is excellent at serving web pages and handling HTTP requests quickly, while Python is the industry standard for data science and calculations. By separating them, PHP handles the user experience, while Python natively handles heavy logic in the background when requested.

**Q2: How does the AI Prediction work?**
**Answer:** This demo uses "statistical prediction" rather than a deep neural network. The Python script connects to our MySQL database using `mysql-connector-python`. It extracts historical data, calculates the "Average Quantity Ordered" per item, and factors it by estimated total daily visits to predict absolute demand for the next business day. 

**Q3: How are the beautiful popups working without a page reload?**
**Answer:** We used **AJAX (the `fetch` API)** in JavaScript alongside **SweetAlert2**. When the user clicks "Order", JS intercepts the form, sends the data silently to `order.php`, receives a JSON response (like `{"status": "success"}`), and uses SweetAlert to display the UI popup without refreshing the screen.

**Q4: Explain the CSS design system you implemented.**
**Answer:** We prioritized clean UI/UX over flashy designs. We used a card-based layout inspired by corporate dashboards. We eliminated all gradients and complex shadows, instead using a rigid primary palette of white (`#ffffff`), light grey (`#f4f7f6`), dark grey (`#4a5568`), and a primary solid blue (`#2b6cb0`) to create strong contrast and extreme readability.

**Q5: What happens in `run_prediction.php`?**
**Answer:** PHP uses the built-in function `shell_exec()` or `escapeshellcmd()`. It essentially simulates typing `python predict.py` straight into the server's command-line terminal, which activates the Python interpreter.
