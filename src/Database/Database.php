<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

/**
 * Creates a new connexion to the database with the database configurations const in the file env.php.
 */
final class Database
{
    /**
     * The host name, for example localhost.
     */
    public const HOST = DB_HOST;

    /**
     * The password for the connection.
     */
    public const PASSWORD = DB_PASSWORD;

    /**
     * The user name for the connection.
     */
    public const USER = DB_USER;

    /**
     * The name of the database.
     */
    public const NAME = DB_NAME;

    /**
     * @var PDO|null PDO is used to perform the query
     */
    private static ?PDO $pdo = null;

    /**
     * Returns an instance of PDO.
     */
    public static function getPDO(): PDO
    {
        if (null === self::$pdo) {
            self::$pdo = new PDO('mysql:host='.self::HOST.';dbname='.self::NAME.';charset=utf8', self::USER, self::PASSWORD);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }

    /**
     * Returns the id of the insertid row.
     */
    public function getLastInsertId(): int
    {
        return (int) self::getPDO()->lastInsertId();
    }
}
