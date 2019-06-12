# Mergers

You might ask: "What the Hell?" But they offer more flexibility and additional processing callbacks. 
For example  take a look at the configuration below, where we want to get a product's type (s) following doofinder's documentation ([More than one category? Right on!](https://www.doofinder.com/support/the-data-feed/the-product-data-feed#more-than-one-category-right-on)) 

## The Category Tree Example

```
...
-  {dfAttribute: "product_type", classAttribute: "categories", locale: "de", merger: [ { class: "AppBundle\\doofinder\\CategoriesMerger", field: "", options: [{locale: "de"}] } ] }
...
```

To just quote doofinder here:

> You can specify **several category trees**  for an item. A sports shoe could belong to both the *Sports > Clothes > Shoes  and Snickers > Jogging*, for instance.  You can do this with either a xml or txt feed:
> * For a XML feed: Just add more than one *<product_type>* tag.
> * For a plain text feed: Use the **%%** character to indicate another category tree. i.e.:  *Sports > Clothes > Shoes %% Snickers > Jogging*.


Our Product class does store categories as a m to n relation to a ShopCategory class. Addtionally we have to differentiate between shop categories and internal categories (e.g. discounts, shipping type, etc.) 

![categories_multiref][multiref]

So the resulting Merger class taking care of all that can be seen here, which generates exactly the category trees we wanted for *shop* categories and applies additionally removes unwanted characters.


````php
<?php

namespace AppBundle\DooFinder;


use AppBundle\Model\ShopCategory;
use DooFinderBundle\Merger\IDooFinderItemMerger;

class CategoriesMerger implements IDooFinderItemMerger
{

    protected $_badChars = array('"',"\r\n","\n","\r","\t", "|");
    protected $_repChars = array(""," "," "," "," ", "");

    public function merge( $objects, $options = array() )
    {
        if($objects == null){
            return "";
        }

        $data = array();

        foreach($objects as $object){
            if(!$object instanceof ShopCategory ){
                continue;
            }
            /**
             * @var $object ShopCategory
             */
            if($object->isShopCategory()){
                $catLine = array();
                $list = $object->getParentCategoryList($object);
                foreach($list as $cat){
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

````

## Generate *ironed* Text

A product's row in the datafeed must be a single line without, but what about attributes that hold multiline descriptions in html?
The merger below shows an example how to achieve that:  


```php
<?php

namespace AppBundle\DooFinder;


use AppBundle\Model\DefaultProduct;
use DooFinderBundle\Merger\IDooFinderItemMerger;

class DescriptionMerger implements IDooFinderItemMerger
{

    public function merge($object, $options = array())
    {
        if($object instanceof DefaultProduct)
        {
            $locale = $options[0]['locale'];
            $description = $object->getDescription($locale);
            
            //remove all tags
            $description = strip_tags($description);
            
            //remove line breaks
            $description = preg_replace('/\s+/S', " ", $description);           

            return $description;
        }
    }
}

```

to apply this merger, again just put it in the configuration:

```
...
-  {dfAttribute: "description", classAttribute: "description", merger: [ { class: "AppBundle\\doofinder\\DescriptionMerger", field: "", options: [{ locale: "de" } ] } ] }
...
```


<!--image definitions-->
[multiref]: https://github.com/asioso/doofinder/raw/master/documentation/images/categories_multiref.png "multiref"
