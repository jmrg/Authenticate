<?php

namespace Authenticate\Access;

trait Request
{
    /**
     * Return the request type that
     * has received server.
     *
     * @return string
     */
    private function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Return the global variable correspondent
     * the method HTTP.
     *
     * @return array
     */
    private function getParamsRequest()
    {
        // Obtenemos el request a travez de la variable GLOBALS
        // junto con el verbo de comunicación.
        return $GLOBALS['_'.$this->getRequestMethod()];
    }

    /**
     * It verify method HTTP is GET.
     *
     * @return bool
     */
    private function isGet()
    {
        return in_array($this->getRequestMethod(), ['GET', 'HEAD']);
    }

    /**
     * It verify method HTTP is POST.
     *
     * @return bool
     */
    private function isPost()
    {
        return in_array($this->getRequestMethod(), ['POST']);
    }

    /**
     * Return last URL executed.
     *
     * @return string
     */
    protected function getLastUrl()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * Return the protocol HTTP implement at server.
     *
     * @return string
     */
    protected function getProtocol()
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
    }

    /**
     * Return the site domain.
     *
     * @return string
     */
    protected function getDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Return the path relative to project.
     *
     * @return string
     */
    protected function getPathProject()
    {
        return dirname($_SERVER['SCRIPT_NAME']);
    }

    /**
     * Return the URL this site.
     *
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->getProtocol().$this->getDomain().$this->getPathProject();
    }

    /**
     * Redirecting to a URI in this site.
     *
     * @param string $to
     */
    public function redirect($to = '/')
    {
        header("Location: {$this->getBaseUrl()}{$to}");
        die();
    }
}
