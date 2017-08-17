<?php

namespace Authenticate\Access;

use Authenticate\Contracts\UserManager;
use Authenticate\Exceptions\AuthorizeExceptions;
use Authenticate\Session;

trait AuthenticatesUsers
{
    use Request;

    /**
     * Load view login.
     */
    public function showLoginForm()
    {
        $this->view($this->pathLoginView());
    }

    /**
     * Return relative path to view login.
     *
     * @return bool|string
     */
    public function pathLoginView()
    {
        // Verify we the property $loginView is defined for return.
        if (property_exists($this, 'loginView')) {
            return $this->loginView;
        }

        // If not, display error.
        trigger_error('Must define $loginView with path of login.', E_USER_ERROR);

        return false;
    }

    /**
     * Return the fields necessary for
     * make login.
     *
     * @return array
     */
    protected function getCredentials()
    {
        // Capture the params of request.
        $request = $this->getParamsRequest();

        // Then return parameters username and password.
        return [
            $this->loginUsername() => $request[$this->loginUsername()],
            'password'             => $request['password'],
        ];
    }

    /**
     * Verify the credentials at th
     * moment make login.
     *
     * @param array $credentials
     *
     * @return void
     */
    private function hasValidCredentials(array $credentials = [])
    {
        try {
            // Verify exist user through username.
            $user = $this->validateUser($credentials[$this->loginUsername()]);
            $this->validatePassword($user, $credentials['password']);

            // Register the user when exist.
            Session::registerUser($user);

            // Redirecting the desktop to project.
            $this->redirect($this->redirectPath());
        } catch (AuthorizeExceptions $e) {
            // In case error we capture exception
            // and return the login view with
            // message error.
            $this->handlerException($e);
            exit();
        }
    }

    /**
     * Validate if exist of the user.
     *
     * @param string $user
     *
     * @throws AuthorizeExceptions
     *
     * @return mixed
     */
    final public function validateUser($user)
    {
        // We loaded the entity that implement UserManager
        // interface for make a search.
        /** @var UserManager $entity */
        $entity = static::$entity;
        $user = $entity::getUserBylLoginUsername([$this->loginUsername(), $user]);

        // In case the nothing exist user return a exception.
        if (!($user instanceof UserManager)) {
            throw new AuthorizeExceptions('The user not exist.');
        }

        return $user;
    }

    /**
     * Verify if password supplied is coincident
     * with the user password .
     *
     * @param $user
     * @param $pass
     *
     * @throws AuthorizeExceptions
     *
     * @return bool
     */
    public function validatePassword($user, $pass)
    {
        if ($user->password != $this->hash($pass)) {
            throw new AuthorizeExceptions('Password incorrecta.');
        }

        return true;
    }

    /**
     * Return the argument supplied as hash.
     *
     * @param string $string
     *
     * @return string
     */
    protected function hash($string)
    {
        return md5($string);
    }

    /**
     * Return the path where should after
     * login correctly.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return $this->redirectTo;
    }

    /**
     * Manager the view to show errors.
     *
     * @param AuthorizeExceptions|false $e
     */
    final protected function handlerException(AuthorizeExceptions $e = null)
    {
        $this->view(
            $this->pathLoginView(),
            ['error' => $e ? $e->getMessage() : false]
        );
    }

    /**
     * Return field name for login.
     *
     * @return string
     */
    public function loginUsername()
    {
        return property_exists($this, 'username') ?
            $this->username : 'email';
    }

    /**
     * Close the session for after send
     * the view login or any others
     * defined.
     */
    public function logout()
    {
        Session::close();

        $this->redirect($this->pathAfterLogin());
    }

    /**
     * Return the path defined for redirect
     * after login.
     *
     * @return string
     */
    public function pathAfterLogin()
    {
        return property_exists($this, 'redirectAfterLogout') ?
            $this->redirectAfterLogout : '/';
    }
}
