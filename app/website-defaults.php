<?php
// Shared defaults for new installations and the intentional public-site redesign.
function home_defaults(): array {
    return [
        'eyebrow'=>'Welcome to Abhishek Rana Shooting Academy',
        'heading'=>'Learn shooting.', 'accent'=>'Aim for more.',
        'intro'=>'Discover 10m air rifle and air pistol shooting. Start as a beginner, build your skills or prepare for your next competition.',
        'hero_image'=>'/assets/images/pistol-training.webp',
        'image_caption'=>'Real shooting-sport photography · Reference image',
        'cta_label'=>'Book a demo class','cta_url'=>'/demo-class',
        'secondary_label'=>'Explore training','secondary_url'=>'/programs',
        'hero_form_title'=>'Not sure where to start?',
        'hero_form_text'=>'Leave your details. We’ll help you choose the right next step.',
        'hero_form_button'=>'Request a callback',
        'program_heading'=>'Find the right training for you',
        'program_intro'=>'Get to know the disciplines, see what you’ll learn and choose where to begin.',
        'about_eyebrow'=>'About the academy',
        'about_heading'=>'Good habits. Better shooting.',
        'about_body'=>"Shooting is a sport of focus, control and steady improvement. Our approach starts with understanding your goals and building the basics.\n\nAt Abhishek Rana Shooting Academy, your next step begins with a conversation. Explore a discipline, visit the range and discuss a training plan that works for you.",
        'about_image'=>'/assets/images/range-practice.webp',
        'about_points'=>"Start with safety|Understand range rules before you begin.\nBuild a strong foundation|Learn posture, aiming and shot consistency.\nProgress at your pace|Discuss your goals and the right training level.",
        'journey_heading'=>'Getting started is simple',
        'journey_intro'=>'Three easy steps from your first enquiry to your first training session.',
        'step1_title'=>'Tell us about yourself','step1_body'=>'Share your experience, preferred location and what you want to learn.',
        'step2_title'=>'Visit for a demo','step2_body'=>'We’ll confirm a suitable session so you can explore the sport and ask questions.',
        'step3_title'=>'Choose your training','step3_body'=>'Discuss the program, fees and schedule before you decide to enrol.',
        'gallery_heading'=>'Take a closer look at shooting',
        'gallery_intro'=>'Real moments from the sport. Our own academy photos will be added here soon.',
        'branch_heading'=>'Let’s find a range near you',
        'branch_intro'=>'Tell us your city or preferred area. We’ll help you find the right branch and confirm its address, timings and available training.',
        'branch_image'=>'/assets/images/indoor-range.webp',
        'faq_heading'=>'Questions before you join?',
        'faq_intro'=>'Here are a few answers to help you plan your first visit.',
        'cta_heading'=>'Ready to try shooting?',
        'cta_body'=>'Book a demo class or send us your questions. We’ll help you get started.',
    ];
}
function redesign_site_defaults(): array {
    return [
        'tagline'=>'Learn. Practise. Improve.',
        'seo_title'=>'Abhishek Rana Shooting Academy | Air Rifle & Air Pistol Training',
        'seo_description'=>'Explore 10m air rifle and air pistol training at Abhishek Rana Shooting Academy. Find a program, ask about a branch and request a demo class.',
        'footer_text'=>'Air rifle and air pistol shooting, with a clear path from your first visit to your next training goal.',
        'illustration_notice'=>'Reference sports photography is used until our own academy images are available. Photos do not imply academy affiliation or endorsement.',
    ];
}
function redesign_pages(): array {
    return [
        'about'=>['About the academy','About us','A clear start. Room to grow.','Get to know Abhishek Rana Shooting Academy and our approach to learning the sport.',"Our aim is to make shooting easier to understand and more approachable. Whether you are trying it for the first time or looking to improve, start by telling us about your goals.\n\nExplore 10m air rifle and air pistol training, ask about your nearest branch and request a demo before choosing a program. The academy will confirm training availability, equipment, fees and schedules with you.\n\nOur approach puts range safety, strong fundamentals and consistent practice first. Approved coach profiles and academy achievements will be added as they are confirmed."],
        'programs'=>['Training programs','Our training','Choose your discipline. Find your level.','Explore air rifle, air pistol and a beginner introduction to shooting.',''],
        'branches'=>['Our branches','Find a branch','Your training. Closer to you.','Find out about locations, facilities and training availability.',''],
        'coaches'=>['Coaches & team','Our people','Meet your coaching team','Find the right guidance for your shooting goals.','We’re preparing our approved coach profiles. Contact the academy to discuss coaching for your preferred discipline.'],
        'achievements'=>['Achievements','Academy achievements','Every improvement matters','Follow our athletes’ progress and verified competition results.','Approved results and athlete stories will be added here.'],
        'gallery'=>['Photo gallery','Explore the sport','Shooting, up close','A look at air rifle, air pistol and life on a shooting range.','These are licensed reference photographs of the sport. They do not show our academy’s students, team or facilities.'],
        'competitions'=>['News & competitions','Latest updates','Stay up to date','Academy notices, competition information and useful official links.','Check the organiser’s official notice for eligibility, dates and registration details.'],
        'contact'=>['Contact us','We’re here to help','Let’s talk about your training','Ask about a program, fees, timings or your nearest branch.',''],
        'admission'=>['Enquire about training','Make an enquiry','How can we help you?','Leave your details and the academy will follow up about your next step.',''],
        'demo-class'=>['Book a demo class','Try the sport','Your first session starts here','Tell us when you’d like to visit. We’ll get back to confirm a suitable demo session.',''],
        'photo-credits'=>['Photo credits','Reference photography','The people behind the photographs','Real sports photography, shared under Creative Commons licences.','The photos below are reference images of shooting sport, not academy students or facilities. No endorsement or academy affiliation is implied. Images have been resized and converted to WebP; the website may crop their display. These image derivatives are shared under their respective source licences.'],
    ];
}
function reference_photos(): array {
    return [
        ['path'=>'/assets/images/pistol-training.webp','alt'=>'Jin Jongoh competing in air pistol at the London 2012 Olympic Games','title'=>'Air pistol in competition','author'=>'Korea.net / Korean Culture and Information Service (Korean Olympic Committee)','source'=>'https://commons.wikimedia.org/wiki/File:KOCIS_London_Korea_Jinjongoh_Shooting_12_(7683324116).jpg','license'=>'CC BY-SA 2.0','license_url'=>'https://creativecommons.org/licenses/by-sa/2.0/'],
        ['path'=>'/assets/images/rifle-training.webp','alt'=>'Jennifer McIntosh with a precision air rifle at the 2015 International Shooting Competition of Hannover','title'=>'Precision air rifle','author'=>'Hemmers','source'=>'https://commons.wikimedia.org/wiki/File:001_Jennifer_McIntosh_at_ISCH_2015.jpg','license'=>'CC BY-SA 4.0','license_url'=>'https://creativecommons.org/licenses/by-sa/4.0/'],
        ['path'=>'/assets/images/indoor-range.webp','alt'=>'The 10 metre Gagan Narang shooting range at Kalinga Stadium, Bhubaneswar','title'=>'Inside a 10m shooting range','author'=>'Subhrasingh','source'=>'https://commons.wikimedia.org/wiki/File:Gagan_Narang_Shooting_Range,_Kalinga_Stadium.jpg','license'=>'CC BY-SA 4.0','license_url'=>'https://creativecommons.org/licenses/by-sa/4.0/'],
        ['path'=>'/assets/images/range-practice.webp','alt'=>'Sport shooters practising 10 metre air rifle at an indoor range','title'=>'Air rifle practice on the range','author'=>'Wittkowsky; colour balance by Hohum','source'=>'https://commons.wikimedia.org/wiki/File:Air-rifle-shooting.jpg','license'=>'CC BY-SA 3.0','license_url'=>'https://creativecommons.org/licenses/by-sa/3.0/'],
    ];
}
function reference_photo(string $path): ?array {
    foreach(reference_photos() as $photo) if($photo['path']===$path) return $photo;
    return null;
}
function photo_alt(string $path,string $fallback='Shooting sport'): string {
    $reference=reference_photo($path);
    if($reference) return $reference['alt'];
    $description=query('SELECT alt FROM media WHERE path=?',[$path])->fetchColumn();
    return $description ? (string)$description : $fallback;
}
