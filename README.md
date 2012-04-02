# SOGEnomBundle
Symfony2 bundle for the [Enom](http://www.enom.com/resellers/api-reseller.aspx) API.

**License**

SOGEnomBundle is licensed under the MIT License - see the `Resources/meta/LICENSE` file for details

**Enom API Commands Supported**

1. `TBC`


## Setup
**Using Submodule**

    git submodule add https://github.com/shaneog/SOGEnomBundle.git vendor/bundles/SOG/EnomBundle
**Using the vendors script**

      [SOGEnomBundle]
          git=https://github.com/shaneog/SOGEnomBundle.git
          target=bundles/SOG/EnomBundle
**Add the SOG namespace to autoloader**

``` php
<?php
   // app/autoload.php
   $loader->registerNamespaces(array(
    // ...
    'SOG'               => __DIR__.'/../vendor/bundles',
  ));
```
**Add SOGEnomBundle to your application kernel**

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new SOG\EnomBundle\SOGEnomBundle(),
    );
}
```
**Yml configuration**

``` yml
# app/config/config.yml
sog_enom:
  url: #Enom Reseller URL
  username: #Enom Account login ID
  password: #Enom Account password
```
## Usage

**Using service**

``` php
<?php
        $enom = $this->get('Enom');
```