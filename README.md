APHOA – Anak Pawis Homeowners’ Association Portal

This repository contains the official implementation of the Anak Pawis Homeowners’ Association Portal (APHOA), developed as part of my Capstone Project during my Bachelor of Science in Information Technology degree.

How to Install and Run

Clone or Download the Repository

git clone https://github.com/your-username/aphoa.git


Install XAMPP (or any PHP + MySQL server).
Make sure Apache and MySQL are running.

Set Up the Database

Open phpMyAdmin (http://localhost/phpmyadmin
).

Create a new database called aphoadb.

Import the SQL file located inside the db folder of this project.

Update the Config File (if needed)
Open config.php and check the database connection:

$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'aphoadb';


Run the Project

Copy the project folder into your XAMPP htdocs directory.

Open in your browser:

http://localhost/aphoa

Default Login Accounts

You can use the following accounts to log in after setup:

Admin
Username: admin
Password: admin

Officer
Username: officer
Password: officer

Member
Username: member
Password: member
