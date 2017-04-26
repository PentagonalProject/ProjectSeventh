<?php
namespace {

    use PentagonalProject\ProjectSeventh\Module;

    /**
     * Class ExampleModule
     */
    class ExampleModule extends Module
    {
        /**
         * Module Name
         *
         * @var string
         */
        protected $embedded_name    = 'My Example Module';

        /**
         * Module Version
         *
         * @var string
         */
        protected $embedded_version = '1.0';

        /**
         * Module Uri
         *
         * @var string
         */
        protected $embedded_uri     = 'https://www.pentagonal.org/';

        /**
         * Module Author
         *
         * @var string
         */
        protected $embedded_author  = 'Pentagonal';

        /**
         * Module Author Uri
         *
         * @var string
         */
        protected $embedded_author_uri = 'https://www.pentagonal.org/';

        /**
         * Module Description
         *
         * @var string
         */
        protected $embedded_description = 'This Module Description';

        /**
         * Doing init when module Loaded
         * just like a construct
         */
        public function init()
        {
            // TODO: Implement init() method.
        }
    }
}
