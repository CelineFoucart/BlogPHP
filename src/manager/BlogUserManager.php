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
            ->select('b.id', 'b.username', 'b.email', 'b.password')
            ->select('r.id AS role_id', 'r.name AS role_name', 'r.alias AS role_alias')
            ->leftJoin('user_role r', 'r.id = b.role_id')
            ->where('b.username = ?')
            ->toSQL()
        ;

        return $this->getBuilder()->fetch($sql, [$username]);
    }

    public function createUser(BlogUser $user, int $roleId): int
    {
        $insertSQL = $this->getQuery()
            ->select('username', 'email', 'password', 'role_id')
            ->setParams([
                'username' => $user->getUsername(), 
                'email' => $user->getEmail(), 
                'password' => $user->getPassword(),
                'roleId' => (string)$roleId
            ])
            ->toSQL('insert')
        ;

        return $this->getBuilder()->alter($insertSQL, $this->getQuery()->getParams());
    }
}