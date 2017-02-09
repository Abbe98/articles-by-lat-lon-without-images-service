# articles-by-lat-lon-without-images-service

This API allows you to return a list of articles within a geographic radius that does not contain any valuable images. Images such as icons and files included from templates etc are ignored. All Wikipedias are supported. The API follows the same scheme as the official API, incompatible features is available through optional parameters.

Sage Ross made a Pepple watchface with this API you should [check it out](http://ragesoss.com/blog/2016/11/05/diderot-a-pebble-watchface-for-finding-nearby-unillustrated-wikipedia-articles/).

# Usage

**API endpoint**
```
http://tools.wmflabs.org/articles-by-lat-lon-without-images/index.php
```

**Required parameters**

 - `wiki` defines the Wikipedia instance to connect to(defined by language code).
 - `lat` the latitude of the search center.
 - `lon` the longitude of the search center.
 - `radius` the radius to search within(specified in meters).

**Example**

```
http://tools.wmflabs.org/articles-by-lat-lon-without-images/index.php?wiki=sv&lat=59.06708056&lon=16.36239722&radius=10000
```

**Optional parameters**

 - `reencode=true` reencodes non-ascii characters(this differs from the Mediawiki API).

**Example**

```
http://tools.wmflabs.org/articles-by-lat-lon-without-images/index.php?wiki=sv&lat=59.06708056&lon=16.36239722&radius=10000&reencode=true
```

**Wikidata**

To fetch Wikidata items without images replace the Wikipedia language code with `wikidata`.

**Example**

```
http://tools.wmflabs.org/articles-by-lat-lon-without-images/index.php?wiki=wikidata&lat=58.1&lon=16.1&radius=1500
```
