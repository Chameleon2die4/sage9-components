<?php /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

namespace Chameleon2die4\Components;

use Roots\Sage\Container;
use Roots\Sage\Template\Blade;

class Loader
{
    private static $namespace = 'Controllers\Components';

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
          (has_filter('sober/components/namespace')
            ? apply_filters('sober/components/namespace', rtrim(self::$namespace))
            : self::$namespace);
    }

    public function getController(string $template)
    {
        $exp = preg_split('/[.\/]/', $template);
        $name = array_pop($exp);
        $namespace = self::$namespace;

        $cname = Utils::convertToPascalCase($name);
        $controller = "{$namespace}\\{$cname}";
        if (class_exists($controller)) {
            return $controller;
        } else {
            return null;
        }
    }

    /**
     * It creates a controller, runs the lifecycle methods, and returns the data
     *
     * @param string class The controller class name
     * @param array data The data that will be passed to the controller.
     *
     * @return array The data that is being returned is the data that is being passed into the controller.
     */
    public function initController(string $class, array $data = [])
    {
        // Use the Sage DI container
        $container = self::sage();

        // Create the class on the DI container
        $controller = $container->make($class);

        // Set the params required for template param
        $controller->__setParams();

        // Lifecycle before
        $controller->__before();

        // Data
        $controller->__setData($data);

        // Lifecycle after
        $controller->__after();

        // Return
        $data = $controller->__getData();

        // Determine template location to expose data
//        $location = "sage/template/{$controller->__getTemplateParam()}-data/data";

        // Pass data to filter
//        add_filter($location, function ($data) use ($container, $class) {
//            // Recreate the class so that $post is included
//            $controller = $container->make($class);
//
//            // Params
//            $controller->__setParams();
//
//            // Lifecycle
//            $controller->__before();
//
//            // Data
//            $controller->__setData($data);
//
//            // Lifecycle
//            $controller->__after();
//
//            // Return
//            return $controller->__getData();
//        }, 10, 2);


        $templateData = collect(get_body_class())->reduce(function ($data, $class) {
            return apply_filters("sage/template/{$class}/data", $data);
        }, []);

        return array_merge($templateData, $data);
    }


    /**
     * Add a body class to the page based on the template name
     *
     * @param string template The name of the template file.
     *
     * @return void The body class is being returned.
     *
     * @noinspection PhpUnused
     */
    public function addBodyClass( string $template ) {
        $location = "sage/template/{$template}-data/data";

        add_filter('body_class', function ($body) use ($location) {
            $body[] = $location;

            return $body;
        }, 12);
    }

    /**
     * @return Blade;
     */
    public static function blade()
    {
        return self::sage('blade');
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
