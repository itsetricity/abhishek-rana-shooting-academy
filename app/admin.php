<?php
header('Cache-Control: no-store, private');
header('X-Robots-Tag: noindex, nofollow');
$adminRoute = substr($path, strlen('/superadmin')) ?: '/';
$types = content_types();
if ($adminRoute==='/login') {
    if(is_admin()) redirect('/superadmin');
    $loginError='';
    if($_SERVER['REQUEST_METHOD']==='POST') {
        if(!valid_csrf()) $loginError='Your session expired. Please try again.';
        elseif(limited('login:'.($_SERVER['REMOTE_ADDR']??''),10,900)) $loginError='Too many sign-in attempts. Please wait 15 minutes.';
        else {
            $password=env('ADMIN_PASSWORD');
            $provided=is_string($_POST['password']??null)?$_POST['password']:'';
            $matches=$password!=='' && (str_starts_with($password,'$2') || str_starts_with($password,'$argon') ? password_verify($provided,$password) : hash_equals($password,$provided));
            if(is_string($_POST['username']??null) && hash_equals(env('ADMIN_USERNAME'),trim($_POST['username'])) && $matches) {
                session_regenerate_id(true); $_SESSION['admin']=env('ADMIN_USERNAME'); $_SESSION['last_active']=time(); $_SESSION['csrf']=bin2hex(random_bytes(32)); audit('Signed in','Superadmin session started.'); redirect('/superadmin');
            }
            $loginError='The username or password is incorrect.';
        }
    }
    require __DIR__.'/login.php'; exit;
}
if(!is_admin()) redirect('/superadmin/login');
if($_SERVER['REQUEST_METHOD']==='POST') {
    if(!valid_csrf()) { http_response_code(419); exit('Your session expired. Go back, reload the page and try again.'); }
    require __DIR__.'/admin-actions.php';
}
$section=trim($adminRoute,'/') ?: 'dashboard';
$section=explode('/',$section)[0];
$allowed=['dashboard','cms','home','settings','content','edit','media','enquiries','demos','audit','export'];
if(!in_array($section,$allowed,true)) { http_response_code(404); $section='not-found'; }
if($section==='export') {
    $kind=($_GET['kind']??'')==='demo'?'demo':'enquiry';
    header('Content-Type: text/csv; charset=UTF-8'); header('Content-Disposition: attachment; filename="academy-'.$kind.'-'.date('Y-m-d').'.csv"');
    $out=fopen('php://output','w'); fwrite($out,"\xEF\xBB\xBF");
    fputcsv($out,['Reference','Name','Email','Phone','Branch','Program','Experience','Message','Status','Demo status','Preferred date','Preferred time','Scheduled at','Coach','Notes','Received','Consent recorded'],',','"','');
    $rows=query('SELECT * FROM enquiries WHERE kind=? ORDER BY id DESC',[$kind])->fetchAll();
    foreach($rows as $r) {
        $values=[]; foreach(['reference','name','email','phone','branch','program','experience','message','status','booking_status','preferred_date','preferred_time','scheduled_at','coach','notes','created_at','consent_at'] as $k) { $v=(string)($r[$k]??''); $values[]=preg_match('/^[\s]*[=+\-@]/u',$v)?"'".$v:$v; }
        fputcsv($out,$values,',','"','');
    }
    audit('Exported enquiries',count($rows).' '.$kind.' records exported.'); fclose($out); exit;
}
$type=(string)($_GET['type']??'pages');
if(in_array($section,['content','edit'],true) && !isset($types[$type])) { http_response_code(404); $section='not-found'; }
$unread=(int)query("SELECT count(*) FROM enquiries WHERE status='New'")->fetchColumn();
$unreadEnquiries=(int)query("SELECT count(*) FROM enquiries WHERE status='New' AND kind='enquiry'")->fetchColumn();
$unreadDemos=(int)query("SELECT count(*) FROM enquiries WHERE status='New' AND kind='demo'")->fetchColumn();
$titles=['dashboard'=>'Overview','cms'=>'Website CMS','home'=>'Homepage editor','settings'=>'Brand & settings','content'=>$types[$type]['label']??'Content','edit'=>(isset($_GET['id'])?'Edit ':'New ').($types[$type]['singular']??'content'),'media'=>'Media library','enquiries'=>'Enquiries','demos'=>'Demo requests','audit'=>'Activity log','not-found'=>'Page not found'];
$adminTitle=$titles[$section]??'Superadmin';
require __DIR__.'/admin-layout.php';
