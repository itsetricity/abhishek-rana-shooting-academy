<?php
// Explicitly invoked for a fresh seed or the user-requested redesign, never per request.
function apply_website_defaults(): void {
    foreach(['home'=>home_defaults(),'site'=>redesign_site_defaults()] as $slug=>$values) {
        $current=record('settings',$slug,true);
        query('UPDATE content SET data=?,updated_at=? WHERE id=?',[json_encode(array_replace($current['fields'],$values),JSON_UNESCAPED_UNICODE),now(),$current['id']]);
    }
    foreach(redesign_pages() as $slug=>$page) {
        $current=record('pages',$slug,true);
        $data=array_replace($current['fields']??[],['eyebrow'=>$page[1],'heading'=>$page[2],'intro'=>$page[3],'body'=>$page[4],'seo_title'=>$page[0].' | Abhishek Rana Shooting Academy','seo_description'=>$page[3]]);
        if($current) query('UPDATE content SET title=?,data=?,updated_at=? WHERE id=?',[$page[0],json_encode($data,JSON_UNESCAPED_UNICODE),now(),$current['id']]);
        else query('INSERT INTO content(type,slug,title,status,data,updated_at) VALUES(?,?,?,?,?,?)',['pages',$slug,$page[0],'published',json_encode($data,JSON_UNESCAPED_UNICODE),now()]);
    }
    $programs=[
        'air-rifle'=>['10m Air Rifle','Rifle training','Beginners & developing shooters','Learn balance, aiming and shot consistency with a precision air rifle.','/assets/images/rifle-training.webp'],
        'air-pistol'=>['10m Air Pistol','Pistol training','Beginners & developing shooters','Work on your stance, trigger control and focus, one shot at a time.','/assets/images/pistol-training.webp'],
        'foundation'=>['Beginner Introduction','New to shooting?','First-time participants','Explore the sport, understand range safety and find the discipline you enjoy.','/assets/images/indoor-range.webp'],
    ];
    foreach($programs as $slug=>$program) {
        $current=record('programs',$slug,true);if(!$current)continue;
        $data=array_replace($current['fields'],['category'=>$program[1],'level'=>$program[2],'summary'=>$program[3],'body'=>$program[3]."\n\nTalk to the academy about the right training level for you. We will confirm program availability, equipment arrangements, eligibility, fees and session timings before you enrol.",'image'=>$program[4]]);
        query('UPDATE content SET title=?,data=?,updated_at=? WHERE id=?',[$program[0],json_encode($data,JSON_UNESCAPED_UNICODE),now(),$current['id']]);
    }
    // Only add reference examples if there are no existing gallery records.
    if(!records('gallery',true)) foreach(reference_photos() as $i=>$photo) {
        query('INSERT INTO content(type,slug,title,status,sort_order,data,updated_at) VALUES(?,?,?,?,?,?,?)',['gallery','sport-reference-'.($i+1),$photo['title'],'published',$i,json_encode(['category'=>'Sport reference','caption'=>$photo['alt'].'. Reference photo; not an academy image.','image'=>$photo['path']]),now()]);
    }
}
