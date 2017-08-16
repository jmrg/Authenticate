<?php

namespace Authenticate;

use Authenticate\Access\AuthenticatesUsers;
use Authenticate\Contracts\UserManager;
use Authenticate\Contracts\Views;

/**
 * Class Authorize
 *
 * Verifica que alguien se encuentre logueado
 * o tenga autorizacion para acceder a
 * otros controladores o metodos.
 *
 * @package Authenticate\Core\Session
 */
abstract class Authorize implements Views
{
    use AuthenticatesUsers;

    /**
     * Mantiene la entidad/modelo con el
     * cual se realizaran las busquedas
     * de usuario.
     *
     * @var mixed
     */
    protected static $entity = null;

    /**
     * Campo a validar en el post incial.
     *
     * @var string
     */
    public $username = 'login';

    /**
     * Define la ubicacion de la vista que contendra
     * el formulario de login.
     *
     * @var string
     */
    public $loginView = 'login/login';

    /**
     * Lugar a donde se redirecciona al usuario
     * al terminar el login.
     *
     * @var string
     */
    public $redirectTo = '/';

    /**
     * Controller constructor.
     * @param UserManager $user
     */
    public function __construct(UserManager $user)
    {
        // Configuracion de la entidad de Usuario
        // para las clases que la utilizaran.
        $this->setEntity($user);
    }

    /**
     * Configura la session bajo un conjunto de
     * parametros especificos y la inicia.
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
     * Configura la entidad con la cual se
     * realizaran las busquedas de
     * usuario.
     *
     * @param UserManager $user
     * @return $this
     */
    public function setEntity(UserManager $user)
    {
        static::$entity = $user;

        return $this;
    }

    /**
     * Verifica que exista un usuario autenticado para
     * continuar con el request.
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
        if (!Session::auth())
            $this->showLoginForm();
    }
}