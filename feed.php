<?php
$url = 'http://lab.linkeddata.deri.ie/2010/planning-apps/feed';

$sourcescraper = 'latest-galway-city-planning-applications';
$start = date('Y-m-d', time() - 28 * 24 * 60 * 60);
$end = date('Y-m-d', time() + 24 * 60 * 60);
$data_url = "http://api.scraperwiki.com/api/1.0/datastore/getdatabydate?format=json&name=$sourcescraper&start_date=$start&end_date=$end";
$applications = json_decode(file_get_contents($data_url));

$max_date = $start;
foreach ($applications as $app) {
  if ($app->date > $max_date) $max_date = $app->date;
}

header('Content-Type: application/atom+xml');
echo "<?xml version=\"1.0\"?>\n";

?><feed xmlns="http://www.w3.org/2005/Atom">
  <title>Galway City planning applications</title>
  <subtitle>Recent planning applications submitted to Galway City Council</subtitle>
  <link href="http://scraperwikiviews.com/run/map-latest-galway-city-planning-apps"/>
  <link rel="self" href="<?php e($url); ?>"/>
  <id><?php e($url); ?></id>
  <updated><?php e($max_date); ?>T06:00:00Z</updated>
<?php foreach ($applications as $app) { ?>
  <entry>
    <title><?php e($app->address); ?> [<?php e($app->appref); ?>]</title>
    <link href="<?php e($app->url); ?>"/>
    <id>http://scraperwikiviews.com/run/galway-city-planning-feed#<?php e($app->appref); ?></id>
    <updated><?php e($app->date); ?>T06:00:00Z</updated>
    <author><name><?php e($app->applicant); ?></name></author>
    <content type="xhtml" xml:lang="en">
      <div xmlns="http://www.w3.org/1999/xhtml">
        <p><?php e($app->details); ?></p>
<?php if (isset($app->latlng)) { ?>
        <p><img src="<?php e('http://maps.google.com/maps/api/staticmap?size=200x200&zoom=16&maptype=hybrid&markers=size:mid|' . $app->latlng[0] . ',' . $app->latlng[1]. '&sensor=false'); ?>" /></p>
<?php } ?>
      </div>
    </content>
  </entry>
<?php } ?>
</feed><?php

function e($s) {
  echo htmlspecialchars($s);
}
