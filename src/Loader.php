<?php

namespace Chameleon2die4\Components;

use Roots\Sage\Container;
use Roots\Sage\Template\Blade;

class Loader
{
    // Dep
//    private $hierarchy;

    // User
    private static $namespace = 'App\Controllers\Components';
//    private $path;

    // Internal
//    private $listOfFiles;
//    private $classesToRun = [];

    public function __construct()
    {
        // Set the default or custom namespace used for Controller files
        $this->setNamespace();
    }

    /**
     * Set Namespace
     *
     * Set the namespace from the filter or use the default
     */
    protected function setNamespace()
    {
        self::$namespace =
          (has_filter('sober/controller/components/namespace')
            ? apply_filters('sober/controller/components/namespace', rtrim(self::$namespace))
            : self::$namespace);
    }

//    /**
//     * Set Path
//     *
//     * Set the path assuming PSR4 autoloading from $this->namespace
//     */
//    protected function setPath()
//    {
//        $reflection = new \ReflectionClass(self::$namespace .'\App');
//        $this->path = dirname($reflection->getFileName());
//    }


//
//    /**
//     * Set File List
//     *
//     * Recursively get file list and place into array
//     */
//    protected function setListOfFiles()
//    {
//        $this->listOfFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path));
//    }

    public function getController(string $template)
    {
        $exp = explode('.', $template);
        $name = array_pop($exp);
        $namespace = self::$namespace;

        $cname = Utils::convertToPascalCase($name);
        if (class_exists("{$namespace}\{$cname}")) {
            return $cname;
        } else {
            return null;
        }
    }

    public function initController(string $class)
    {
        // Use the Sage DI container
        $container = self::sage();

        // Create the class on the DI container
        $controller = $container->make($class);

        // Set the params required for template param
        $controller->__setParams();

        // Determine template location to expose data
        $location = "sage/template/{$controller->__getTemplateParam()}-data/data";

        // Pass data to filter
        add_filter($location, function ($data) use ($container, $class) {
            // Recreate the class so that $post is included
            $controller = $container->make($class);

            // Params
            $controller->__setParams();

            // Lifecycle
            $controller->__before();

            // Data
            $controller->__setData($data);

            // Lifecycle
            $controller->__after();

            // Return
            return $controller->__getData();
        }, 10, 2);
    }


    /**
     * @return Blade;
     */
    public static function blade()
    {
        $container = Container::getInstance();

        return $container->makeWith('blade');
    }

    /**
     * @param string $file
     * @param array $data
     * @return string
     */
    public static function template(string $file, array $data = [])
    {
        return self::blade()->render($file, $data);
    }

    /**
     * Get the sage container.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @param Container|null $container
     * @return Container|mixed
     */
    public static function sage(string $abstract = null, array $parameters = [], Container $container = null)
    {
        $container = $container ?: Container::getInstance();
        if (!$abstract) {
            return $container;
        }
        return $container->bound($abstract)
          ? $container->makeWith($abstract, $parameters)
          : $container->makeWith("sage.{$abstract}", $parameters);
    }

}