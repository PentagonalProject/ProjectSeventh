<?php
namespace {

    use PentagonalProject\ProjectSeventh\Extension;

    /**
     * Class ExampleExtension
     */
    class ExampleExtension extends Extension
    {
        /**
         * Extension Name
         *
         * @var string
         */
        protected $embedded_name    = 'My Example Extension';

        /**
         * Extension Version
         *
         * @var string
         */
        protected $embedded_version = '1.0';

        /**
         * Extension Uri
         *
         * @var string
         */
        protected $embedded_uri     = 'https://www.pentagonal.org/';

        /**
         * Extension Author
         *
         * @var string
         */
        protected $embedded_author  = 'Pentagonal';

        /**
         * Extension Author Uri
         *
         * @var string
         */
        protected $embedded_author_uri = 'https://www.pentagonal.org/';

        /**
         * Extension Description
         *
         * @var string
         */
        protected $embedded_description = 'This Extension Description';

        /**
         * Doing init when extension Loaded
         * just like a construct
         */
        public function init()
        {
            // TODO: Implement init() method.
        }
    }
}