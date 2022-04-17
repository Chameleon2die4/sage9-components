# Sage9 Components

[//]: # ([![PHP Composer]&#40;https://github.com/Chameleon2die4/sage9-components/actions/workflows/php.yml/badge.svg&#41;]&#40;https://github.com/Chameleon2die4/WP-Router/actions/workflows/php.yml&#41;)
[![Latest Version](https://img.shields.io/github/v/tag/Chameleon2die4/sage9-components?sort=semver&label=version)](https://github.com/Chameleon2die4/WP-Router/)
[![Packagist](https://badgen.net/packagist/v/chameleon2die4/sage9-components/latest)](https://packagist.org/packages/chameleon2die4/wp-router/)
[![PHP Version Require](https://badgen.net/packagist/php/chameleon2die4/sage9-components/)](https://www.php.net/docs.php)
[![License](https://img.shields.io/badge/license-mit-blue.svg)](https://github.com/Chameleon2die4/WP-Router/blob/master/LICENSE.md)

Add Components controllers support to Sage 9.x.

## Installation

### Composer:

Install with [Composer](https://getcomposer.org/). Run in shell:

```shell
$ composer require chameleon2die4/sage9-components
```

### Requirements:

* [PHP](http://php.net/manual/en/install.php) >= 7.2
* [Sage](https://roots.io/sage/) ^9.x

## Setup

By default Controllers uses namespace `Controllers\Components`.

Controller takes advantage of [PSR-4 autoloading](https://www.php-fig.org/psr/psr-4/). To change the namespace, use the filter below within `functions.php`

```php
add_filter('sober/components/namespace', function () {
    return 'Data\Partials';
});
```

## Usage

### Overview:

* Controller class names follow the same hierarchy as WordPress.
* The Controller class name should match the filename
    * For example `App.php` should define class as `class App extends Controller`
* Create methods within the Controller Class;
    * Use `public function` to return data to the Blade views/s
        * The method name becomes the variable name in Blade
        * Camel case is converted to snake case. `public function ExampleForUser` in the Controller becomes `$example_for_user` in the Blade template
        * If the same method name is declared twice, the latest instance will override the previous
    * Use `public static function` to use run the method from your Blade template which returns data. This is useful for loops
        * The method name is not converted to snake case
        * You access the method using the class name, followed by the method. `public static function Example` in `App.php` can be run in Blade using `App::Example()`
        * If the same method name is declared twice, the latest instance will override the previous
    * Use `protected function` for internal methods. These will not be exposed to Blade. You can run them within `__construct`
        * Dependency injection with type hinting is available through `__construct`


The above may sound complicated on first read, so let's take a look at some examples to see how simple Controller is to use.

### Basic Controller;

The following example will expose `$images` to `resources/views/partials/slider.blade.php`

**app/Controllers/Components/Slider.php**

```php
<?php

namespace App\Controllers\Components;

use Chameleon2die4\Components\Component;

class Slider extends Component
{
    /**
     * Return images from Advanced Custom Fields
     *
     * @return array
     */
    public function images()
    {
        return get_field('images');
    }
}
```

**resources/views/partials/slider.blade.php**

```php
@if($images)
  <ul>
    @foreach($images as $image)
      <li><img src="{{$image['sizes']['thumbnail']}}" alt="{{$image['alt']}}"></li>
    @endforeach
  </ul>
@endif
```

### Parent:

In parent template use directive 
```php 
@includePart( string $template, array $data = [] ) 
```

**resources/views/single.blade.php**

```php
@extends('layouts.app')

@section('content')
  @while(have_posts()) @php the_post() @endphp
    @include('partials.page-header')
    
    @includePart('partials.slider')
  @endwhile
@endsection
```

### Lifecycles;

Controller Classes come with two lifecycle hooks for greater control.

```php
public function __before()
{
    // runs after this->data is set up, but before the class methods are run
}

public function __after()
{
    // runs after all the class methods have run
}
```


