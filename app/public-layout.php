<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=e($metaTitle)?></title><meta name="description" content="<?=e($metaDescription)?>">
 <link rel="canonical" href="<?=e($canonical)?>">
 <meta property="og:title" content="<?=e($metaTitle)?>"><meta property="og:description" content="<?=e($metaDescription)?>"><meta property="og:type" content="website">
 <?php if(image_path($home['hero_image'] ?? '')): ?><meta property="og:image" content="<?=e(rtrim(env('APP_URL'),'/').$home['hero_image'])?>"><?php endif ?>
 <?php if(env('APP_ENV')!=='production' || $preview): ?><meta name="robots" content="noindex,nofollow"><?php endif ?>
 <link rel="icon" href="/favicon.ico?v=2" sizes="any"><link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png?v=2"><link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png?v=2">
 <link rel="stylesheet" href="/assets/css/fonts.css"><link rel="stylesheet" href="/assets/css/site.css?v=6"><script src="/assets/js/site.js?v=3" defer></script>
</head>
<body>
<a href="#main" class="skip-link">Skip to content</a>
<?php if($preview): ?><div class="preview-banner">CMS preview · Draft content is included. <a href="/superadmin/cms">Back to CMS</a></div><?php endif ?>
<div class="utility"><div class="container"><span><?=icon('target')?><?=e($site['tagline'])?></span><div><a href="/events">Event calendar</a><a href="/achievements">Achievements</a><a href="/superadmin/login">Academy login <?=icon('external')?></a></div></div></div>
<header class="site-header"><div class="container nav-wrap">
 <a class="brand" href="/" aria-label="<?=e($site['name'])?> home">
 <?php if(image_path($site['logo'] ?? '')): ?><img class="brand-logo" src="<?=e($site['logo'])?>" alt="<?=e($site['name'])?>"><span><strong><?=e(preg_replace('/ Shooting Academy$/i','',$site['name']))?></strong><small>SHOOTING ACADEMY</small></span><?php else: ?><img src="/assets/images/mark.svg" width="46" height="46" alt=""><span><strong><?=e(preg_replace('/ Shooting Academy$/i','',$site['name']))?></strong><small><?=e(str_ends_with(strtolower($site['name']),'shooting academy')?'SHOOTING ACADEMY':$site['tagline'])?></small></span><?php endif ?>
 </a>
 <button class="nav-toggle icon-button" aria-label="Open navigation" aria-expanded="false" aria-controls="main-nav"><?=icon('menu')?></button>
 <nav id="main-nav" aria-label="Main navigation">
 <?php foreach([''=>'Home','about'=>'About us','programs'=>'Training','gallery'=>'Gallery','branches'=>'Branches'] as $slug=>$label): ?>
 <a href="/<?=$slug?>" <?=($pageSlug===$slug || ($slug==='' && $pageSlug==='home'))?'aria-current="page"':''?>><?=$label?></a>
 <?php endforeach ?><a class="contact-nav" href="/contact" <?=$pageSlug==='contact'?'aria-current="page"':''?>>Contact us</a>
 <a class="button button-small" href="/demo-class">Book a demo <?=icon('arrow')?></a>
 </nav>
</div></header>
<main id="main">
<?php if($notFound): ?>
 <section class="page-hero"><div class="container"><span class="eyebrow">Page not found</span><h1>Let’s get you back on track.</h1><p>We couldn’t find this page. Explore the academy or contact us for help.</p><a class="button" href="/">Back to home <?=icon('arrow')?></a></div></section>
