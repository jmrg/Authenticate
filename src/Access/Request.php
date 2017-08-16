<?php

namespace Authenticate\Access;

trait Request
{
    /**
     * Devuelve el tipo de peticion que se
     * ha recibo el servidor.
     *
     * @return string
     */
    private function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Devuelve el contenido de la variable
     * global correspondiente al metodo
     * request recibido.
     *
     * @return array
     */
    private function getParamsRequest()
    {
        // Obtenemos el request a travez de la variable GLOBALS
        // junto con el verbo de comunicación.
        return $GLOBALS['_' . $this->getRequestMethod()];
    }

    /**
     * Verifica que el metodo de comunicacion
     * sea GET.
     *
     * @return bool
     */
    private function isGet()
    {
        return in_array($this->getRequestMethod(), ['GET', 'HEAD']);
    }

    /**
     * Verifica que el metodo de comunicacion
     * sea POST.
     *
     * @return bool
     */
    private function isPost()
    {
        return in_array($this->getRequestMethod(), ['POST']);
    }

    /**
     * Devuelve la url de la ultima peticion
     * realizada.
     *
     * @return string
     */
    protected function getLastUrl()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * Devuelve el protocolo utilizado para el servidor.
     *
     * @return string
     */
    protected function getProtocol()
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https://" : "http://";
    }

    /**
     * Devuelve el dominio del sitio.
     *
     * @return string
     */
    protected function getDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Devuelve el dominio del sitio.
     *
     * @return string
     */
    protected function getPathProject()
    {
        return dirname($_SERVER['SCRIPT_NAME']);
    }

    /**
     * Devuelve la url del sitio.
     *
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->getProtocol() . $this->getDomain() . $this->getPathProject();
    }

    /**
     * Redirecciona a una direccion dentro del sitio actual.
     *
     * @param string $to
     */
    public function redirect($to = '/')
    {
        header("Location: {$this->getBaseUrl()}{$to}");
        die();
    }
}