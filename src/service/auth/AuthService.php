<?php

namespace src\service\auth;

/**
 * Class AuthService
 * @package src\service\product
 */
class AuthService
{
    /**
     * @return bool
     */
    public function checkAuthorization(): bool
    {
        $userId    = 1;
        $userLogin = 'admin';

        return true;
    }
}