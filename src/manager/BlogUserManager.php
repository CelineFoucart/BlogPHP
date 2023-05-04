<?php

declare(strict_types=1);

namespace App\manager;

use App\Entity\BlogUser;
use App\Manager\AbstractManager;

class BlogUserManager extends AbstractManager
{
    public function findUserAfterLogin(string $username): ?BlogUser
    {
        $sql = $this->getQuery()
            ->select('b.id', 'b.username', 'b.email', 'b.password', 'b.attempts', 'b.last_attempt')
            ->select('r.id AS role_id', 'r.name AS role_name', 'r.alias AS role_alias')
            ->leftJoin('user_role r', 'r.id = b.role_id')
            ->where('b.username = ?')
            ->toSQL()
        ;

        return $this->getBuilder()->fetch($sql, [$username]);
    }

    public function findUserList(): array
    {
        $sql = $this->getQuery()->select('b.id AS id', 'b.username AS username')->orderBy('b.username')->toSQL();

        return $this->getBuilder()->fetchAll($sql);
    }

    public function createUser(BlogUser $user, int $roleId): int
    {
        $query = $this->getQuery();
        $insertSQL =  $query->select('username', 'email', 'password', 'role_id')
            ->setParams([
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'roleId' => (string) $roleId,
            ])
            ->toSQL('insert')
        ;

        return $this->getBuilder()->alter($insertSQL, $query->getParams());
    }

    public function updateAttemps(BlogUser $user): int
    {
        $query = $this->getQuery();
        $updateSQL = $query->select('attempts', 'last_attempt')
            ->setParams([
                'attempts' => $user->getAttempts(),
                'last_attempt' => $user->getLastAttempt() ? $user->getLastAttempt()->format('Y-m-d H:i:s') : NULL,
                'id' => $user->getId(),
            ])
            ->toSQL('update')
        ;

        return $this->getBuilder()->alter($updateSQL, $query->getParams());
    }
}
