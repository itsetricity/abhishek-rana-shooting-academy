<?php
// Content upgrade invoked by the versioned deployment upgrader or CLI helper.
function apply_sporting_update(): void {
 $map=['/assets/images/pistol-training.webp'=>'/assets/images/manu-portrait.webp','/assets/images/rifle-training.webp'=>'/assets/images/indian-rifle.webp','/assets/images/range-practice.webp'=>'/assets/images/indoor-range.webp'];
 foreach(query('SELECT * FROM content')->fetchAll() as $row){$data=json_decode($row['data'],true);$changed=false;foreach($data as $key=>$value)if(is_string($value)&&isset($map[$value])){$data[$key]=$map[$value];$changed=true;}
 if($row['type']==='gallery'&&str_starts_with($row['slug'],'sport-reference-')){foreach(reference_photos() as $photo)if(($data['image']??'')===$photo['path']){$row['title']=$photo['title'];$data['caption']=$photo['alt'].'. Licensed sports reference; no academy affiliation implied.';$changed=true;}}
 if($row['type']==='settings'&&$row['slug']==='home'){$data=array_replace(home_defaults(),$data);$data['image_caption']=home_defaults()['image_caption'];$changed=true;}
 if($changed)query('UPDATE content SET title=?,data=?,updated_at=? WHERE id=?',[$row['title'],json_encode($data,JSON_UNESCAPED_UNICODE),now(),$row['id']]);
 }
 if(!record('pages','events',true)){ $page=redesign_pages()['events']; query('INSERT INTO content(type,slug,title,status,data,updated_at) VALUES(?,?,?,?,?,?)',['pages','events',$page[0],'published',json_encode(['eyebrow'=>$page[1],'heading'=>$page[2],'intro'=>$page[3],'body'=>$page[4],'seo_title'=>'Event calendar | Abhishek Rana Shooting Academy','seo_description'=>$page[3]]),now()]); }
 if(!record('events','introductory-demo',true))query('INSERT INTO content(type,slug,title,status,data,updated_at) VALUES(?,?,?,?,?,?)',['events','introductory-demo','Your first shooting session','published',json_encode(['category'=>'Demo enquiries open','date'=>'','time'=>'By prior confirmation','location'=>'Contact the academy to confirm your branch','summary'=>'New to air rifle or air pistol? Request a demo. The academy will confirm your visit date and available training.','body'=>'Send a demo request with your preferred date, experience and location. This listing does not confirm a scheduled event or booking. Your session is arranged separately with the academy.','registration_url'=>'/demo-class']),now()]);
}
