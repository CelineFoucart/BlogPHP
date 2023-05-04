# BlogPHP
This project is a small blog developped with PHP, as part of the Openclassrooms Program PHP/Symfony.

## Getting Started

### Prerequisites

* PHP 8.0
* composer
* MariaDB / MySQL

### Installation

1. Install the project and the dependencies:
```sh
git clone https://github.com/CelineFoucart/BlogPHP.git
composer install
```
2. Configure the database in the file env.php.
3. Create the database which must have the name given to the const DB_NAME in env.php.
4. In phpmyadmin, import in the database the sql file in the folder **/database**. It creates the tables and the required role of users.
5. Create an account in the registration page of the application.
6. In phpmyadmin, change the value of the **user_id** in the table **blog_user** to the new account, set 1 to create an admin user.

## License

Distributed under the MIT License. See `LICENSE` for more information.