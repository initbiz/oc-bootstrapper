<?php

namespace OFFLINE\Bootstrapper\October\Config;


use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * Class Yaml
 * @package OFFLINE\Bootstrapper\October\Config
 */
class Yaml implements Config
{
    /**
     * @var mixed
     */
    protected $config;

    /**
     * Yaml constructor.
     *
     * @param             $file
     * @param Parser|null $parser
     */
    public function __construct($file, Parser $parser = null)
    {
        if ($parser === null) {
            $parser = new Parser();
        }

        try {
            $this->config = $parser->parse(file_get_contents($file));
        } catch (ParseException $e) {
            throw new \RuntimeException('Unable to parse the YAML string: %s', $e->getMessage());
        }
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name) {
        return $this->config[$name];
    }
}