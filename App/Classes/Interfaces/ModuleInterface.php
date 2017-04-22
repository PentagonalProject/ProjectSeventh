<?php
namespace PentagonalProject\ProjectSeventh\Interfaces;

/**
 * Interface ModuleInterface
 * @package PentagonalProject\ProjectSeventh\Interfaces
 */
interface ModuleInterface
{
    /**
     * Get Module Name
     *
     * @return string
     */
    public function getModuleName() : string;

    /**
     * Get Module Author
     *
     * @return string
     */
    public function getModuleAuthor() : string;

    /**
     * Get Module Version commonly string|integer|float
     *
     * @return string
     */
    public function getModuleVersion() : string;

    /**
     * Initialize Module
     *
     * @return mixed
     */
    public function init();
}
