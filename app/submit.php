<?php
$formValues = array_map(fn($v)=>is_string($v)?trim($v):'',$_POST);
foreach(['name'=>120,'email'=>180,'phone'=>30,'branch'=>180,'program'=>180,'experience'=>80,'message'=>4000,'preferred_date'=>10,'preferred_time'=>30] as $k=>$max) {
    if (mb_strlen($formValues[$k] ?? '')>$max) $formErrors[$k]='Please shorten this field.';
}
if (!valid_csrf()) $formErrors['general']='Your session expired. Please submit the form again.';
if (!empty($formValues['website']) || (int)($formValues['started'] ?? 0)>time()-2) $formErrors['general']='Please take a moment to check the form and try again.';
if (mb_strlen($formValues['name'] ?? '')<2) $formErrors['name']='Please enter your name.';
if (!filter_var($formValues['email'] ?? '',FILTER_VALIDATE_EMAIL)) $formErrors['email']='Enter a valid email address.';
if (!preg_match('/^[+()\d\s-]{7,25}$/',$formValues['phone'] ?? '')) $formErrors['phone']='Enter a valid contact number.';
if (empty($formValues['consent'])) $formErrors['consent']='Please agree so we can respond to your request.';
if (!in_array($formValues['experience'] ?? '',['First time','Some experience','Regular shooter','Enquiring for my child'],true)) $formErrors['experience']='Please select your experience level.';
$validBranches = array_column(records('branches'),'title'); $validBranches[]='Help me find a branch';
if (!in_array($formValues['branch'] ?? '',$validBranches,true)) $formErrors['branch']='Choose an available branch option.';
$validPrograms = array_column(records('programs'),'title'); $validPrograms[]='Help me choose';
if (!in_array($formValues['program'] ?? '',$validPrograms,true)) $formErrors['program']='Choose an available program.';
$isDemo=$pageSlug==='demo-class';
if ($isDemo) {
    $date=DateTimeImmutable::createFromFormat('!Y-m-d',$formValues['preferred_date'] ?? '');
    if (!$date || $date->format('Y-m-d')!==($formValues['preferred_date'] ?? '') || $date<new DateTimeImmutable('today') || $date>new DateTimeImmutable('+6 months')) $formErrors['preferred_date']='Choose a date between today and six months from now.';
    if (!in_array($formValues['preferred_time'] ?? '',['Morning','Afternoon','Evening','Flexible'],true)) $formErrors['preferred_time']='Choose your preferred time.';
}
if (!$formErrors && limited('form:'.($_SERVER['REMOTE_ADDR'] ?? ''),5,900)) $formErrors['general']='You’ve sent several requests. Please wait 15 minutes before trying again.';
if (!$formErrors) {
    $reference=($isDemo?'DEMO':'ENQ').'-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
    $delivery=[];
    $recipients = array_values(array_filter(array_map('trim',explode(',',env('ENQUIRY_RECIPIENTS'))),fn($v)=>filter_var($v,FILTER_VALIDATE_EMAIL)));
    if (!$recipients) $delivery[]=['recipient'=>'Academy notifications','status'=>'Not configured','detail'=>'Saved to the inbox. Email recipients have not been configured.'];
    foreach($recipients as $recipient) {
        $sent=false; $enabled=env('MAIL_ENABLED')==='true' && filter_var(env('MAIL_FROM'),FILTER_VALIDATE_EMAIL);
        if ($enabled) $sent=@mail($recipient,'New academy request '.$reference,"A new ".($isDemo?'demo':'enquiry')." request has been received.\nReference: ".$reference."\nOpen the secure academy inbox to view and respond.\n".rtrim(env('APP_URL'),'/').'/superadmin/'.($isDemo?'demos':'enquiries'),'From: '.env('MAIL_FROM')."\r\nContent-Type: text/plain; charset=UTF-8");
        $delivery[]=['recipient'=>$recipient,'status'=>$sent?'Accepted by mail server':($enabled?'Failed':'Disabled'),'detail'=>$sent?'Mail server accepted the notification; final delivery is not verified.':($enabled?'The mail server did not accept this notification.':'Email sending is disabled; request is saved in the inbox.')];
    }
    query('INSERT INTO enquiries(reference,kind,name,email,phone,branch,program,experience,message,preferred_date,preferred_time,delivery,consent_at,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[
      $reference,$isDemo?'demo':'enquiry',$formValues['name'],$formValues['email'],$formValues['phone'],$formValues['branch'],$formValues['program'],$formValues['experience'],$formValues['message'] ?? '',$isDemo?$formValues['preferred_date']:'',$isDemo?$formValues['preferred_time']:'',json_encode($delivery),now(),now()
    ]);
    $_SESSION['confirmation']=['reference'=>$reference,'demo'=>$isDemo];
    redirect('/'.$pageSlug.'?submitted=1');
}
