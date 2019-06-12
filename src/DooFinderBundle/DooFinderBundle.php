<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle;

use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\Routing\RouteReferenceInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * Class DooFinderBundle
 * @package DooFinderBundle
 */
class DooFinderBundle extends Bundle implements PimcoreBundleInterface
{


    use PackageVersionTrait;

    /**
     * Bundle name as shown in extension manager
     *
     * @return string
     */
    public function getNiceName()
    {
        return 'Asioso - Doofinder Bundle';
    }

    /**
     * Bundle description as shown in extension manager
     *
     * @return string
     */
    public function getDescription()
    {
        return "";
    }


    /**
     * If the bundle has an installation routine, an installer is responsible of handling installation related tasks
     *
     * @return InstallerInterface|null
     */
    public function getInstaller()
    {
        return null;
    }

    /**
     * Get path to include in admin iframe
     *
     * @return string|RouteReferenceInterface|null
     */
    public function getAdminIframePath()
    {
        return null;
    }

    /**
     * Get javascripts to include in admin interface
     *
     * Strings will be directly included, RouteReferenceInterface objects are used to generate an URL through the
     * router.
     *
     * @return string[]|RouteReferenceInterface[]
     */
    public function getJsPaths()
    {
        return [];
    }

    /**
     * Get stylesheets to include in admin interface
     *
     * Strings will be directly included, RouteReferenceInterface objects are used to generate an URL through the
     * router.
     *
     * @return string[]|RouteReferenceInterface[]
     */
    public function getCssPaths()
    {
        return [];
    }

    /**
     * Get javascripts to include in editmode
     *
     * Strings will be directly included, RouteReferenceInterface objects are used to generate an URL through the
     * router.
     *
     * @return string[]|RouteReferenceInterface[]
     */
    public function getEditmodeJsPaths()
    {
        return [];
    }

    /**
     * Get stylesheets to include in editmode
     *
     * Strings will be directly included, RouteReferenceInterface objects are used to generate an URL through the
     * router.
     *
     * @return string[]|RouteReferenceInterface[]
     */
    public function getEditmodeCssPaths()
    {
        return [];
    }

    /**
     * Returns the composer package name used to resolve the version
     *
     * @return string
     */
    protected function getComposerPackageName(): string
    {
        return 'asioso/pimcore-doofinder-module';
    }
}
