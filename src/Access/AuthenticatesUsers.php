<?php

namespace Authenticate\Access;

use Authenticate\Contracts\UserManager;
use Authenticate\Exceptions\AuthorizeExceptions;
use Authenticate\Session;

trait AuthenticatesUsers
{
    use Request;

    /**
     * Carga la vista de login.
     */
    public function showLoginForm()
    {
        $this->view($this->pathLoginView());
    }

    /**
     * Devuelve el path relativo de la vista
     * de login.
     *
     * @return bool|string
     */
    public function pathLoginView()
    {
        // Verificamos que el atributo $loginView se
        // encuentre definido para retornarlo
        if (property_exists($this, 'loginView'))
            return $this->loginView;

        // De no estarlo imprimimos un error a nivel de usuario.
        trigger_error('Debe definir la propiedad $loginView con el path de login.', E_USER_ERROR);

        return false;
    }

    /**
     * Devuelve los campos necesarios para
     * realizar un login.
     *
     * @return array
     */
    protected function getCredentials()
    {
        // Capturamos los parametros del request.
        $request = $this->getParamsRequest();

        //  Capturamos el login y el password.
        return [
            $this->loginUsername() => $request[$this->loginUsername()],
            'password'             => $request['password'],
        ];
    }

    /**
     * Logea con los parametros de usuario y
     * contraseña recibidos.
     *
     * @param array $credentials
     * @return void
     */
    private function hasValidCredentials(array $credentials = [])
    {
        try {
            // Verificamos el usuario y el password.
            $user = $this->validateUser($credentials[$this->loginUsername()]);
            $this->validatePassword($user, $credentials['password']);

            // Segistramos el usuario.
            Session::registerUser($user);

            // Redireccionamos al escritorio del proyecto.
            $this->redirect($this->redirectPath());
        } catch (AuthorizeExceptions $e) {
            // En caso de error capturamos la excepcion y
            // Y devolvemos la vista de login con
            // el error
            $this->handlerException($e);
            exit();
        }
    }

    /**
     * Devuelve el resultado de la busqueda de la Preinscripcion
     * a travez del token.
     *
     * @param string $user
     * @return mixed
     * @throws AuthorizeExceptions
     */
    public final function validateUser($user)
    {
        // Cargamos la entidad y realizamos la busqueda
        // con el parametro.
        /** @var UserManager $entity */
        $entity = static::$entity;
        $user = $entity::buscar([$this->loginUsername() => $user])->first();

        // En caso de que no exista devolvemos una excepcion.
        if (!$user->id)
            throw new AuthorizeExceptions('El usuario no existe.');

        return $user;
    }

    /**
     * Verifica el password para el usuario
     * encontrado.
     *
     * @param $user
     * @param $pass
     * @return bool
     * @throws AuthorizeExceptions
     */
    public function validatePassword($user, $pass)
    {
        if ($user->password != $this->hash($pass))
            throw new AuthorizeExceptions('Password incorrecta.');

        return true;
    }

    /**
     * Convierte un string en un hash.
     *
     * @param string $string
     * @return string
     */
    protected function hash($string)
    {
        return md5($string);
    }

    /**
     * Retorna el path a donde debe ser redireccionado
     * el sitio luego del login.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath'))
            return $this->redirectPath;

        return $this->redirectTo;
    }

    /**
     * Maneja una vista para mostrar los errores
     * que se puedan dar.
     *
     * @param AuthorizeExceptions|false $e
     */
    protected final function handlerException(AuthorizeExceptions $e = null)
    {
        $this->view(
            $this->pathLoginView(),
            ['error' => $e ? $e->getMessage() : false]
        );
    }

    /**
     * Devuelve el nombre del campo login utilizado
     * para el controlador.
     *
     * @return string
     */
    public function loginUsername()
    {
        return property_exists($this, 'username') ? $this->username : 'email';
    }

    /**
     * Cierra cualquier sesion abierta y nos
     * envia a la vista de login o cualquier
     * path configurado.
     */
    public function logout()
    {
        Session::close();

        $this->redirect($this->pathAfterLogin());
    }

    /**
     * Devuelve el path configurado para
     * redireccionar despues de realizar
     * el logout.
     *
     * @return string
     */
    public function pathAfterLogin()
    {
        return property_exists($this, 'redirectAfterLogout') ?
            $this->redirectAfterLogout : '/';
    }
}
