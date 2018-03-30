<?php declare(strict_types = 1);

namespace App\Repository;

class ConfigurationRepository
{
    /**
     * @var array $config
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $id
     * @param mixed|null $default
     *
     * @return array|mixed|null
     */
    public function get(string $id, $default = null)
    {
        return $this->config[$id] ?? $default;
    }
}
