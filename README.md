# MediFul-Medical-Inventory-Management
MediFul is a convenient Inventory Management Software that can be used by Hospitals and Pharmacies to keep track of their medicines' stock.

# 📦 Database Setup Guide
This project requires a MySQL database. Follow the instructions below to set it up.

🛠 Step 1: Create a New Database
Before importing, you need to create a new database.

✅ Using phpMyAdmin
-> Open phpMyAdmin in your browser.
-> Click on Databases at the top.
-> Enter a new database name (e.g., inventory_db).
-> Click Create.

✅ Using MySQL Command Line
Run the following command in your MySQL CLI or terminal:
  CREATE DATABASE inventory_db;


🛠 Step 2: Import the Database

✅ Using phpMyAdmin
-> Select the newly created database (inventory_db).
-> Click on the Import tab.
-> Click Choose File and select database_backup.sql.
-> Click Go to start the import.

✅ Using MySQL Command Line
Run this command in the terminal:
  mysql -u your_username -p inventory_db < database_backup.sql
  
Replace your_username with your MySQL username. You'll be asked for your MySQL password. The database will be imported.


🛠 Step 3: Configure Database Connection
Modify the database connection settings in your project:

✅ For PHP (config.php file)
Update config.php with your database details
  <?php
    $DB_HOST = "localhost";  // Change if using a remote DB
    $DB_USER = "your_username";
    $DB_PASS = "your_password";
    $DB_NAME = "inventory_db";
  ?>
  
✅ For .env (if using environment variables)
If your project uses an .env file, update:
  DB_HOST=localhost
  DB_USER=your_username
  DB_PASS=your_password
  DB_NAME=inventory_db

  
🛠 Step 4: Verify the Connection
After setting up the database, test the connection:

✅ For PHP
Run this script to check if the connection works:
  <?php
    $conn = new mysqli("localhost", "your_username", "your_password", "inventory_db");
    
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    echo "Database connected successfully!";
  ?>
Save it as test_db.php
Run it in the browser: http://localhost/test_db.php
If successful, you’re all set! ✅
