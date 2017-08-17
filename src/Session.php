<?php

namespace Authenticate;

use Authenticate\Contracts\UserManager;

/**
 * Class Session.
 */
abstract class Session
{
    /**
     * Capture the instance of logged-in user.
     *
     * @var UserManager
     */
    protected static $user = null;

    /**
     * Define the timeout default for session in seconds.
     *
     * @var int
     */
    private static $sessionTimeOut = 1800;

    /**
     * Return the value default timeout for session.
     *
     * @return int
     */
    private static function getTimeout()
    {
        return static::$sessionTimeOut;
    }

    /**
     * Setting parameters necessary for start a session.
     */
    public static function setSession()
    {
        // We verify that exist define a constant with the value of the timeout session.
        // If this value nothing defined in a constant then we load the constant predefined in the class.
        $sessionTimeOut = defined('SessionTimeout') ? constant('SessionTimeout') : static::getTimeout();

        // Setting session timeout in that should the server maintenance the data.
        ini_set('session.gc_maxlifetime', $sessionTimeOut);

        // Timeout the cookie in browser.
        ini_set('session.cookie_lifetime', $sessionTimeOut);

        // Setting time life of session id.
        session_set_cookie_params($sessionTimeOut);
    }

    /**
     * Start session.
     */
    public static function start()
    {
        session_start();
    }

    /**
     * Return a UserManager instance when exist
     * a user logged.
     *
     * @param UserManager $user
     */
    final public static function registerUser(UserManager $user)
    {
        $_SESSION['user'] = $user;
    }

    /**
     * Return content of the postion "user" in the global
     * array $_SESSION where is save a instance
     * UserManager.
     *
     * @return UserManager
     */
    protected static function getUser()
    {
        return $_SESSION['user'];
    }

    /**
     * Return the user logged.
     *
     * @return UserManager
     */
    public static function user()
    {
        is_null(static::$user) && static::getUser();

        return static::$user;
    }

    /**
     * Check if a registered user exists.
     *
     * @return bool
     */
    public static function auth()
    {
        return static::user() instanceof UserManager;
    }

    /**
     * End current session and the cookie of
     * the user signed.
     */
    public static function close()
    {
        // If exist a cookie configured, we destroy it.
        if (ini_get('session.use_cookies')) {
            // Capture the parameters of the cookie.
            $params = session_get_cookie_params();

            // Delete all content the cookie and the session.
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        // And finally destroy session.
        session_destroy();
    }

    /**
     * End any session open and clear all
     * trace of the $_SESSION var for
     * start again.
     */
    public static function purge()
    {
        // Close any session open.
        static::close();

        // Setting all again.
        static::setSession();
        static::start();
    }
}
