 # Custom Login, registration, forgot password and change password in Laravel
This Laravel application is developed for custom authentication in Laravel without using any Package. It provides Registration, Login, Change Password and Forgot Password. It also does email verification during Registration using MailTrap. Tutorial - https://codehow2.com/laravel/how-to-develop-custom-register-and-login-with-change-password-in-laravel

# How To Use

1) Download the repository from https://github.com/sundarsau/lara_custom_login
2) Extract it into a folder
3) Create a Database in MySQL
4) copy .env.example to .env and update database name, username and password. For example, I used the database lara_demo and updated database details as below:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=lara_demo
    DB_USERNAME=root
    DB_PASSWORD=

5) Run composer install from project root
6) Run php artisan key:generate
7) Run php artisan migrate. This will create Laravel default tables and also will create a custom table called applications. Initially, the table will be empty and after the form is submitted data should be inserted in this table.
8) Run php artisan serve
9) In Browser run localhost:8000
10) Click on New Applicants and submit the form.

# License
This is an MIT license, you can modify the code according to your requirements
