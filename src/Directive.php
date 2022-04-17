<?php /** @noinspection PhpUnused */

namespace Chameleon2die4\Components;

class Directive
{

    public function __construct()
    {
        $class = __CLASS__;

        Loader::sage('blade')->compiler()->directive('includePart', function ($expression) use ($class) {
            echo "<?php ${$class}::includePart({$expression}) ?>";
        });
    }


    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public static function includePart(string $template, array $data)
    {
        $loader = new Loader();
        $controller = $loader->getController($template);
        if ($controller) {
            $loader->initController($controller);

            return Loader::template($template, $data);
        } else {
            return Loader::template($template, $data);
        }
    }

}