<?php
// CLI-only, explicit one-time content update requested by the owner.
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
require dirname(__DIR__).'/app/bootstrap.php';
require_once ROOT.'/app/apply-website-defaults.php';
db()->beginTransaction();
apply_website_defaults();
audit('Redesigned public website','Plain-language content and licensed reference photographs applied.');
db()->commit();
echo "Public website content updated. Administrator credentials and enquiries preserved.\n";
