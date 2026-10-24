<?php
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
require dirname(__DIR__).'/app/bootstrap.php';require_once ROOT.'/app/research-update.php';
db()->beginTransaction();apply_research_update();db()->commit();echo "Verified academy content applied.\n";
