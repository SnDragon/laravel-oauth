<?php

namespace App\Support;

use Illuminate\Support\Manager;

class ClientManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new \RuntimeException("没有可用的driver");
    }

    public function createGithubDriver()
    {
        return new GithubClient($this->app->config['oauth.github']);
    }
}