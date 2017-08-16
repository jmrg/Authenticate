<?php

namespace Authenticate;

use Authenticate\Contracts\UserManager;

/**
 * Class Session
 * @package Authenticate\Surveys\Session
 */
abstract class Session
{
    /**
     * Mantiene el identificador del usuario
     * en la cookie de sesion.
     *
     * @var null
     */
    protected static $user = null;

    /**
     * Entidad/Modelo sobre el cual se realice
     * la busqueda del usuario autenticado.
     *
     * @var null
     */
    protected static $entity = null;

    /**
     * Configura los parametros necesarios para
     * poder arrancar una sesion.
     */
    public static function setSession()
    {
        // Capturamos de la constante el tiempo de vida que tendra la sesion
        // En caso de que no se haya configurado la constante necesitada
        // se define por defecto que el tiempo de espera sea de 30 min.
        $sessionTimeOut = defined('SessionTimeout') ? constant('SessionTimeout') : 1800;

        // Configuramos el tiempo que el servidor debe mantener los datos.
        ini_set('session.gc_maxlifetime', $sessionTimeOut);

        // Tiempo de vida de la cookie en el navegador.
        ini_set('session.cookie_lifetime', $sessionTimeOut);

        // Configuramos el tiempo de vida del id de sesion.
        session_set_cookie_params($sessionTimeOut);
    }

    /**
     * Inicia la sesion.
     */
    public static function start()
    {
        session_start();
    }

    /**
     * Registra el identificador del usuario
     * en la sesion inciada.
     *
     * @param mixed $user
     */
    public final static function registerUser($user)
    {
        $_SESSION['uid'] = $user->id;
    }

    /**
     * Devuelve el identificador del usuario
     * guardado.
     *
     * @return mixed
     */
    protected static function uid()
    {
        return $_SESSION['uid'];
    }

    /**
     * Carga una entidad de usuario.
     *
     * @return mixed
     */
    protected function loadUser()
    {
        /** @var UserManager $entity */
        $entity = static::$entity;

        return static::$user = $entity::find(static::uid());
    }

    /**
     * Setea la entidad en la cual se haran las
     * busquedas para usuarios.
     *
     * @param UserManager $manager
     */
    public static function setEntity(UserManager $manager = null)
    {
        static::$entity = $manager;
    }

    /**
     * Devuelve el usuario registrado para la sesion.
     *
     * @return mixed
     */
    public static function user()
    {
        is_null(static::$user) && static::loadUser();

        return static::$user;
    }

    /**
     * Verifica que exista un usuario autenticado.
     *
     * @return bool
     */
    public static function auth()
    {
        // Traemos el registrado.
        $user = static::user();

        // Verificamos que realmente exista algo.
        return !empty($user->id);
    }

    /**
     * Finaliza la sesion y la cookie correspondiente.
     */
    public static function close()
    {
        // Si existe una cookie la borramos.
        if (ini_get("session.use_cookies")) {
            // Capturamos los parametros de la cookie.
            $params = session_get_cookie_params();

            // Borramos todos los contenidos de la cookie.
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Y finalmente destruimos la sesion.
        session_destroy();
    }

    /**
     * Cierra cualquier sesion abierta
     * para iniciar una nueva.
     */
    public static function purge()
    {
        // Cerramos cualquier sesion abierta.
        static::close();

        // Configuramos e iniciamos nuevamente.
        static::setSession();
        static::start();
    }
}