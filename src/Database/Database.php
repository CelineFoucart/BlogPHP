<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

/**
 * Creates a new connexion to the database.
 */
final class Database
{
    public const HOST = DB_HOST;
    public const PASSWORD = DB_PASSWORD;
    public const USER = DB_USER;
    public const NAME = DB_NAME;
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
