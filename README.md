# BlogPHP

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/062c6e63a89e47be9f482c26526b3279)](https://app.codacy.com/gh/CelineFoucart/BlogPHP/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

This project is a small blog developed with PHP, as part of the Openclassrooms Program PHP/Symfony.

## Getting Started

### Prerequisites

* PHP 8.0
* composer
* MariaDB / MySQL

### Installation

Install the project and the dependencies:

```sh
git clone https://github.com/CelineFoucart/BlogPHP.git
composer install
```

### Configurations

1. Configure the database in the file env.php.
2. Create the database which must have the name given to the const DB_NAME in env.php.
3. In phpmyadmin, import in the database the sql file in the folder **/database**. It creates the tables and the required role of users.
4. Create an account in the registration page of the application.
5. In phpmyadmin, change the value of the **user_id** in the table **blog_user** to the new account, set 1 to create an admin user.

## License

Distributed under the MIT License. See `LICENSE` for more information.
