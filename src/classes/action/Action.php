<?php

namespace iutnc\NetVOD\action;

abstract class Action
{
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    public function __construct()
    {

        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    abstract public function execute(): string;

    public function getHttpMethod(): ?string
    {
        return $this->http_method;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function getScriptName(): ?string
    {
        return $this->script_name;
    }
}