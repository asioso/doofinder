# DooFinder Bundle

## Prerequisites
* PHP 7.1 or higher (https://secure.php.net/)
* Composer (https://getcomposer.org/download/)
* A Pimcore  Installation 
* A [doofinder account](https://app.doofinder.com/en/signup?fp_ref=asioso)



## Installation

### composer
in your composer.json file add the following repo under your

```json
"repositories": [
    {
      "type": "vcs",
      "url":  "git@github.com:asioso/doofinder.git"
    }
  ],
``` 



after that make sure you have access to the repo and added your ssh key to your bitbucket account.
test if composer can find the package.

```
composer search asioso

>>>>>
asioso/doofinder A bundle to help with dooFinder  

```

add the bundle to composer.json with
```
composer require asioso/pimcore-doofinder:dev-master

```
### Extension manager
just enable and install the Bundle in the pimcore extension manager

<!-- ![ext_manager_screenshot][extension_manager] -->

<!-- It might be possible that you have to add the minimal configuration in any project related config.yml file first, otherwise the Kernel might not boot. --> 



### What's next?

After defining your configuration, to generate your datafeed you will need to define a pimcore/symfony command to do the heavy lifting for you.

If you are familiar with Pimcore you will know that you have multiple possibilities to query for objects
 * use Listings via the API
 * write your own SQL query
 * using an [Index](https://pimcore.com/docs/5.x/Development_Documentation/E-Commerce_Framework/Index_Service/index.html) 
 

You can find a implementation of such a command [here](https://github.com/asioso/pimcore-doofinder-module/src/master/examples/Feed/BuildDooFinderDataFeedCommand.php), which uses both default  object listings and *AdvancedMysql* Index Service and runs with efficient ressource management in mind.
 

## More Details

[Configuration](/documentation/configuration.md)

[Feeding DooFinder](/documentation/feeds.md)

[Internals](/documentation/internals.md)

[Examples](/documentation/examples.md)




## TODO
* explain *active* and *objectPathRegex* configuration


<!--image definitions-->
[extension_manager]: https://github.com/asioso/doofinder/raw/master/documentation/images/extension_manager.png "Extension Manager"
