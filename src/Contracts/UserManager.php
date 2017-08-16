<?php

namespace Authenticate\Contracts;

interface UserManager
{
    /**
     * Perform user search for login.
     *
     * @param string $fieldLoginUsername
     * @param string $user
     * @return UserManager
     */
    public static function getUserBylLoginUsername($fieldLoginUsername = null, $user = null);

}