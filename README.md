Lab Sheet 4 - Authentication Form

This project is a simple authentication form developed with PHP and MySQL.

The system allows registered users to log in and access a protected dashboard page. If the entered information is incorrect, the user is redirected to an access denied page.

Database Name

ibp_auth_db

Files
database.sql
config.php
login.php
dashboard.php
fraud.php
logout.php
Setup Instructions
Start Apache and MySQL from XAMPP.
Copy the project folder into the htdocs directory.
Open phpMyAdmin.
Import or run the database.sql file.
Open the project from:

http://localhost/IBP_Authentication_Form/login.php

Test User

Email: admin@test.com

Password: 123456

Authentication Logic
If the email and password are correct, the user is redirected to dashboard.php.
If the email or password is incorrect, the attempt is recorded in the fraud table.
After an incorrect login attempt, the user is redirected to fraud.php.
If the login page is opened for the first time, an information message is displayed.
Users who are not logged in cannot directly access the dashboard page.
Database Connection

The database connection is established in config.php.

This file is included in login.php using require_once.

Session Management

After a successful login, user information is stored in session variables.

The dashboard page checks the session before allowing access.

The logout.php file destroys the session and redirects the user back to the login page.

User Interface

The project uses a simple and modern interface.

The login, dashboard, and access denied pages have a consistent visual design.

The custom CSS styling is written inside the PHP files for simplicity.

Conclusion

This project demonstrates a basic authentication system with user login, session control, protected page access, failed login handling, and logout functionality.
