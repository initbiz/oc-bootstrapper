<?php

namespace OFFLINE\Bootstrapper\October\Util;

use October\Rain\Config\Rewrite;

/**
 * Class ConfigWriter
 * @package OFFLINE\Bootstrapper\October\Util
 */
class ConfigWriter
{
    /**
     * @var string
     */
    public $env = 'dev';
    /**
     * @var Rewrite
     */
    protected $writer;
    /**
     * @var string
     */
    protected $dir;

    /**
     * ConfigWriter constructor.
     *
     * @param Rewrite|null $writer
     */
    public function __construct(Rewrite $writer = null)
    {
        if ($writer === null) {
            $writer = new Rewrite();
        }
        $this->writer = $writer;
        $this->dir    = getcwd() . '/config';
    }

    /**
     * @param string $appEnv
     *
     * @return $this
     */
    public function setAppEnv($appEnv = 'dev')
    {
        file_put_contents(getcwd() . '/.env', 'APP_ENV=' . $appEnv);

        return $this;
    }

    /**
     * @param $files
     *
     * @return $this
     */
    public function copyConfigFileToEnv($files)
    {
        if ( ! is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $target = $this->filePath($file);
            if ( ! file_exists($target)) {
                copy($this->dir . '/' . $file . '.php', $target);
            }
        }

        return $this;
    }

    /**
     * @param       $file
     * @param array $values
     *
     * @return $this|ConfigWriter
     */
    public function write($file, array $values)
    {
        if ($file === 'app') {
            return $this->writeApp($values);
        }

        $file = $this->filePath($file);

        $this->writer->toFile($file, $values);

        return $this;
    }

    /**
     * Write to the app config file.
     *
     * Since the app config file contains user defined
     * functions like base_path() we cannot use the
     * usual config writer which eval's the code.
     *
     * @param array $values
     *
     * @return $this
     */
    private function writeApp(array $values)
    {
        $file = $this->filePath('app');

        $contents = file_get_contents($file);
        foreach ($values as $key => $value) {
            $contents = preg_replace("/('{$key}'\s=>\s'?)([^,']+)(')?/", "$1{$value}$3", $contents);
        }

        file_put_contents($file, $contents);

        return $this;
    }

    /**
     * @param $key
     */
    public function setAppKey($key)
    {
        $this->writer->toFile($this->dir . '/app.php', compact('key'), false);
    }

    /**
     * @param $file
     *
     * @return string
     */
    protected function filePath($file)
    {
        $envPath   = $this->env === 'prod' ? '' : $this->env . '/';
        $targetDir = $this->dir . '/' . $envPath;

        if ( ! is_dir($targetDir)) {
            mkdir($targetDir);
        }

        return $targetDir . $file . '.php';
    }
}