<?php

namespace Authenticate\Contracts;

interface Views
{
    /**
     * Load a view or template through path and send the data.
     *
     * @param null  $view
     * @param array $data
     * @param bool  $capture
     */
    public function view($view = null, array $data = [], $capture = false);
}
