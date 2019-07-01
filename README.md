# DooFinder Bundle

## Prerequisites
* PHP 7.1 or higher (https://secure.php.net/) 
* Composer (https://getcomposer.org/download/)
* A Pimcore  Installation (Symfony 3.4)
* A [doofinder account](https://app.doofinder.com/en/signup?fp_ref=asioso)


![stats_screenshot][stats]



## Installation

### composer

just run:

```bash

composer require asioso/pimcore-doofinder:dev-master

``` 

### Extension manager

just enable and install the Bundle in the pimcore extension manager

<!-- ![ext_manager_screenshot][extension_manager] -->

<!-- It might be possible that you have to add the minimal configuration in any project related config.yml file first, otherwise the Kernel might not boot. --> 



## Configuration

what's absolutely necessary to be added to your config.yml file:

````
doo_finder:
    search_api_key: "<search-api-key>"
    management_api_key: "<management-api-key>"

````

### Define your Engine Configurations

Take a look [here](https://github.com/asioso/doofinder/blob/master/documentation/internals.md) for a deeper insights on the internal workings of this bundle, but in short:
doofinder let's you define multiple search engines for your account. Each of these engines might have several **types** (so to say indices) for content. Each **type**'s content might greatly differ in structure and content, however they all must share the same *language*-property (also *currency*!) from their parent engine. 

#### So why does this matter?

For example you want to put your webshop's products from pimcore to doofinder. Let's say you have active localization for German (DE) und English (EN) and Prices in Euro(€) and Pound(£). That would mean you would still have to create two search engines in doofinder, one for each language respectively.

So you will have add the following configurations (simplified)

```
 - de_engine (DE , €)
      + de - products
      
 - en_engine (EN , £)
      + en -products
```

Given the case you would like to offer both currencies in both languages, you will need four engines!
 
```
 - de_eur_engine (DE , €)
      + de-eur - products
      
 - en_eur_engine (EN , €)
      + en-eur -products
      
 - de_pound_engine (DE , £)
      + de_pound - products
      
 - en__pound engine (EN , £)
      + en_pound -products      
```

Keep this in mind, but apparently that's how doofinder is designed. 

**Note** before you continue reading, I'm aware that there is some potential in mixing up terminology, because of the following:

* in doofinder:
    * an engine is just a *search engine* with any number of types (indices) attached.
* in this bundle:
    * an engine refers to exactly one engine and one type.
   

This is due to design and let's us determine an engine and type specific representation per Object.  This is important, you can read more on this in the [internals](https://github.com/asioso/doofinder/blob/master/documentation/internals.md). 

#### Some Example Configuration

Below you can see a configuration we have been using for a project. We are using 3 **types** for one search engine. So each bundle-engine here uses the same <hashId> but references different *types*.

*  *products* with type: **test_products**
*  *products_special* with type: **test_products_special**
*  *content* with type: **test_content**


way more interesting are the engine's item definitions. *class* defines the object's classname it's "listening" to, the *field* set defines the feed representation.

*  **dfAttribute**: is the attribute's name obviously (this will end up in feeds header line)
*  **classAttribute**: either:
    * 'self' with combination of a merger 
    * the attributes Name. e.g 'description' - we are using PathProperty and Reflection to retrieve the value
*  **merger**: originally designed to merge arrays, but can be used to do other things as well. just use it as a callback where you can control the output. see [here](https://github.com/asioso/doofinder/blob/master/documentation/mergers.md) for more details
*  **locale**: very useful if the classAttribute is a localized field
*  **getter**: if classAttribute itself is another object, then *getter* will be executed.     


This settings and behavior came up during development, so there is still potential to do this better and more user friendly. Configuration can be quite redundant right now for example.


````
doo_finder:
        search_api_key: "<search-api-key>"
        management_api_key: "<management-api-key>"
    search_engines:
       engine:
            name: "products"
            type: "test_product"
            site_url: "https://example.de/"
            language: "German"
            currency: "Euro"
            hashId: "<hashId>"
            baseURL: "https://example.de"
            user: ~
            item:
                class: "AppBundle\\Model\\DefaultProduct"
                listing: "Pimcore\\Model\\DataObject\\Product\\Listing"
                fields:
                    -  {dfAttribute: "title", classAttribute: "self", merger: [ { class: "AppBundle\\doofinder\\TitleMerger", field: "", options: [{ locale: "de" } ] } ] }
                    -  {dfAttribute: "description", classAttribute: "description", merger: [ { class: "AppBundle\\doofinder\\DescriptionMerger", field: "", options: [{ locale: "de" } ] } ] }
                    -  {dfAttribute: "image link", classAttribute: "self", locale: "de", merger: [ { class: "AppBundle\\doofinder\\FirstImageMerger", field: "", options: [{ locale: "de", baseUrl: "https://example.de" , thumbnail: 'productDetailThumb'}] } ] }
                    -  {dfAttribute: "product_type", classAttribute: "categories", locale: "de", merger: [ { class: "AppBundle\\doofinder\\CategoriesMerger", field: "", options: [{locale: "de"}] } ] }
                    -  {dfAttribute: "link",  url: [ { class: "AppBundle\\doofinder\\DefaultProductURLProvider", locale: "de", route: "shop-detail" } ] }
                    -  {dfAttribute: "attributes", classAttribute: "self", locale: "de", merger: [ { class: "AppBundle\\doofinder\\DefaultProductAttributeMerger", field: "", options: [{"locale":"de"} ]} ] }
                    -  {dfAttribute: "price",  classAttribute: "self", merger: [ { class: "AppBundle\\doofinder\\PriceMerger", field: "", options: [{ locale: "de" } ] } ] }
                    -  {dfAttribute: "uvp price",  classAttribute: "self", merger: [ { class: "AppBundle\\doofinder\\UvpPriceMerger", field: "", options: [{ locale: "de"} ] } ] }
                    -  {dfAttribute: "id", classAttribute: "id"}
                    -  {dfAttribute: "brand", classAttribute: "brand", locale: "de" , getter: "getName" }
                    -  {dfAttribute: "sku_field", classAttribute: "sku" }
                    -  {dfAttribute: "sku2", classAttribute: "skuTwo" }
                    -  {dfAttribute: "sku3", classAttribute: "skuThree",}
                    -  {dfAttribute: "manufacturer", classAttribute: "manufacturer", locale: "de", getter: "getName" }

       engine_product_special:
            name: "products_special"
            type: "test_product_special"
            site_url: "https://example.de/"
            language: "German"
            currency: "Euro"
            hashId: "<hashId>"
            baseURL: "https://example.de"
            user: ~
            item:
                class: "AppBundle\\Model\\DefaultProductSpecial"
                listing: "Pimcore\\Model\\DataObject\\Product\\Listing"
                fields:
                    -  {dfAttribute: "title", classAttribute: "name", locale: "de"  }
                    -  {dfAttribute: "description", classAttribute: "description", merger: [ { class: "AppBundle\\doofinder\\DescriptionMerger", field: "", options: [{ locale: "de" } ] } ] }
                    -  {dfAttribute: "image_link", classAttribute: "self", locale: "de", merger: [ { class: "AppBundle\\doofinder\\SpecialImageMerger", field: "", options: [{ baseUrl: "https://example.de" , thumbnail: 'specialProductThumbnail'}] } ] }
                    -  {dfAttribute: "product type", classAttribute: "categories", locale: "de", merger: [ { class: "AppBundle\\doofinder\\CategoriesMerger", field: "", options: [{locale: "de"}] } ] }
                    -  {dfAttribute: "link",  url: [ { class: "AppBundle\\doofinder\\ProductSpecialURLProvider", locale: "de", route: "shop-detail" , prefix: "https://example.de" } ] }
                    -  {dfAttribute: "id", classAttribute: "id"}

       engine_content:
                name: "content"
                type: "test_content"
                site_url: "https://example.de/"
                language: "German"
                currency: "Euro"
                hashId: "<hashId>"
                baseURL: "https://example.de"
                user: ~
                item:
                    class: "\\Pimcore\\Model\\Document"
                    listing: "\\Pimcore\\Model\\Document\\Listing"
                    listing_arguments: [unpublished: "false", condition: "`parentId` = 2"]
                    fields:
                        -  {dfAttribute: "id", classAttribute: "id"  }
                        -  {dfAttribute: "title", classAttribute: "title", locale: "de"  }
                        -  {dfAttribute: "description", classAttribute: "description", locale: "de"  }
                        -  {dfAttribute: "metadata", classAttribute: "metadata", locale: "de",merger: [ { class: "AppBundle\\doofinder\\MetadataMerger"} ] }
                        -  {dfAttribute: "link",  url: [ { class: "AppBundle\\doofinder\\DocumentURLProvider", prefix: "https://example.de" } ] }
                        #-  {dfAttribute: "image_link", classAttribute: "self", locale: "de", merger: [ { class: "AppBundle\\doofinder\\ContentImageMerger", field: "", options: [{  baseUrl: "https://example.de"  }] } ] }
                        #-  {dfAttribute: "content", classAttribute: "self", merger: [ { class: "AppBundle\\doofinder\\PageContentMerger" } ] }


````



### What's next?

After defining your configuration, to generate your datafeed you will need to define a pimcore/symfony command to do the heavy lifting for you.

If you are familiar with Pimcore you will know that you have multiple possibilities to query for objects
 * use Listings via the API
 * write your own SQL query
 * using an [Index](https://pimcore.com/docs/5.x/Development_Documentation/E-Commerce_Framework/Index_Service/index.html) 
 

You can find a implementation of such a command [here](https://github.com/asioso/pimcore-doofinder-module/src/master/examples/Feed/BuildDooFinderDataFeedCommand.php), which uses both default  object listings and *AdvancedMysql* Index Service and runs with efficient ressource management in mind.

 

## More Details

* [Feeding DooFinder](https://github.com/asioso/doofinder/blob/master/documentation/feeds.md)
* [Internals](https://github.com/asioso/doofinder/blob/master/documentation/internals.md)
* [Examples](https://github.com/asioso/doofinder/blob/master/documentation/examples.md)




## TODO

* explain *active* and *objectPathRegex* configuration


<!--image definitions-->
[stats]: https://github.com/asioso/doofinder/raw/master/documentation/images/doofinder_stats.png "Stats"
[extension_manager]: https://github.com/asioso/doofinder/raw/master/documentation/images/extension_manager.png "Extension Manager"

