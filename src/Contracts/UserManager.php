<?php

namespace Authenticate\Contracts;

interface UserManager
{
    /**
     * Realiza una busqueda por medio de los
     * argumentos de entrada.
     *
     * @param array $campos
     * @param array $_arguments
     * @return mixed
     */
    public static function buscar($campos = [], $_arguments = []);

    /**
     * Realiza una busqueda para devolver una instancia
     * de la clase que hereda o una coleccion
     * si recibe un array.
     *
     * @param null $arguments
     * @return mixed
     */
    public static function find($arguments = null);

}