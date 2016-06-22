<?php
header('Content-Type: application/json; charset=utf-8');
if (!empty($_GET)) {
  $wiki = $_GET['wiki'];
  $radius = $_GET['radius'];
  $lat = $_GET['lat'];
  $lon = $_GET['lon'];

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
  echo json_encode($dataToReturn);
}
