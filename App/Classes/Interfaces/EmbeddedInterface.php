<?php
namespace PentagonalProject\ProjectSeventh\Interfaces;

/**
 * Interface EmbeddedInterface
 * @package PentagonalProject\ProjectSeventh\Interfaces
 */
interface EmbeddedInterface
{
    /**
     * Get Embedded Name
     *
     * @return string
     */
    public function getEmbeddedName() : string;

    /**
     * Get Embedded Author
     *
     * @return string
     */
    public function getEmbeddedAuthor() : string;

    /**
     * Get Embedded Version commonly string|integer|float
     *
     * @return string
     */
    public function getEmbeddedVersion() : string;

    /**
     * Initialize Embed
     *
     * @return mixed
     */
    public function init();
}
