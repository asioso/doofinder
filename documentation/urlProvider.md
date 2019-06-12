#URL Provider

You will need a way to build URLs for your products during datafeed generation.

below you can see an example, which mimics the way product URLs are generated within the pimcore e-commerce bundle default product implementation.

```php

<?php

namespace AppBundle\DooFinder;


use AppBundle\Model\DefaultProduct;
use DooFinderBundle\Merger\AbstractURLProvider;
use DooFinderBundle\Merger\IURLProvider;

class DefaultProductURLProvider extends AbstractURLProvider implements IURLProvider
{

    /**
     * @param $object
     * @param $locale
     * @param $route
     * @param null $prefix
     * @return string
     */
    public function getUrlForObject($object, $locale, $route, $prefix = null): string
    {
        /**
         * @var $object DefaultProduct
         *
         */

        // add id
        //if (!array_key_exists('product', $params)) {
            $params['product'] = $object->getId();
        //}

        //add prefix / by default language/shop
        if (!array_key_exists('prefix', $params)) {
            if ($params['document']) {
                $params['prefix'] = substr($params['document']->getFullPath(), 1);
            } else {
                $language = $locale;
                #$language = \Pimcore::getContainer()->get('request_stack')->getCurrentRequest()->getLocale();
                $params['prefix'] = substr($language, 0, 2) . '/shop';
            }
        }

        // add name
        if (!array_key_exists('name', $params)) {
            // add category path
            $category = $object->getFirstCategory();
            if ($category) {
                $path = $category->getNavigationPath($params['rootCategory'], $params['document']);
                $params['name'] = $path."/";
            }

            // add name
            $name = \Pimcore\File::getValidFilename($object->getOSName());
            $params['name'] .= preg_replace('#-{2,}#', '-', $name);
        }

        unset($params['rootCategory']);
        unset($params['document']);

        // create url
        $urlHelper = \Pimcore::getContainer()->get('pimcore.templating.view_helper.pimcore_url');        

        return $this->getBaseURL() . $urlHelper($params, $route);


    }

}
 
```


To use this Provider class again just put it in the configuration:

```
...
-  {dfAttribute: "link",  url: [ { class: "AppBundle\\doofinder\\DefaultProductURLProvider", locale: "de", route: "shop-detail" } ] }
...
```
