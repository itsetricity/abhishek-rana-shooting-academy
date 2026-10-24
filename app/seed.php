<?php
function seed_record(string $type, string $slug, string $title, array $data, string $status='published', int $order=0): void {
    query('INSERT INTO content(type,slug,title,status,sort_order,data,updated_at) VALUES(?,?,?,?,?,?,?)',[$type,$slug,$title,$status,$order,json_encode($data,JSON_UNESCAPED_UNICODE),now()]);
}
db()->beginTransaction();
seed_record('settings','site','Website settings',[
 'name'=>'Abhishek Rana Shooting Academy','short_name'=>'ARSA','tagline'=>'AIM WITH PURPOSE.',
 'phone'=>'','email'=>'','address'=>'','hours'=>'Visits by confirmed appointment',
 'seo_title'=>'Abhishek Rana Shooting Academy | Find your focus',
 'seo_description'=>'Discover precision shooting at Abhishek Rana Shooting Academy. Explore training pathways, meet the academy and request your first demo session.',
 'footer_text'=>'A place to discover your focus, develop your discipline and pursue your potential in shooting sport.',
 'illustration_notice'=>'Preview content · Imagery is illustrative. Programs and branch details are subject to academy confirmation.',
]);
seed_record('settings','home','Homepage',[
 'eyebrow'=>'ABHISHEK RANA SHOOTING ACADEMY',
 'heading'=>'GREAT SHOTS|BEGIN WITH', 'accent'=>'GREATER FOCUS.',
 'intro'=>'Find your stillness. Build your discipline. Discover what you’re capable of—one shot at a time.',
 'hero_image'=>'/assets/images/hero.webp','image_caption'=>'Illustrative shooting-sport imagery',
 'cta_label'=>'Find your first shot','cta_url'=>'/demo-class','secondary_label'=>'Explore the academy','secondary_url'=>'/about',
 'about_eyebrow'=>'MORE THAN A SPORT. A MINDSET.',
 'about_heading'=>'The target is just|the beginning.',
 'about_body'=>'Shooting is a quiet pursuit of something extraordinary. It asks for patience, rewards consistency and teaches you to trust your preparation.\n\nAt Abhishek Rana Shooting Academy, our vision is to make that journey approachable—from your first experience on the range to a more purposeful training routine.',
 'about_image'=>'',
 'program_heading'=>'YOUR AMBITION.|YOUR STARTING POINT.',
 'journey_heading'=>'A CLEAR PATH.|ONE SHOT AT A TIME.',
 'step1_title'=>'Discover your discipline','step1_body'=>'Request a demo. Get familiar with the range, ask questions and explore the sport at your pace.',
 'step2_title'=>'Build your foundation','step2_body'=>'Discuss your goals, understand the fundamentals and find a training pathway that fits.',
 'step3_title'=>'Train with intention','step3_body'=>'Develop a consistent practice routine, reflect on your progress and prepare for your next challenge.',
 'branch_heading'=>'FIND YOUR|PLACE TO FOCUS.',
 'branch_intro'=>'One academy. A shared commitment to precision. Tell us your preferred location and our team will help you find your nearest training centre.',
 'cta_heading'=>'YOUR NEXT CHAPTER|STARTS WITH A SHOT.',
 'cta_body'=>'Curious about shooting? You don’t need to have it all figured out. Start with a conversation.',
]);
$pages=[
 'about'=>['About the academy','THE ACADEMY','A QUIET PURSUIT.\nAN EXTRAORDINARY JOURNEY.','A culture of focus, discipline and continuous improvement.','Abhishek Rana Shooting Academy is building a connected academy experience around a simple belief: meaningful progress begins with the fundamentals.\n\nOur vision brings a welcoming introduction to shooting sport together with structured development for those ready to go further. Safety, considered coaching and purposeful practice shape that approach.\n\nMeet the team, discuss your ambitions and explore the right starting point. Coach biographies, verified milestones and range details will be added as they are confirmed.'],
 'programs'=>['Training programs','FIND YOUR DISCIPLINE','START CURIOUS.\nTRAIN WITH PURPOSE.','Explore a starting point that matches your experience and ambition.',''],
 'branches'=>['Our branches','THE ACADEMY NETWORK','CLOSER TO HOME.\nFOCUSED ON YOU.','Find your training environment. Branch profiles will include addresses, facilities, opening hours and directions as they are confirmed.',''],
 'coaches'=>['Coaches & team','PEOPLE BEHIND THE PROGRESS','GOOD GUIDANCE.\nGREATER POSSIBILITIES.','Get to know the people who will guide your training.','Approved coach profiles and verified qualifications will appear here. In the meantime, tell us your preferred discipline and we’ll help you connect with the academy.'],
 'achievements'=>['Achievements','MOMENTS THAT MATTER','PROGRESS WORTH\nCELEBRATING.','The stories behind the scores, the discipline behind the result.','Verified athlete achievements and competition results will be shared here.'],
 'gallery'=>['Inside the academy','LIFE ON THE RANGE','FOCUS. EFFORT.\nEVERYDAY PROGRESS.','A closer look at training, academy life and the moments in between.','Official academy photography will be added soon.'],
 'competitions'=>['Competitions & notices','STAY IN THE KNOW','YOUR NEXT\nCHALLENGE AWAITS.','Academy updates, competition information and official resources.','Competition eligibility and registration are governed by the relevant organiser. Always check the official notice before making plans.'],
 'contact'=>['Contact the academy','LET’S TALK SHOOTING','EVERY JOURNEY\nSTARTS SOMEWHERE.','Ask a question, explore a program or find your nearest branch.',''],
 'admission'=>['Admission enquiry','TAKE THE FIRST STEP','TELL US\nYOUR AMBITION.','Share a little about yourself. The academy will help you explore the next step.',''],
 'demo-class'=>['Request a demo','EXPERIENCE THE SPORT','YOUR FIRST SHOT\nAT SOMETHING GREAT.','Request a session, meet the academy and discover your starting point.',''],
 'privacy'=>['Privacy notice','YOUR INFORMATION','PRIVACY &\nYOUR CHOICES.','How this website handles information shared through its forms.','We collect the name, contact details, preferences and message you submit so the academy can respond to enquiries and coordinate demo requests. Submission does not create a student account.\n\nInformation is accessible to authorised academy administrators. It is not sold. Necessary service providers may process information when configured to support communications and website hosting.\n\nPlease do not submit identification documents, biometric information or sensitive medical details through these forms. If the prospective student is a minor, a parent or guardian should complete the enquiry using their own contact details.\n\nContact the academy through the enquiry form to request access, correction or deletion of your information. The academy should confirm its retention periods and official privacy contact before the public launch.\n\nEssential session cookies support secure forms and administrator authentication. This MVP does not include advertising trackers.'],
 'terms'=>['Website terms','BEFORE YOU BEGIN','A FEW THINGS\nTO KNOW.','Information about website use, enquiries and demo requests.','Website content introduces the academy and its proposed training pathways. Illustrative images do not show the academy’s actual athletes or facilities. Program availability, fees, eligibility and branch details must be confirmed with the academy.\n\nA demo submission is a request, not a confirmed booking. The academy will confirm availability and the arrangements separately. Enquiries and demo attendance do not activate a student account; admissions require owner approval.\n\nFees, cancellation terms, consents and training arrangements will be provided by the academy before enrolment. Official competition registrations are handled by the relevant organisers.\n\nThese are initial website terms. The academy should review and confirm its full policies before a public launch.'],
];
foreach($pages as $slug=>$p) seed_record('pages',$slug,$p[0],['eyebrow'=>$p[1],'heading'=>str_replace('\\n',"\n",$p[2]),'intro'=>$p[3],'body'=>str_replace('\\n',"\n",$p[4]),'seo_title'=>$p[0].' | Abhishek Rana Shooting Academy','seo_description'=>$p[3]]);
$programs=[
 ['air-rifle','10m Air Rifle','PRECISION & POISE','Foundation to development','A discipline of balance, stillness and repeatable technique. Discover the details that make every shot count.','Stance & stability\nSight alignment\nConsistency & review','01'],
 ['air-pistol','10m Air Pistol','CONTROL & COMPOSURE','Foundation to development','Find the connection between a steady mind and a deliberate shot. Explore a rewarding precision discipline.','Balance & posture\nFocus & trigger control\nPurposeful practice','02'],
 ['foundation','Start with the fundamentals','YOUR FIRST EXPERIENCE','First-time participants','New to shooting? Start with a guided introduction and a conversation about your goals.','Range etiquette & safety\nDiscipline introduction\nYour next steps','03'],
];
foreach($programs as $i=>$p) seed_record('programs',$p[0],$p[1],['category'=>$p[2],'level'=>$p[3],'summary'=>$p[4],'body'=>$p[4]."\n\nThis is an introductory pathway outline. The academy will confirm the program structure, availability, equipment arrangements and eligibility during your enquiry.",'features'=>str_replace('\\n',"\n",$p[5]),'duration'=>'Schedule confirmed after enquiry','eligibility'=>'Discuss age and experience requirements with the academy','fees'=>'Contact the academy for the current fee plan'], 'published', $i);
for($i=1;$i<=6;$i++) seed_record('branches','branch-'.$i,'Branch '.$i.' — details pending',['city'=>'Location to be confirmed','address'=>'','body'=>'Add the confirmed branch name, address, facilities, contact details and directions before publishing.'],'draft',$i);
seed_record('coaches','head-coach','Coach profile — pending',['role'=>'Head coach','credentials'=>'Add verified qualifications','body'=>'Replace this draft with the approved coach biography.'],'draft');
seed_record('achievements','first-result','Achievement — pending verification',['athlete'=>'','event'=>'','result'=>'','body'=>'Add the athlete’s approved name, event and verified result before publishing.'],'draft');
seed_record('competitions','welcome','Your shooting journey starts here',['category'=>'ACADEMY UPDATE','summary'=>'Explore our training pathways and request a demo to find your starting point.','body'=>'Share your preferred discipline and location through our demo request form. Our team will confirm availability and guide you through the next steps.']);
$faqs=[
 ['Do I need experience to request a demo?','You can enquire as a complete beginner. Share your experience level so the academy can suggest a suitable introduction.'],
 ['What should I bring to my first session?','Wait for the academy to confirm your booking and explain clothing, equipment and any documents required before you travel.'],
 ['How do I find the right program?','Tell us your experience, preferred discipline and goals. The academy will discuss suitable options and confirm program availability and fees.'],
 ['Can I enquire for my child?','Yes. A parent or guardian should complete the form with their own contact details. The academy will confirm age requirements and consent arrangements.'],
 ['Does a demo request confirm my admission?','No. The team first confirms demo availability. Admission is a separate process and every new student requires owner approval.'],
];
foreach($faqs as $i=>$faq) seed_record('faqs','question-'.($i+1),$faq[0],['answer'=>$faq[1]],'published',$i);
 db()->commit();
require_once __DIR__.'/apply-website-defaults.php';
apply_website_defaults();
require_once __DIR__.'/sporting-update.php';
apply_sporting_update();
require_once __DIR__.'/research-update.php';
apply_research_update();
