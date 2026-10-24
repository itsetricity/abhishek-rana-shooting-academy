<?php
$site = settings();
$home = array_replace(home_defaults(),settings('home'));
$preview = isset($_GET['preview']) && is_admin();
$route = trim($path,'/');
$segments = explode('/',$route);
$detailTypes = ['programs','branches','coaches','achievements','competitions','events'];
$detail = null;
$pageSlug = $segments[0] ?: 'home';
if (count($segments) === 2 && in_array($segments[0],$detailTypes,true)) $detail = record($segments[0],$segments[1],$preview);
$page = $pageSlug === 'home' ? null : record('pages',$pageSlug,$preview);
$notFound = ($pageSlug !== 'home' && !$page) || count($segments)>2 || (count($segments) === 2 && !$detail);
if ($notFound) http_response_code(404);
$formErrors=[]; $formValues=[];
if ($_SERVER['REQUEST_METHOD']==='POST' && in_array($pageSlug,['admission','demo-class','contact'],true) && !$notFound) {
    require __DIR__.'/submit.php';
}
if ($path === '/robots.txt') {
    http_response_code(200);
    header('Content-Type: text/plain'); echo "User-agent: *\nDisallow: /superadmin\n";
    if (env('APP_ENV') !== 'production') echo "Disallow: /\n";
    else echo 'Sitemap: '.rtrim(env('APP_URL'),'/')."/sitemap.xml\n";
    exit;
}
if ($path === '/sitemap.xml') {
    http_response_code(200);
    header('Content-Type: application/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $urls=['/']; foreach(records('pages') as $p) $urls[]='/'.$p['slug'];
    foreach($detailTypes as $type) foreach(records($type) as $p) $urls[]='/'.$type.'/'.$p['slug'];
    foreach($urls as $url) echo '<url><loc>'.e(rtrim(env('APP_URL'),'/').$url).'</loc></url>';
    echo '</urlset>'; exit;
}
$metaTitle = $notFound ? 'Page not found | '.$site['name'] : ($detail ? $detail['title'].' | '.$site['name'] : field($page ?? [],'seo_title',$site['seo_title']));
$metaDescription = $detail ? field($detail,'seo_description',field($detail,'summary',$site['seo_description'])) : field($page ?? [],'seo_description',$site['seo_description']);
$canonical = rtrim(env('APP_URL'),'/').$path;
function public_records(string $type): array { global $preview; return records($type,$preview); }
function page_heading(string $text): string { return nl2br(e(str_replace('|',"\n",$text))); }
function detail_link(array $r): string { return '/'.e($r['type']).'/'.e($r['slug']); }
require __DIR__.'/public-layout.php';
