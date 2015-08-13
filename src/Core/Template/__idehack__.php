<?php

if (false) {
    /**
     * @method include() include(string $filename, array $vars) load template $filename and return rendered with $vars as string
     */
    class Blitz {
        public function __construct($file = null) { }

        public function load($template) { }

        public function set(array $vars) { }

        public function parse(array $vars = null) { return ''; }

        public function display(array $vars = null) { }

        public function block($path, array $vars = null, $iterate_nonexistent = false) { }

        public function fetch($path, array $vars = null) { return array(); }

        public function clean() { }

        public function getGlobals() { }

        public function setGlobals(array $global_vars) { }

        public function hasContext($path) { }

        public function context($path) { }

        public function iterate($path, $iterate_nonexistent = false) { }

        public function getContext() { }

        public function getStruct() { }

        public function dumpStruct() { }

        public function getIterations() { }
    }
}
