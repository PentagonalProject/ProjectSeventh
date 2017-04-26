<?php
/* ------------------------------------------------------ *\
 |             APPLICATION COMPONENT ROUTES              |
\* ------------------------------------------------------ */

namespace {

    use PentagonalProject\ProjectSeventh\Application;
    use PentagonalProject\ProjectSeventh\Arguments;
    use Slim\App;

    if (!isset($this) || ! $this instanceof Arguments) {
        return;
    }

    /** @var Application $c */
    $c =& $this['application'];
    if (!$c instanceof Application) {
        return;
    }

    /**
     * @var App $slim
     */
    // $slim =& $c->getSlim();
}
