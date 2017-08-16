<?php

namespace Authenticate\Contracts;

interface Views
{
    /**
     * Carga una vista
     *
     * @param null $view
     * @param array $data
     * @param bool $capture
     */
    public function view($view = null, array $data = [], $capture = false);
}