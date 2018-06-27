<?php
defined('AUTH') or die('Restricted access');

const XML_PATH = '/temp/yml.xml';

header('Content-disposition: attachment; filename="yml.xml"');
header('Content-type: "text/xml"; charset="utf8"');
readfile($_SERVER['DOCUMENT_ROOT'].XML_PATH);
exit();
