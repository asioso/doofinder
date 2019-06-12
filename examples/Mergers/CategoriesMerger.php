<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace AppBundle\DooFinder;


use AppBundle\Model\ShopCategory;
use DooFinderBundle\Merger\IDooFinderItemMerger;

/**
 * Class CategoriesMerger
 * @package AppBundle\DooFinder
 */
class CategoriesMerger implements IDooFinderItemMerger
{

    protected $_badChars = array('"', "\r\n", "\n", "\r", "\t", "|");
    protected $_repChars = array("", " ", " ", " ", " ", "");

    public function merge($objects, $options = array())
    {
        if ($objects == null) {
            return "";
        }

        $data = array();

        foreach ($objects as $object) {
            if (!$object instanceof ShopCategory) {
                continue;
            }
            /**
             * @var $object ShopCategory
             */
            if ($object->isShopCategory()) {
                $catLine = array();
                $list = $object->getParentCategoryList($object);
                foreach ($list as $cat) {
                    $line = $cat->getName($options['locale']);
                    $line = str_replace($this->_badChars, $this->_repChars, $line);
                    $catLine[] = $line;

                }
                $data[] = trim(implode(" > ", $catLine));
            }
        }
        return trim(implode(" %% ", $data));
    }
}