<?php elseif($pageSlug==='home'): require __DIR__.'/home.php'; ?>
<?php else: ?>
 <section class="page-hero"><div class="container"><div class="breadcrumb"><a href="/">Home</a><span>/</span><?php if($detail): ?><a href="/<?=e($pageSlug)?>"><?=e($page['title'])?></a><span>/</span><?php endif ?><span><?=e($detail['title'] ?? $page['title'])?></span></div><span class="eyebrow"><?=e($detail ? field($detail,'category',$page['title']) : field($page,'eyebrow'))?></span><h1><?=page_heading($detail['title'] ?? field($page,'heading',$page['title']))?></h1><p><?=e($detail ? field($detail,'summary',field($detail,'role')) : field($page,'intro'))?></p></div><div class="page-target" aria-hidden="true"></div></section>
 <section class="section inner-content"><div class="container">
 <?php if($detail): require __DIR__.'/detail.php'; ?>
 <?php elseif(in_array($pageSlug,['admission','demo-class','contact'],true)): require __DIR__.'/form.php'; ?>
 <?php else: ?>
 <?php if(field($page,'body')): ?><div class="prose page-intro"><?=paragraphs(field($page,'body'))?></div><?php endif ?>
 <?php if(image_path(field($page,'image'))): ?><img class="page-feature-image" src="<?=e(field($page,'image'))?>" alt="<?=e($page['title'])?>"><?php endif ?>
 <?php if($pageSlug==='photo-credits'): ?>
 <div class="credits-list"><?php foreach(reference_photos() as $photo): ?><article class="credit-item"><img src="<?=e($photo['path'])?>" alt="<?=e($photo['alt'])?>" loading="lazy"><div><h2><?=e($photo['title'])?></h2><p><?=e($photo['alt'])?></p><p>Photo: <?=e($photo['author'])?></p><p><a href="<?=e($photo['source'])?>" target="_blank" rel="noopener noreferrer">Original photograph</a> · <a href="<?=e($photo['license_url'])?>" target="_blank" rel="noopener noreferrer"><?=e($photo['license'])?></a> · <a href="<?=e($photo['path'])?>" target="_blank" rel="noopener">WebP derivative</a></p><p>Resized and converted to WebP; display may be cropped. Academy branding remains the property of its owner; licensed reference photos retain their source terms.</p></div></article><?php endforeach ?></div>
 <?php elseif($pageSlug==='about'): ?>
 <div class="values-grid"><?php foreach([['shield','Safety comes first','Respect for the range, the equipment and each other is the starting point.'],['target','A purposeful practice','Build the fundamentals with patience, consistency and a clear sense of direction.'],['users','A personal journey','Different starting points. Different ambitions. A shared commitment to improvement.']] as $value): ?><article><?=icon($value[0])?><h3><?=e($value[1])?></h3><p><?=e($value[2])?></p></article><?php endforeach ?></div>
 <div class="inline-cta"><div><span class="eyebrow">Explore the academy</span><h2>Meet the team. Find your branch.</h2></div><a class="text-link" href="/coaches">Meet the team <?=icon('arrow')?></a><a class="text-link" href="/branches">Explore branches <?=icon('arrow')?></a></div>
 <?php elseif($pageSlug==='programs'): require __DIR__.'/program-cards.php'; ?>
 <?php elseif($pageSlug==='branches'): ?>
 <?php $items=public_records('branches'); if($items): ?><div class="listing-grid"><?php foreach($items as $item): ?><article class="listing-card"><span class="eyebrow"><?=e(field($item,'city'))?></span><h2><?=e($item['title'])?></h2><p><?=nl2br(e(field($item,'address')))?></p><a href="<?=detail_link($item)?>" class="text-link">Explore branch <?=icon('arrow')?></a></article><?php endforeach ?></div><?php else: ?><div class="location-empty"><div class="target-art" aria-hidden="true"><i></i></div><div><span class="eyebrow">LET’S FIND YOUR NEAREST RANGE</span><h2>Tell us where you’d like to train</h2><p>Our branch directory is being prepared. Share your preferred city or area and we’ll help you find a suitable location.</p><a class="button" href="/contact">Find my nearest branch <?=icon('arrow')?></a></div></div><?php endif ?>
 <?php elseif($pageSlug==='coaches'): ?>
 <?php $items=public_records('coaches'); if($items): ?><div class="listing-grid"><?php foreach($items as $item): ?><article class="listing-card"><?php if(image_path(field($item,'image'))): ?><img class="card-image" src="<?=e(field($item,'image'))?>" alt="<?=e($item['title'])?>"><?php endif ?><span class="eyebrow"><?=e(field($item,'role'))?></span><h2><?=e($item['title'])?></h2><p><?=e(field($item,'credentials'))?></p><a class="text-link" href="<?=detail_link($item)?>">Meet your coach <?=icon('arrow')?></a></article><?php endforeach ?></div><?php else: ?><div class="empty-public"><?=icon('users')?><h2>Speak with the academy</h2><p>Official team profiles will be shared here soon.</p><a class="button" href="/contact">Talk to the academy <?=icon('arrow')?></a></div><?php endif ?>
 <?php elseif($pageSlug==='achievements'): ?>
 <?php $items=public_records('achievements'); if($items): ?><div class="listing-grid"><?php foreach($items as $item): ?><article class="listing-card"><?=icon('medal')?><span class="eyebrow"><?=e(field($item,'result'))?></span><h2><?=e($item['title'])?></h2><p><?=e(field($item,'athlete'))?> · <?=e(field($item,'event'))?></p><a class="text-link" href="<?=detail_link($item)?>">Read the story <?=icon('arrow')?></a></article><?php endforeach ?></div><?php else: ?><div class="empty-public"><?=icon('medal')?><h2>Follow our athletes’ progress</h2><p>Our verified results and athlete stories are being prepared. Come back to follow the journey.</p><a class="text-link" href="/gallery">Inside the academy <?=icon('arrow')?></a></div><?php endif ?>
 <?php elseif($pageSlug==='gallery'): ?>
 <?php $items=public_records('gallery'); if($items): ?><div class="gallery-grid"><?php foreach($items as $item): ?><figure><?php if(image_path(field($item,'image'))): ?><a href="<?=e(field($item,'image'))?>" target="_blank" rel="noopener"><img src="<?=e(field($item,'image'))?>" alt="<?=e(field($item,'caption',$item['title']))?>" loading="lazy"></a><?php endif ?><figcaption><strong><?=e($item['title'])?></strong><p><?=e(field($item,'caption'))?></p><?php if(safe_link(field($item,'video_url'))): ?><a class="text-link" href="<?=e(safe_link(field($item,'video_url')))?>" target="_blank" rel="noopener noreferrer">Watch video <?=icon('external')?></a><?php endif ?></figcaption></figure><?php endforeach ?></div><?php else: ?><div class="gallery-placeholder"><img src="<?=e(image_path($home['hero_image'] ?? '') ?: '/assets/images/target.svg')?>" alt="Shooting-sport reference photograph"><div><span class="eyebrow">Explore shooting</span><h2>See the sport up close</h2><p>Real sports reference photography. Our own academy photographs will be added soon.</p><a class="text-link" href="/demo-class">Experience it for yourself <?=icon('arrow')?></a></div></div><?php endif ?>
 <?php elseif($pageSlug==='events'): require __DIR__.'/event-calendar.php'; ?>
 <?php elseif($pageSlug==='competitions'): ?>
 <div class="notices-list"><?php foreach(public_records('competitions') as $item): ?><a href="<?=detail_link($item)?>" class="notice-row"><span class="notice-date"><?=field($item,'date')?format_date(field($item,'date'),'d M'):'NEWS'?></span><div><span class="eyebrow"><?=e(field($item,'category'))?></span><h3><?=e($item['title'])?></h3><p><?=e(field($item,'summary'))?></p></div><?=icon('arrow')?></a><?php endforeach ?></div><div class="resource-callout"><?=icon('external')?><p>For official national competition notices, eligibility and registration, visit the <a href="https://www.thenrai.in/" target="_blank" rel="noopener noreferrer">National Rifle Association of India</a>.</p></div>
 <?php endif ?>
 <?php endif ?>
 </div></section>
 <?php if(!in_array($pageSlug,['privacy','terms','demo-class','admission','contact'],true)): require __DIR__.'/closing-cta.php'; endif ?>
