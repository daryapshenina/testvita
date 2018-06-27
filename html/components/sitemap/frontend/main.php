<?php
defined('AUTH') or die('Restricted access');

header("Content-type: text/xml");

include_once $root.'/classes/Sitemap.php';

$sitemap = new Sitemap();
$sitemap->run();
$sitemap->save('php://output');

exit;
