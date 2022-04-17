<?php

namespace Chameleon2die4\Components;

/**
 * Hooks
 */
if (function_exists('add_action')) {
    add_action('init', function() {
        new Directive();
    });
}