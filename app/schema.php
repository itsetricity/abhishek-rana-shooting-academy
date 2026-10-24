<?php
function content_types(): array {
    return [
      'pages'=>['label'=>'Pages','singular'=>'Page','icon'=>'page','description'=>'Editorial pages, privacy information and search metadata.','fields'=>[
        'eyebrow'=>['Eyebrow','text'],'heading'=>['Page heading','text'],'intro'=>['Introduction','textarea'],'body'=>['Page content (blank lines separate paragraphs)','textarea'],'image'=>['Feature image','image'],'seo_title'=>['SEO title','text'],'seo_description'=>['SEO description','textarea']]],
      'programs'=>['label'=>'Training programs','singular'=>'Program','icon'=>'target','description'=>'Help visitors choose a discipline and the right starting point.','fields'=>[
        'category'=>['Discipline / category','text'],'level'=>['Experience level','text'],'summary'=>['Short description','textarea'],'body'=>['Full program details','textarea'],'duration'=>['Format / schedule','text'],'eligibility'=>['Eligibility','text'],'fees'=>['Fee information','text'],'features'=>['What you will learn (one per line)','textarea'],'image'=>['Program image','image'],'seo_description'=>['SEO description','textarea']]],
      'branches'=>['label'=>'Branches','singular'=>'Branch','icon'=>'pin','description'=>'Publish confirmed locations. Add as many branches as you need.','fields'=>[
        'city'=>['City / region','text'],'address'=>['Full address','textarea'],'phone'=>['Phone number','tel'],'email'=>['Branch email','email'],'hours'=>['Opening hours','text'],'facilities'=>['Facilities (one per line)','textarea'],'body'=>['About this branch','textarea'],'map_url'=>['Google Maps directions URL','url'],'image'=>['Branch image','image'],'seo_description'=>['SEO description','textarea']]],
      'coaches'=>['label'=>'Coaches & team','singular'=>'Coach','icon'=>'users','description'=>'Only publish verified profiles and qualifications.','fields'=>[
        'role'=>['Role / discipline','text'],'credentials'=>['Verified qualifications','text'],'branch'=>['Branch / location','text'],'body'=>['Biography','textarea'],'image'=>['Portrait','image']]],
      'achievements'=>['label'=>'Achievements','singular'=>'Achievement','icon'=>'medal','description'=>'Celebrate verified athlete results. Draft examples are not visible publicly.','fields'=>[
        'athlete'=>['Athlete name','text'],'event'=>['Competition / event','text'],'date'=>['Achievement date','date'],'result'=>['Result / medal','text'],'source_url'=>['Verified result source URL','url'],'body'=>['Story','textarea'],'image'=>['Photo','image']]],
      'gallery'=>['label'=>'Gallery','singular'=>'Gallery item','icon'=>'image','description'=>'Academy moments, training photographs and approved athlete imagery.','fields'=>[
        'category'=>['Category','text'],'caption'=>['Caption / image description','textarea'],'image'=>['Photograph','image'],'video_url'=>['Optional external video URL','url']]],
      'competitions'=>['label'=>'Competitions & notices','singular'=>'Notice','icon'=>'calendar','description'=>'Publish academy updates, event dates and verified official entry links.','fields'=>[
        'category'=>['Category','text'],'date'=>['Date','date'],'location'=>['Location','text'],'summary'=>['Short description','textarea'],'body'=>['Details / eligibility','textarea'],'registration_url'=>['Official registration / information URL','url']]],
      'events'=>['label'=>'Event calendar','singular'=>'Event','icon'=>'calendar','description'=>'Dates appear on the homepage calendar. Leave the date blank for a date-to-be-announced session.','fields'=>[
        'category'=>['Event category','text'],'date'=>['Event date (blank = to be announced)','date'],'time'=>['Time / session information','text'],'location'=>['Location','text'],'summary'=>['Short description','textarea'],'body'=>['Full details','textarea'],'registration_url'=>['Official information / registration URL','url']]],
      'faqs'=>['label'=>'FAQs','singular'=>'Question','icon'=>'page','description'=>'Answer the questions that matter before a first visit.','fields'=>[
        'answer'=>['Answer','textarea']]],
    ];
}
function setting_fields(string $group): array {
    return $group === 'home' ? [
      'eyebrow'=>['Hero eyebrow','text'],'heading'=>['Hero heading (use | for a new line)','text'],'accent'=>['Hero accent line','text'],'intro'=>['Hero introduction','textarea'],'hero_image'=>['Hero image','image'],'image_caption'=>['Hero image credit / disclosure','text'],
      'cta_label'=>['Primary button label','text'],'cta_url'=>['Primary button link','text'],'secondary_label'=>['Secondary button label','text'],'secondary_url'=>['Secondary button link','text'],
      'hero_form_title'=>['Quick enquiry heading','text'],'hero_form_text'=>['Quick enquiry introduction','textarea'],'hero_form_button'=>['Quick enquiry button label','text'],
      'usp_eyebrow'=>['Olympic story eyebrow','text'],'usp_heading'=>['Olympic story heading','text'],'usp_body'=>['Olympic coaching story','textarea'],'usp_image'=>['Olympic story photograph','image'],'usp_source'=>['Official source URL','url'],'usp_video'=>['Coach interview URL','url'],
      'events_heading'=>['Calendar heading','text'],'events_intro'=>['Calendar introduction','textarea'],'achievements_heading'=>['Achievements heading','text'],'achievements_intro'=>['Achievements introduction','textarea'],
      'about_eyebrow'=>['About eyebrow','text'],'about_heading'=>['About heading','text'],'about_body'=>['About text','textarea'],'about_image'=>['About image','image'],
      'about_points'=>['About benefits (one per line: heading | description)','textarea'],
      'program_heading'=>['Programs section heading','text'],'program_intro'=>['Programs introduction','textarea'],'journey_heading'=>['Training journey heading','text'],'journey_intro'=>['Training journey introduction','textarea'],
      'step1_title'=>['Journey step 1 title','text'],'step1_body'=>['Journey step 1 text','textarea'],'step2_title'=>['Journey step 2 title','text'],'step2_body'=>['Journey step 2 text','textarea'],'step3_title'=>['Journey step 3 title','text'],'step3_body'=>['Journey step 3 text','textarea'],
      'gallery_heading'=>['Homepage gallery heading','text'],'gallery_intro'=>['Homepage gallery introduction','textarea'],
      'branch_heading'=>['Branches section heading','text'],'branch_intro'=>['Branches introduction','textarea'],'branch_image'=>['Branches section photograph','image'],
      'faq_heading'=>['FAQs section heading','text'],'faq_intro'=>['FAQs section introduction','textarea'],'cta_heading'=>['Closing call to action heading','text'],'cta_body'=>['Closing call to action text','textarea'],
    ] : [
      'name'=>['Academy name','text'],'short_name'=>['Short name','text'],'tagline'=>['Tagline','text'],'logo'=>['Official logo (leave empty for text identity)','image'],'phone'=>['Public phone number','tel'],'email'=>['Public email','email'],'address'=>['Main address','textarea'],'hours'=>['Office / enquiry hours','text'],
      'instagram'=>['Instagram URL','url'],'facebook'=>['Facebook URL','url'],'youtube'=>['YouTube URL','url'],'seo_title'=>['Default SEO title','text'],'seo_description'=>['Default SEO description','textarea'],'footer_text'=>['Footer description','textarea'],'illustration_notice'=>['Reference photo / content notice','text'],
    ];
}
