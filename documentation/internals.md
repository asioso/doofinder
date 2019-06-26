# Internals

## What is doofinder

If you are not familar with doofinder it's best to take a look at their [website](https://www.doofinder.com/en/?fp_ref=asioso). BUT basically they provide a search engine for your content/products etc., which is accessible either by API or customizable search layer to you and your clients.

All you have to do is to make your content available to doofinder and include their search snippet.


## Bundle Architecture
This Pimcore Bundle should help you with bringing your content to doofinder's search engines. There are two approaches to connect do doofinder:



### API
You could say this is still under development, since we turned our focus on the feed strategy because of the sheer number of objects we had to work with for the moment.

The original idea was to hook into the publishing process of pimcore DataObjets and push the updated data to doofinder then ( the eventListener is still there, but deactived ;) ), but we ran into api quota restrictions and timeouts, beside the fact that we needed a mechanism to do batch processeing anyway, which is now realized by the data feed.



### Data Feed

The datafeed concept is that you supply doofinder with a number of urls where a crawler can find your feeds representing your content.

![ext_manager_screenshot][feed]         


If you are new to this topics, lease read more about this in the [doofinder documentation](https://www.doofinder.com/support/the-data-feed/the-product-data-feed?fp_ref=asioso)
in short doofinder expects for example a *.txt* file with a header line, separated by a pipe ('**|**') character. other formats like xml would also be possible, but we used txt because it would get less bloated in file size in comparison to xml. 
```
title|link|description|id|price|image link|product type
```

entries then will look like the following way:

```
LG Flatron M2262D 22" Full HD LCD|http://www.example.com/electronics/tv/LGM2262D.html|Attractively styled and boasting stunning picture quality|TV_123456|159 USD|http://images.example.com/TV_123456.png|Consumer Electronics > TVs > Flat Panel TVs

```

We use the bundles configuration (see here: [Configuration](https://github.com/asioso/doofinder/blob/master/documentation/configuration.md)) to determine what should be in the file.

so basically all you have to do is to call this (this is taken from [our example command](https://github.com/asioso/doofinder/src/master/examples/Feed/BuildDooFinderDataFeedCommand.php ):
```php
    
    /**
     * @var DooFinderBundle\DependencyInjection\DooFinder\IDooFinderServiceHandler
     */
    $doofinder = $this->dooFinder;
    
    /*
     * returns an array with <engineHashId> => [ data ] 
     * e.g.:   [
     *          'hash1'=>['data' => ['id'=> 1, 'name'=> 'en_name'], 'engine'=> 'hash1',  .... ],
     *          'hash2'=>['data' => ['id'=> 1, 'name'=> 'de_name'], 'engine'=> 'hash2',  .... ]
     *         ]
     */
    $feed = $dooFinder->getValuesForEngine($object);
    
    foreach ($feed as $engineKey => $feedContent) {
    
        //append the data line to each engine's type-feed file, $header is only required if the file needs to be created first
        $doofinderFile->writeToFile($feedContent['engine'], $feedContent['type'], $feedContent['data'], $this->header);
    }    

```
 



### Search Layer

When doofinder is fed with content you can include a configured search layer (configuration takes place in the search engine's configuration).
You can use a controller action we wrote to render all required javascript. example below: 

```
    <!-- PHP --->
    <?php if($this->getLocale() == "en"){
        $params = array(
            "hashID"=> "<en_engine_hash>",
            "locale"=> "en",
            "selector" => "#s",
            "zone" => "eu1",
        );
    }else{
        $params = array(
            "hashID"=> "<de_engine_hash>",
            "locale"=> "de",
            "selector" => "#s",
            "zone" => "eu1",
        );
    }
    ?>
    <?php echo $this->action("doofinderLayer", "DooFinder", "DooFinder", $params ); ?>

```
```
{#TWIG#}
{% if app.request.locale == 'en'%}
    {{ render(controller('DooFinderBundle:DooFinder:doofinderLayer',{"hashID": "<en_engine_hash>","locale": "en", "selector" : "#s","zone" : "eu1",})) }}
{% else %}
    {{ render(controller('DooFinderBundle:DooFinder:doofinderLayer',{"hashID": "<de_engine_hash>","locale": "de", "selector" : "#s","zone" : "eu1",})) }}
{% endif%}    


```

this is the demo layer for example: 

![demo_screenshot][demo]
            



##Concepts **[important]**


Read more here:

* [Merger](mergers.md)
* [URLProvider](urlProvider.md)




<!--image definitions-->
[demo]: https://github.com/asioso/doofinder/raw/master/documentation/images/demo.png "Stats"
[feed]: https://github.com/asioso/doofinder/raw/master/documentation/images/feed.png "Extension Manager"
