<?php
$action=is_string($_POST['action']??null)?$_POST['action']:'';
if($action==='logout') { audit('Signed out','Superadmin session ended.'); $_SESSION=[]; session_destroy(); redirect('/superadmin/login'); }
if($action==='save_settings') {
    $group=($_POST['group']??'')==='home'?'home':'site'; $current=record('settings',$group,true);
    $values=[];
    foreach(setting_fields($group) as $key=>$config) {
        $value=is_string($_POST['fields'][$key]??null)?trim($_POST['fields'][$key]):'';
        if(mb_strlen($value)>6000) { flash('Please keep each field under 6,000 characters.','error'); redirect('/superadmin/'.($group==='home'?'home':'settings')); }
        if($config[1]==='image' && $value!=='' && !image_path($value)) { flash('Select an image from the media library.','error'); redirect('/superadmin/'.($group==='home'?'home':'settings')); }
        if(($config[1]==='url' || str_ends_with($key,'_url')) && $value!=='' && !safe_link($value)) { flash('Enter a valid website link.','error'); redirect('/superadmin/'.($group==='home'?'home':'settings')); }
        if($config[1]==='email' && $value!=='' && !filter_var($value,FILTER_VALIDATE_EMAIL)) { flash('Enter a valid email address.','error'); redirect('/superadmin/settings'); }
        $values[$key]=$value;
    }
    foreach($group==='home'?['heading','accent','cta_label','cta_url']:['name','short_name','seo_title'] as $required) if(empty($values[$required])) { flash('Please fill in the essential identity and heading fields.','error'); redirect('/superadmin/'.($group==='home'?'home':'settings')); }
    if(!hash_equals(hash('sha256',$current['data']), (string)($_POST['revision']??''))) { flash('This content changed in another window. Reload and reapply your changes.','error'); redirect('/superadmin/'.($group==='home'?'home':'settings')); }
    query('UPDATE content SET data=?,updated_at=? WHERE id=?',[json_encode($values,JSON_UNESCAPED_UNICODE),now(),$current['id']]); audit('Updated website settings',$group.' content updated.'); flash('Changes published. Your website is up to date.'); redirect('/superadmin/'.($group==='home'?'home':'settings'));
}
if(in_array($action,['save_content','delete_content'],true)) {
    $type=is_string($_POST['type']??null)?$_POST['type']:'';
    if(!isset($types[$type])) { http_response_code(400); exit('Invalid content type.'); }
    $id=(int)($_POST['id']??0); $current=$id?query('SELECT * FROM content WHERE id=? AND type=?',[$id,$type])->fetch():null;
    if($id && !$current) { http_response_code(404); exit('Content not found.'); }
    if($action==='delete_content') {
        if($type==='pages') { flash('Core website pages can be edited or unpublished, but cannot be deleted.','error'); redirect('/superadmin/content?type=pages'); }
        audit('Deleted content',$type.': '.$current['title']); query('DELETE FROM content WHERE id=? AND type=?',[$id,$type]); flash('Content deleted.'); redirect('/superadmin/content?type='.$type);
    }
    $title=trim(is_string($_POST['title']??null)?$_POST['title']:'');
    $slug=slugify(is_string($_POST['slug']??null)&&$_POST['slug']!==''?$_POST['slug']:$title);
    if($current && $type==='pages') $slug=$current['slug'];
    $status=($_POST['status']??'')==='published'?'published':'draft';
    $values=[]; $error='';
    if($title==='' || mb_strlen($title)>180 || strlen($slug)>180) $error='Enter a title and URL slug under 180 characters.';
    foreach($types[$type]['fields'] as $key=>$config) {
        $value=is_string($_POST['fields'][$key]??null)?trim($_POST['fields'][$key]):'';
        if(mb_strlen($value)>20000) $error='Please shorten the '.$config[0].' field.';
        if($config[1]==='image' && $value!=='' && !image_path($value)) $error='Select a valid image from the media library.';
        if($config[1]==='url' && $value!=='' && !safe_link($value)) $error='Enter a valid URL for '.$config[0].'.';
        if($config[1]==='email' && $value!=='' && !filter_var($value,FILTER_VALIDATE_EMAIL)) $error='Enter a valid email address.';
        if($config[1]==='date' && $value!=='' && (!strtotime($value) || date('Y-m-d',strtotime($value))!==$value)) $error='Enter a valid date.';
        $values[$key]=$value;
    }
    if($type==='gallery' && $status==='published' && empty($values['image'])) $error='A published gallery item needs an image.';
    if($type==='branches' && $status==='published' && (empty($values['address']) || empty($values['city']))) $error='Add a city and full address before publishing a branch.';
    if($type==='coaches' && $status==='published' && (empty($values['credentials']) || empty($values['body']))) $error='Add a biography and verified qualifications before publishing a coach.';
    if($type==='achievements' && $status==='published' && (empty($values['athlete']) || empty($values['event']) || empty($values['result']))) $error='Add the athlete, event and verified result before publishing.';
    if(query('SELECT id FROM content WHERE type=? AND slug=? AND id<>?',[$type,$slug,$id])->fetch()) $error='This URL slug is already in use. Please choose another.';
    if($current && !hash_equals(hash('sha256',json_encode($current)),(string)($_POST['revision']??''))) $error='This content changed in another window. Reload before saving.';
    if($error) { $_SESSION['content_old']=['title'=>$title,'slug'=>$slug,'status'=>$status,'sort_order'=>(int)($_POST['sort_order']??0),'fields'=>$values]; flash($error,'error'); redirect('/superadmin/edit?type='.$type.($id?'&id='.$id:'')); }
    $sort=max(-10000,min(10000,(int)($_POST['sort_order']??0)));
    if($id) query('UPDATE content SET slug=?,title=?,status=?,sort_order=?,data=?,updated_at=? WHERE id=? AND type=?',[$slug,$title,$status,$sort,json_encode($values,JSON_UNESCAPED_UNICODE),now(),$id,$type]);
    else { query('INSERT INTO content(type,slug,title,status,sort_order,data,updated_at) VALUES(?,?,?,?,?,?,?)',[$type,$slug,$title,$status,$sort,json_encode($values,JSON_UNESCAPED_UNICODE),now()]); $id=(int)db()->lastInsertId(); }
    audit('Saved '.$status.' content',$type.': '.$title); flash($status==='published'?'Content published to the website.':'Draft saved. It is hidden from public visitors.'); redirect('/superadmin/content?type='.$type);
}
if($action==='upload_media') {
    $f=$_FILES['image']??[]; $error=''; $alt=trim(is_string($_POST['alt']??null)?$_POST['alt']:'');
    if(($f['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK) $error='Upload failed. Choose a JPG, PNG or WebP file under 8 MB (also check your PHP upload limit).';
    elseif(($f['size']??0)>8*1024*1024) $error='Please use an image smaller than 8 MB.';
    elseif($alt==='' || mb_strlen($alt)>300) $error='Add a short image description (up to 300 characters).';
    else {
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        $info=@getimagesize($f['tmp_name']);
        if(!in_array($mime,['image/jpeg','image/png','image/webp'],true) || !$info || $info[0]*$info[1]>30000000) $error='Use a valid JPG, PNG or WebP image up to 30 megapixels.';
        else {
            $img=match($mime){'image/jpeg'=>@imagecreatefromjpeg($f['tmp_name']),'image/png'=>@imagecreatefrompng($f['tmp_name']),'image/webp'=>@imagecreatefromwebp($f['tmp_name'])};
            if(!$img) $error='This image could not be processed.';
            else {
                imagealphablending($img,false); imagesavealpha($img,true);
                $dest='/uploads/'.bin2hex(random_bytes(16)).'.webp';
                if(imagewebp($img,ROOT.$dest,88)) { query('INSERT INTO media(path,alt,created_at) VALUES(?,?,?)',[$dest,$alt,now()]); audit('Uploaded media',$alt); flash('Image added to your media library. Select it in any image field.'); }
                else $error='The upload folder is not writable.';
                unset($img);
            }
        }
    }
    if($error) flash($error,'error'); redirect('/superadmin/media');
}
if(in_array($action,['mark_viewed','enquiry_status','save_booking'],true)) {
    $id=(int)($_POST['id']??0); $enquiry=query('SELECT * FROM enquiries WHERE id=?',[$id])->fetch();
    if(!$enquiry) { http_response_code(404); exit('Request not found.'); }
    if($action==='mark_viewed') {
        if($enquiry['status']==='New') { query("UPDATE enquiries SET status='Viewed' WHERE id=? AND status='New'",[$id]); audit('Viewed enquiry',$enquiry['reference']); }
        header('Content-Type: application/json'); echo json_encode(['ok'=>true,'unread'=>(int)query("SELECT count(*) FROM enquiries WHERE status='New'")->fetchColumn(),'enquiries'=>(int)query("SELECT count(*) FROM enquiries WHERE status='New' AND kind='enquiry'")->fetchColumn(),'demos'=>(int)query("SELECT count(*) FROM enquiries WHERE status='New' AND kind='demo'")->fetchColumn()]); exit;
    }
    if($action==='enquiry_status') {
        $status=($_POST['status']??'')==='Contacted'?'Contacted':'New'; query('UPDATE enquiries SET status=? WHERE id=?',[$status,$id]); audit('Updated enquiry status',$enquiry['reference'].' → '.$status); flash('Request marked as '.($status==='New'?'unread':'contacted').'.');
    }
    if($action==='save_booking') {
        $status=(string)($_POST['booking_status']??'Requested');
        $scheduled=(string)($_POST['scheduled_at']??''); $coach=trim((string)($_POST['coach']??'')); $notes=trim((string)($_POST['notes']??''));
        if(!in_array($status,['Requested','Confirmed','Rescheduled','Attended','No-show','Cancelled'],true) || mb_strlen($notes)>6000 || mb_strlen($coach)>180 || ($scheduled!=='' && (!strtotime($scheduled) || date('Y-m-d\TH:i',strtotime($scheduled))!==$scheduled))) { flash('Please check the booking details and try again.','error'); redirect('/superadmin/demos'); }
        if(in_array($status,['Confirmed','Rescheduled'],true) && ($scheduled==='' || $coach==='')) { flash('A confirmed or rescheduled session needs a date, time and assigned coach.','error'); redirect('/superadmin/demos'); }
        // BEGIN IMMEDIATE serialises competing booking updates across requests.
        db()->exec('BEGIN IMMEDIATE');
        if($enquiry['kind']==='demo' && $scheduled!=='' && $coach!=='' && in_array($status,['Confirmed','Rescheduled'],true)) {
            $conflict=query("SELECT reference FROM enquiries WHERE id<>? AND kind='demo' AND lower(trim(coach))=lower(trim(?)) AND scheduled_at=? AND booking_status IN ('Confirmed','Rescheduled')",[$id,$coach,$scheduled])->fetch();
            if($conflict) { db()->exec('ROLLBACK'); flash('This coach already has a booking at that time ('.$conflict['reference'].'). Choose another time.','error'); redirect('/superadmin/demos'); }
        }
        query('UPDATE enquiries SET booking_status=?,scheduled_at=?,coach=?,notes=? WHERE id=?',[$status,$scheduled,$coach,$notes,$id]);
        audit('Updated '.($enquiry['kind']==='demo'?'demo booking':'follow-up notes'),$enquiry['reference'].' → '.$status.'; date: '.$scheduled.'; coach: '.$coach.'; notes: '.$notes);
        db()->exec('COMMIT'); flash('Request updated. Contact the applicant separately to confirm any arrangements.');
    }
    $anchor = $action==='enquiry_status' && $status==='New' ? '' : '#request-'.$id;
    redirect('/superadmin/'.($enquiry['kind']==='demo'?'demos':'enquiries').$anchor);
}
http_response_code(400); exit('Unknown action.');
