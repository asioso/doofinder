# nom nom nom


If you are familiar with Pimcore you will know that you have multiple possibilities to query for objects
 * use Listings via the API
 * write your own SQL query
 * using an [Index](https://pimcore.com/docs/5.x/Development_Documentation/E-Commerce_Framework/Index_Service/index.html) 
 

You can find a implementation of such a command [here](https://github.com/asioso/doofinder/src/master/examples/Feed/BuildDooFinderDataFeedCommand.php), which uses both default  object listings and *OptimizedMysql* Index Service and runs with efficient ressource management in mind.


run the command like this:
```bash
$bin/console doo:build --force --process --notify=<example@email.com> --notify=<other@email.com>

```
options:

* --force: disables interactive questions in the command flow
* --notify: sends out a email to the defined recipient, when the command is done. multiple recipients possible!
* --process: notifies doofinder to process all new datafeeds, using the [management api](https://www.doofinder.com/support/developer/api/management-api?fp_ref=asioso)
* --gzip: compress your feed files  


as a result there should be a */data* folder in your project's root directory with one file for every configured doofinder search engine and type combination prefixed with a timestamp.

> **naming convention:** {Ymd}_{Hi}\_feed\_{engine\_hash}\_{type}.txt

> e.g: 20181116\_101605\_feed_\<someHash\>\_test\_product.txt


There is a controller action dedicated to serve the latest datafeed file under the following route:
```
/asioso-doofinder-bundle/{hashId}/feed/{type}
```

this would check against the feed urls we have configured in doofinder, e.g.:

![ext_manager_screenshot][feed]







<!--image definitions-->
[feed]: https://github.com/asioso/doofinder/raw/master/documentation/images/feed.png "Extension Manager"

