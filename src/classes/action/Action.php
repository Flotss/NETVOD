<?php

namespace iutnc\NetVOD\action;

/**
 * Class Action
 * Cette classe permet de gérer les actions
 */
abstract class Action
{
    /**
     * @var string|mixed|null $http_method La méthode HTTP utilisée
     */
    protected ?string $http_method = null;

    /**
     * @var string|mixed|null $action Le nom de l'hote
     */
    protected ?string $hostname = null;

    /**
     * @var string|mixed|null $action Le nom du script
     */
    protected ?string $script_name = null;


    /**
     * Constructeur de la classe Action
     */
    public function __construct()
    {

        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    /**
     * Méthode execute
     * Qui permet d'exécuter l'action voulu
     * @return string le code HTML de la page
     */
    abstract public function execute(): string;

    /**
     * Méthode getHttpMethod
     * @return string|null la méthode HTTP utilisée
     */
    public function getHttpMethod(): ?string
    {
        return $this->http_method;
    }

    /**
     * Méthode getHostname
     * @return string|null Le nom de l'hote
     */
    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    /**
     * Méthode getScriptName
     * @return string|null Le nom du script
     */
    public function getScriptName(): ?string
    {
        return $this->script_name;
    }
}