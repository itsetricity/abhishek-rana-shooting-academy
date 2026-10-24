<?php
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
require dirname(__DIR__).'/app/bootstrap.php';
require_once ROOT.'/app/sporting-update.php';
db()->beginTransaction();apply_sporting_update();db()->commit();echo "Sporting content update complete.\n";
