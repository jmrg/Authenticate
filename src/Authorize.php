<?php

namespace Authenticate;

use Authenticate\Access\AuthenticatesUsers;
use Authenticate\Contracts\UserManager;
use Authenticate\Contracts\Views;

/**
 * Class Authorize.
 *
 * Supplies the methods necessary for verify
 * logging and authorization de acceso
 * to a system.
 */
abstract class Authorize implements Views
{
    use AuthenticatesUsers;

    /**
     * Capture a implemented UserManager interface.
     *
     * @var UserManager
     */
    protected static $entity = null;

    /**
     * Field name for login.
     *
     * @var string
     */
    public $username = 'login';

    /**
     * Defines the path of the view with login form.
     *
     * @var string
     */
    public $loginView = 'login/login';

    /**
     * Site to redirect after success login.
     *
     * @var string
     */
    public $redirectTo = '/';

    /**
     * It receive a implementation of UserManager in construct.
     *
     * @param UserManager $user
     */
    public function __construct(UserManager $user)
    {
        // Configuracion de la entidad de Usuario
        // para las clases que la utilizaran.
        $this->setEntity($user);
    }

    /**
     * It settings and start the session.
     *
     * @return $this
     */
    public function setSession()
    {
        // Inciamos la variable de session
        // bajo un conjuntos de parametros.
        Session::setSession();

        // Iniciamos la sesion.
        Session::start();

        return $this;
    }

    /**
     * It save a implementation the UserManager
     * interface for multiple uses.
     *
     * @param UserManager $user
     *
     * @return $this
     */
    public function setEntity(UserManager $user)
    {
        static::$entity = $user;

        return $this;
    }

    /**
     * It verify if a user logged for continue request.
     *
     * @return void
     */
    public function authorize()
    {
        // Si no existe usuario autentica procedemos
        // a validar los request.
        if (!Session::auth()) {
            // En caso de que sea una peticion de tipo GET
            // mostramos la vista de login.
            if ($this->isGet()) {
                $this->showLoginForm();
                exit();
            }

            // Si la peticion es de tipo POST intentamos
            // validar el request.
            if ($this->isPost()) {
                $this->authenticate();
            }
        }
    }

    /**
     * Verify data to login.
     *
     * @return void
     */
    protected function authenticate()
    {
        // Capturamos las credenciales y las enviamos al
        // metodo para realizar la validacion.
        $this->hasValidCredentials($this->getCredentials());

        // Si no hay acceso despues de intentar logeo
        // enviamos a la vista de login.
        if (!Session::auth()) {
            $this->showLoginForm();
        }
    }
}
