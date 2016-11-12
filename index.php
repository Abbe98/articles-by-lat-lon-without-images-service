<?php
header('Content-Type: application/json; charset=utf-8');
if (!empty($_GET)) {
  $wiki = $_GET['wiki'];
  $radius = $_GET['radius'];
  $lat = $_GET['lat'];
  $lon = $_GET['lon'];

  if ($wiki !== 'wikidata') {
    $query = 'https://' . $wiki . '.wikipedia.org/w/api.php?action=query&format=json&prop=images%7Ccoordinates&imlimit=500&generator=geosearch&redirects=1&colimit=500&ggscoord=' . $lat . '%7C' . $lon . '&ggsradius=' . $radius . '&ggslimit=50&ggsprimary=primary';

    $data = json_decode(file_get_contents($query, false, NULL));

    $dataToReturn = array();
    foreach ($data->query->pages as $page) {
      if (!empty($page->images)) {
        $needsImage = true;
        foreach ($page->images as $image) {
          if (strpos($image->title, '.svg') === false && strpos($image->title, '.png') === false) {
            $needsImage = false;
          }
        }

        if ($needsImage) {
          $pageObject;
          $pageObject['id'] = $page->pageid;
          $pageObject['title'] = $page->title;
          $pageObject['lat'] = $page->coordinates[0]->lat;
          $pageObject['lon'] = $page->coordinates[0]->lon;
          $dataToReturn[] = $pageObject;
        }

      } else {
        $pageObject;
        $pageObject['id'] = $page->pageid;
        $pageObject['title'] = $page->title;
        $pageObject['lat'] = $page->coordinates[0]->lat;
        $pageObject['lon'] = $page->coordinates[0]->lon;
        $dataToReturn[] = $pageObject;
      }
    }
  } else {
    // Wikidata takes kilometers not meters...
    $radius = $radius / 1000;

    $query = 'https://query.wikidata.org/bigdata/namespace/wdq/sparql?query=SELECT%3Fitem(SAMPLE(%3Fitem_label)as%3Flabel)(SAMPLE(%3Flocation)as%3Flocation)WHERE%7BSERVICE%20wikibase%3Aaround%7B%3Fitem%20wdt%3AP625%3Flocation.bd%3AserviceParam%20wikibase%3Acenter"Point(' . $lon . '%20' . $lat . ')"%5E%5Egeo%3AwktLiteral.%20bd%3AserviceParam%20wikibase%3Aradius"' . $radius . '".%7DMINUS%20%7B%3Fitem%20wdt%3AP18%5B%5D.%7DOPTIONAL%7B%3Fitem%20rdfs%3Alabel%3Fitem_label.%7D%7DGROUP%20BY%20%3Fitem&format=json';

    $data = json_decode(file_get_contents($query, false, NULL));

    $dataToReturn = array();
    foreach ($data->results->bindings as $item) {
      $itemObject;
      preg_match('/\d+$/', $item->item->value, $uriMatch);
      $itemObject['id'] = intval($uriMatch[0]);
      $itemObject['title'] = $item->label->value;
      preg_match_all('/((-)|())\d+\.\d+/', $item->location->value, $lonlat);
      $itemObject['lat'] = floatval($lonlat[0][1]);
      $itemObject['lon'] = floatval($lonlat[0][0]);
      $dataToReturn[] = $itemObject;
    }
  }

  if (isset($_GET['reencode'])) {
    echo json_encode($dataToReturn, JSON_UNESCAPED_UNICODE);
  } else {
    echo json_encode($dataToReturn);
  }
}
