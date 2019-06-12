<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */


namespace DooFinderBundle\Adapter;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;

/**
 * Class AbstractDooFinderSearchableItem
 * @package DooFinderBundle\Adapter
 */
abstract class AbstractDooFinderProduct extends AbstractProduct implements IDooFinderSearchableItem
{
    const DOO_FINDER_ID = "id";
    const DOO_FINDER_MANUAL_BOOST = "df_manual_boost";
    const DOO_FINDER_INDEXED_TEXT = "df_indexed_text";
    const DOO_FINDER_SITE_URL = "url";
    const DOO_FINDER_ITEM_LINK = "link";

    use DooFinderSearchableItemTrait;
}