<?php endif ?>
</main>
<footer class="site-footer"><div class="container"><div class="footer-main"><div class="footer-brand"><a class="brand" href="/"><img src="/assets/images/mark-light.svg" alt="" width="46" height="46"><span><strong><?=e($site['name'])?></strong><small><?=e($site['tagline'])?></small></span></a><p><?=e($site['footer_text'])?></p><div class="social-links"><?php foreach(['instagram'=>'Instagram','facebook'=>'Facebook','youtube'=>'YouTube'] as $key=>$label): if(safe_link($site[$key]??'')): ?><a href="<?=e(safe_link($site[$key]))?>" target="_blank" rel="noopener noreferrer"><?=$label?> <?=icon('external')?></a><?php endif; endforeach ?></div></div><div><h3>The academy</h3><a href="/about">Our story</a><a href="/coaches">Coaches & team</a><a href="/branches">Our branches</a><a href="/gallery">Gallery</a></div><div><h3>Your journey</h3><a href="/programs">Training programs</a><a href="/demo-class">Request a demo</a><a href="/admission">Admission enquiry</a><a href="/competitions">Competitions & notices</a></div><div><h3>Let’s connect</h3><?php if($site['phone']): ?><a href="tel:<?=e(preg_replace('/[^+0-9]/','',$site['phone']))?>"><?=e($site['phone'])?></a><?php endif ?><?php if($site['email']): ?><a href="mailto:<?=e($site['email'])?>"><?=e($site['email'])?></a><?php endif ?><a href="/contact">Contact the academy <?=icon('arrow')?></a><a href="/superadmin/login">Academy login</a></div></div>
<?php if($site['illustration_notice']??''): ?><p class="illustration-notice"><?=e($site['illustration_notice'])?> <a href="/photo-credits">Photo credits & licences</a></p><?php endif ?>
<div class="footer-bottom"><span>© <?=date('Y')?> <?=e($site['name'])?></span><div><a href="/privacy">Privacy</a><a href="/terms">Terms</a><a href="/photo-credits">Photo credits</a><a class="agency-credit" href="https://www.itsoftexpert.com/" target="_blank" rel="noopener noreferrer">Developed by ITSoftExpert</a></div></div></div></footer>
<button type="button" class="back-top" aria-label="Back to top" tabindex="-1" hidden>↑</button>
<div class="mobile-action"><a href="/demo-class">Request a demo <?=icon('arrow')?></a><a href="/contact">Enquire now</a></div>
</body></html>
