Frontend Prerenderer
====================

Uses PhantomJS/Chromium to render a single-page application written in javascript.
Useful for SEO and indexing by social media. Works on LOW-END-BOXES, does not consume much ram memory.

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

## Configuration

Create your `config.php` file in the root directory to override default settings.
The `config.dist.php` is a default configuration file and can be copied in place of `config.php`

#### Reference
   
- **skipped_headers**: List of headers to not forward (PhantomJS only)
- **allowed_domains**: Accept requests only for those domains (all browsers)
- **delay**: Give the browser X seconds before getting the result from it (PhantomJS only)
- **timeout**: Give the browser maximum amount of time to render (PhantomJS only)
- **with_images**: Allow to render images? Mostly its not necessary (all browsers)
- **debug**: Print debugging information instead of the result (PhantomJS only)
- **renderer**: Decides if we want to use phantomjs or chromium
- **chromium_binary**: The command name for Chromium, could be eg. chrome, chromium, google-chrome-beta or some path
- **window_size**: Browser window size (Chromium only)
- **open_process_limit**: Limit the amount of workers, so the server will not blow up (Chromium only)
- **wait_for_process_time**: Amount of seconds to wait for a process when the maximum of opened browsers (defined in open_process_limit) is reached
                         After this time there will be a 503 returned.

## Installation

`make deploy`

## Browser differences

| Feature          | Chromium                  | PhantomJS                                     |
| -------------    | -------------             | -----                                         |
| Response body    | Fast fetch, waits for everything | Based on delay or timeout              |
| Response headers | Not supported | Supported with exclusion list                             |
| Response code    | Not supported             | Supported                                     |
| Requirements     | Just the Chromium browser | The PhantomJS, and the NodeJS                 |
| Proxy support    | Yes                       | No                                            |

## Usage

The usage is simple, just redirect any request to this service, it should go through index.php
Use the webserver to redirect requests properly and validate which domains are allowed.

#### Render a different page than the request

You can render any page eg. facebook.com by providing the URL in the header.

Example request:

```
GET /

X-Render-Url: https://www.facebook.com/events/209461189653825/
```

This allows to use the prerender service for scrapping any pages in a microservice architecture,
with scaled services behind the load balancer.

## Cache regeneration

The service keeps the history of successful requests from robots, so later there is a possibility to click
all of those links again using a script `reclick-cache`

To pass the clicks from `reclick-cache` you have to redirect it's useragent `curl/reclick-cache` through the 
prerenderer, so accept it as a crawler on the webserver configuration just like you do for search engines.

Schedule `./bin/reclick-cache.sh` to run eg. daily to regenerate the cache, so the crawlers could hit the cached version
instead of waiting long for the page to load.

## Proxy support

_(Works only in Chromium)_

By passing a header "X-Proxy-Address" you can enforce the browser to use a proxy server.

Example:

```
X-Proxy-Address: http://localhost:8080
```
