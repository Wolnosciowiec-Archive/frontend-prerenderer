Frontend Prerenderer
====================

Uses PhantomJS to render a single-page application written in javascript.
Useful for SEO and indexing by social media.

```
/*
 * Wolnościowiec / Frontend Pre-renderer
 * -------------------------------------
 *
 *   Wolnościowiec is a project to integrate the movement
 *   of people who strive to build a society based on
 *   solidarity, freedom, equality with a respect for
 *   individual and cooperation of each other.
 *
 *   We support human rights, animal rights, feminism,
 *   anti-capitalism (taking over the production by workers),
 *   anti-racism, and internationalism. We negate
 *   the political fight and politicians at all.
 *
 *   http://wolnosciowiec.net/en
 *
 *   License: LGPLv3
 */
```

## Usage

The usage is simple, just redirect any request to this service, it should go through index.php
Use the webserver to redirect requests properly and validate which domains are allowed.

## Cache regeneration

The service keeps the history of successful requests from robots, so later there is a possibility to click
all of those links again using a script `reclick-cache`

To pass the clicks from `reclick-cache` you have to redirect it's useragent `curl/reclick-cache` through the 
prerenderer, so accept it as a crawler on the webserver configuration just like you do for search engines.

Schedule `./bin/reclick-cache.sh` to run eg. daily to regenerate the cache, so the crawlers could hit the cached version
instead of waiting long for the page to load.
