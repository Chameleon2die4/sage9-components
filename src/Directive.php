<?php /** @noinspection PhpUnused */

namespace Chameleon2die4\Components;

class Directive
{

    public function __construct()
    {
        $class = __CLASS__;

        Loader::sage('blade')->compiler()->directive('includePart', function ($expression) use ($class) {
            return "<?= {$class}::includePart({$expression}) ?>";
        });
    }


    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public static function includePart(string $template, array $data = [])
    {
        $loader = new Loader();
        $controller = $loader->getController($template);

        if ($controller) {
            $data = $loader->initController($controller, $data);
        }

        return Loader::template($template, $data);
    }

}