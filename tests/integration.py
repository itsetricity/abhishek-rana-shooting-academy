"""End-to-end HTTP checks against an isolated SQLite database, never the live inbox."""
from pathlib import Path
from html.parser import HTMLParser
import base64, csv, http.cookiejar, io, json, os, shutil, socket, sqlite3, subprocess, tempfile, time, urllib.request, urllib.parse, urllib.error

ROOT=Path(__file__).resolve().parent.parent
class FormParser(HTMLParser):
    def __init__(self):
        super().__init__(); self.forms=[]; self.current=None; self.tag=None; self.key=None
    def handle_starttag(self,tag,attrs):
        a=dict(attrs)
        if tag=='form': self.current={}; self.forms.append(self.current)
        if self.current is None: return
        if tag=='input' and a.get('name') and a.get('type') not in ['submit','file','checkbox']:
            self.current[a['name']]=a.get('value','')
        if tag=='textarea': self.tag='textarea'; self.key=a.get('name'); self.current[self.key]=''
        if tag=='select': self.tag='select'; self.key=a.get('name'); self.current[self.key]=''
        if tag=='option' and self.tag=='select' and 'selected' in a: self.current[self.key]=a.get('value','')
    def handle_data(self,data):
        if self.current is not None and self.tag=='textarea' and self.key: self.current[self.key]+=data
    def handle_endtag(self,tag):
        if tag=='form': self.current=None
        if tag in ['textarea','select']: self.tag=None; self.key=None

def form(html,action=None):
    parser=FormParser();parser.feed(html)
    return next(f for f in parser.forms if action is None or f.get('action')==action)

class Client:
    def __init__(self,base): self.base=base;self.opener=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    def request(self,path,data=None,headers=None):
        if isinstance(data,dict): data=urllib.parse.urlencode(data).encode()
        req=urllib.request.Request(self.base+path,data=data,headers=headers or {})
        try:
            with self.opener.open(req) as r:
                self.last_url=r.url
                return r.status,r.read().decode('utf-8',errors='replace'),r.headers
        except urllib.error.HTTPError as e: return e.code,e.read().decode('utf-8',errors='replace'),e.headers
    def get(self,path): return self.request(path)
    def post(self,path,data): return self.request(path,data)

checks=0
def check(condition,label):
    global checks
    if not condition: raise AssertionError(label)
    checks+=1; print('PASS',label)

env_values={}
for line in (ROOT/'.env').read_text().splitlines():
    if '=' in line and not line.lstrip().startswith('#'):
        k,v=line.split('=',1); env_values[k.strip()]=v.strip().strip('"')
if env_values.get('MAIL_ENABLED')=='true': raise SystemExit('Disable email sending before running integration tests.')

with tempfile.TemporaryDirectory(prefix='arsa-tests-') as tmp:
    testdb=Path(tmp)/'test.sqlite'
    with socket.socket() as s: s.bind(('127.0.0.1',0)); port=s.getsockname()[1]
    base=f'http://127.0.0.1:{port}'
    process=subprocess.Popen([os.environ.get('ARSA_PHP_BIN') or shutil.which('php'),'-S',f'127.0.0.1:{port}','router.php'],cwd=ROOT,env={**os.environ,'ARSA_DB_PATH':str(testdb)},stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
    uploads=[]
    try:
        public=Client(base); admin=Client(base)
        for _ in range(30):
            try: public.get('/');break
            except urllib.error.URLError: time.sleep(.1)
        for route in ['/','/about','/programs','/programs/air-rifle','/branches','/coaches','/achievements','/events','/events/introductory-demo','/gallery','/photo-credits','/competitions','/competitions/welcome','/admission','/demo-class','/contact','/privacy','/terms','/sitemap.xml','/robots.txt','/assets/fonts/barlow-condensed-800.ttf']:
            status,body,_=public.get(route);check(status==200 and 'Warning:' not in body and 'Fatal error' not in body,'Public route '+route)
        for route in ['/.env','/app/bootstrap.php','/storage/academy.sqlite','/docs/setup-vhost.py','/tests/integration.py','/perposal%20of%20website.pdf','/router.php']:
            check(public.get(route)[0]==403,'Private resource blocked '+route)
        check(public.get('/missing-page')[0]==404,'Unknown public page returns 404')
        check('Sign in to your academy workspace' in public.get('/superadmin')[1],'Admin requires authentication')
        login=admin.get('/superadmin/login')[1];data=form(login);data.update(username=env_values['ADMIN_USERNAME'],password=env_values['ADMIN_PASSWORD'])
        status,body,_=admin.post('/superadmin/login',data);check(status==200 and 'Your academy’s digital presence' in body,'Superadmin username/password login')
        for route in ['/superadmin/cms','/superadmin/home','/superadmin/settings','/superadmin/content?type=branches','/superadmin/content?type=events','/superadmin/edit?type=events','/superadmin/edit?type=programs','/superadmin/media','/superadmin/enquiries','/superadmin/demos','/superadmin/audit']:
            status,body,_=admin.get(route);check(status==200 and 'Warning:' not in body and 'Fatal error' not in body,'Admin route '+route)
        check(admin.post('/superadmin',{'action':'save_content','csrf':'bad'})[0]==419,'Admin rejects invalid CSRF')
        db=sqlite3.connect(testdb);db.row_factory=sqlite3.Row
        check(db.execute("select count(*) from content where type='branches' and status='draft'").fetchone()[0]==6,'Six starter branches are drafts')
        check('Branch 1 — details pending' not in public.get('/branches')[1],'Unconfirmed branches hidden publicly')
        editor=admin.get('/superadmin/edit?type=programs')[1]; data=form(editor,'save_content'); data.update(title='QA Precision Program',slug='qa-precision',status='draft',sort_order='9',**{'fields[summary]':'A temporary test program','fields[body]':'<script>alert(1)</script>'})
        check(admin.post('/superadmin/edit?type=programs',data)[0]==200,'CMS creates draft')
        row=db.execute("select * from content where slug='qa-precision'").fetchone();item_id=row['id']
        check(public.get('/programs/qa-precision')[0]==404,'Draft detail cannot be read anonymously')
        check(admin.get('/programs/qa-precision?preview=1')[0]==200,'Authenticated draft preview')
        data=form(admin.get(f'/superadmin/edit?type=programs&id={item_id}')[1],'save_content');data.update(status='published')
        check(admin.post('/superadmin/edit?type=programs',data)[0]==200,'CMS publishes content')
        body=public.get('/programs/qa-precision')[1];check('QA Precision Program' in body and '&lt;script&gt;' in body and '<script>alert(1)</script>' not in body,'Published content renders and escapes scripts')
        stale=data.copy();stale['title']='Stale overwrite';check('changed in another window' in admin.post('/superadmin/edit?type=programs',stale)[1],'Stale CMS revision rejected')
        data=form(admin.get('/superadmin/home')[1],'save_settings');data['fields[accent]']='QA FOCUS.'
        check(admin.post('/superadmin/home',data)[0]==200 and 'QA FOCUS.' in public.get('/')[1],'Homepage CMS changes render on public site')
        data=form(admin.get('/superadmin/edit?type=branches')[1],'save_content');data.update(title='QA Test Range',slug='qa-range',status='published',**{'fields[city]':'QA City','fields[address]':'Test address for automated checks'})
        admin.post('/superadmin/edit?type=branches',data);check('Test address for automated checks' in public.get('/branches/qa-range')[1],'Branches added without code changes')

        # Simulate a code deployment onto an older, populated production database.
        before_enquiries=db.execute('select count(*) from enquiries').fetchone()[0]
        home_row=db.execute("select id,data from content where type='settings' and slug='home'").fetchone()
        old_home=json.loads(home_row['data']);old_home.update(heading='Learn shooting.',accent='Aim for more.',hero_image='/assets/images/pistol-training.webp')
        db.execute('update content set data=? where id=?',(json.dumps(old_home),home_row['id']))
        db.execute("delete from content where type='settings' and slug='research-2026-10'")
        site_row=db.execute("select id,data from content where type='settings' and slug='site'").fetchone()
        old_site=json.loads(site_row['data']);old_site['logo']=''
        db.execute('update content set data=? where id=?',(json.dumps(old_site),site_row['id']));db.commit()
        upgraded=public.get('/')[1]
        check('Your ambition.' in upgraded and '/assets/images/academy-logo.webp' in upgraded and 'pistol-training.webp' not in upgraded,'Existing database upgrades branding on first deployed request')
        backups=list((Path(tmp)/'backups').glob('*.sqlite'))
        check(len(backups)==1,'Upgrade creates one protected database snapshot')
        with sqlite3.connect(backups[0]) as snapshot:
            saved=json.loads(snapshot.execute("select data from content where type='settings' and slug='home'").fetchone()[0])
            check(saved['heading']=='Learn shooting.','Upgrade snapshot preserves old content for recovery')
        check(db.execute('select count(*) from enquiries').fetchone()[0]==before_enquiries,'Content upgrade preserves enquiries')
        data=form(admin.get('/superadmin/home')[1],'save_settings');data['fields[accent]']='QA CUSTOM AFTER UPGRADE'
        admin.post('/superadmin/home',data)
        check('QA CUSTOM AFTER UPGRADE' in public.get('/')[1] and len(list((Path(tmp)/'backups').glob('*.sqlite')))==1,'Upgrade runs once and preserves later CMS edits')

        event_data=form(admin.get('/superadmin/edit?type=events')[1],'save_content')
        event_data.update(title='QA Calendar Event',slug='qa-calendar-event',status='draft',**{'fields[date]':'2026-11-12','fields[time]':'10:00 AM','fields[summary]':'Calendar integration test'})
        admin.post('/superadmin/edit?type=events',event_data)
        check('QA Calendar Event' not in public.get('/events')[1],'Draft calendar event hidden')
        event_id=db.execute("select id from content where type='events' and slug='qa-calendar-event'").fetchone()[0]
        event_data=form(admin.get(f'/superadmin/edit?type=events&id={event_id}')[1],'save_content');event_data['status']='published'
        admin.post('/superadmin/edit?type=events',event_data)
        event_page=public.get('/events')[1]
        check('data-event-date="2026-11-12"' in event_page and 'QA Calendar Event' in event_page,'Published CMS event feeds calendar dates')
        check('10:00 AM' in public.get('/events/qa-calendar-event')[1],'Event detail includes session time')
        home_page=public.get('/')[1]
        check(home_page.index('id="academy-live"')<home_page.index('Latest achievements')<home_page.index('id="programs"'),'Calendar and achievements precede training programs')
        check('personal coach of Paris 2024 bronze medallist Sarabjot Singh' in home_page,'Verified Olympic coaching USP renders')
        check(all(name not in home_page for name in ['pistol-training.webp','rifle-training.webp','range-practice.webp']),'Foreign athlete images absent from homepage')

        def submit(kind,name='QA Test Applicant'):
            route='/demo-class' if kind=='demo' else '/admission'; html=public.get(route)[1];data=form(html)
            data.update(name=name,email='qa@example.test',phone='9999999999',branch='Help me find a branch',program='10m Air Rifle',experience='First time',message='Test message',consent='1',started=str(int(time.time())-5))
            if kind=='demo':data.update(preferred_date=time.strftime('%Y-%m-%d'),preferred_time='Morning')
            return public.post(route,data)
        data=form(public.get('/admission')[1]);data.update(name='QA',email='bad',phone='a',started=str(int(time.time())-5))
        public.post('/admission',data);check(db.execute('select count(*) from enquiries').fetchone()[0]==0,'Invalid enquiry rejected without saving')
        status,body,_=submit('enquiry','=1+2');check(status==200 and 'Thank you. We’ll be in touch.' in body,'Enquiry saves and shows confirmation')
        request=db.execute("select * from enquiries where kind='enquiry'").fetchone();check(request['status']=='New' and 'Not configured' in request['delivery'],'Unread enquiry with honest email result')
        inbox=admin.get('/superadmin/enquiries')[1];check(request['reference'] in inbox and 'qa@example.test' in inbox,'Enquiry appears in admin inbox')
        data=form(inbox,'mark_viewed') if False else form(inbox,'enquiry_status')
        csrf=data['csrf']; status,body,_=admin.post('/superadmin/enquiries',{'csrf':csrf,'action':'mark_viewed','id':request['id']})
        check(status==200 and json.loads(body)['enquiries']==0 and db.execute('select status from enquiries where id=?',(request['id'],)).fetchone()[0]=='Viewed','Opening request marks Viewed and updates unread count')
        admin.post('/superadmin/enquiries',{'csrf':csrf,'action':'enquiry_status','id':request['id'],'status':'Contacted'});check(db.execute('select status from enquiries where id=?',(request['id'],)).fetchone()[0]=='Contacted','Mark as contacted works')
        admin.post('/superadmin/enquiries',{'csrf':csrf,'action':'enquiry_status','id':request['id'],'status':'New'});check(db.execute('select status from enquiries where id=?',(request['id'],)).fetchone()[0]=='New','Mark as unread works')
        check('#request-' not in admin.last_url,'Mark as unread leaves the row collapsed rather than immediately reopening it')
        exported=admin.get('/superadmin/export?kind=enquiry')[1];check("'=1+2" in exported,'CSV export neutralises formula injection')
        homepage=public.get('/')[1]
        check('coach-abhishek.webp' in homepage and 'hero.webp' not in homepage,'Homepage uses real sports photography')
        callback=form(homepage);callback.update(name='QA Callback Applicant',email='callback@example.test',phone='9999999999',experience='Regular shooter',consent='1',started=str(int(time.time())-5))
        status,body,_=public.post('/admission',callback)
        check(status==200 and 'Thank you. We’ll be in touch.' in body,'Homepage callback form shows a confirmation')
        callback_row=db.execute('select * from enquiries where email=?',('callback@example.test',)).fetchone()
        check(callback_row is not None and callback_row['kind']=='enquiry' and callback_row['experience']=='Regular shooter','Homepage callback persists as an enquiry with the chosen experience')
        callback['csrf']='invalid';public.post('/admission',callback)
        check(db.execute('select count(*) from enquiries where email=?',('callback@example.test',)).fetchone()[0]==1,'Callback rejects invalid CSRF without duplicating the request')
        credits=public.get('/photo-credits')[1]
        check('Martin Rulsch' in credits and 'Subhrasingh' in credits and 'creativecommons.org/licenses/by-sa/' in credits,'Real photos have public attribution and licence links')
        check(submit('demo','QA Demo One')[0]==200,'First demo request saves');check(submit('demo','QA Demo Two')[0]==200,'Second demo request saves')
        demos=db.execute("select * from enquiries where kind='demo' order by id").fetchall();scheduled=time.strftime('%Y-%m-%d')+'T15:00'
        booking={'csrf':csrf,'action':'save_booking','booking_status':'Confirmed','scheduled_at':scheduled,'coach':'QA Coach','notes':'Confirmation test','id':demos[0]['id']}
        admin.post('/superadmin/demos',booking);check(db.execute('select booking_status from enquiries where id=?',(demos[0]['id'],)).fetchone()[0]=='Confirmed','Demo can be confirmed with coach and schedule')
        booking['id']=demos[1]['id'];body=admin.post('/superadmin/demos',booking)[1];check('already has a booking' in body and db.execute('select booking_status from enquiries where id=?',(demos[1]['id'],)).fetchone()[0]=='Requested','Conflicting coach booking rejected')
        for outcome in ['Rescheduled','Attended','No-show','Cancelled']:
            booking.update(id=demos[0]['id'],booking_status=outcome);admin.post('/superadmin/demos',booking);check(db.execute('select booking_status from enquiries where id=?',(demos[0]['id'],)).fetchone()[0]==outcome,'Demo outcome '+outcome)
        check(db.execute('select count(*) from audit_log').fetchone()[0]>=12,'Sensitive CMS and enquiry actions are audited')

        def upload(content,mime,filename):
            boundary='ARSAFormBoundary123';data=[]
            for key,val in [('csrf',csrf),('action','upload_media'),('alt','QA temporary image')]:data.append(f'--{boundary}\r\nContent-Disposition: form-data; name="{key}"\r\n\r\n{val}\r\n'.encode())
            data.append(f'--{boundary}\r\nContent-Disposition: form-data; name="image"; filename="{filename}"\r\nContent-Type: {mime}\r\n\r\n'.encode()+content+b'\r\n');data.append(f'--{boundary}--\r\n'.encode())
            return admin.request('/superadmin/media',b''.join(data),{'Content-Type':'multipart/form-data; boundary='+boundary})
        from PIL import Image
        buf=io.BytesIO();Image.new('RGB',(12,12),'navy').save(buf,format='PNG')
        upload(buf.getvalue(),'image/png','test.png');media=db.execute('select * from media').fetchall();uploads=[ROOT/r['path'].lstrip('/') for r in media]
        check(len(media)==1 and media[0]['path'].endswith('.webp') and uploads[0].is_file(),'Image uploads re-encoded to safe WebP file')
        body=upload(b'<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>','image/svg+xml','bad.svg')[1];check('Use a valid JPG' in body and db.execute('select count(*) from media').fetchone()[0]==1,'Active SVG upload rejected')
        check(public.get('/superadmin/export?kind=enquiry')[2].get('Content-Type','').startswith('text/html'),'Anonymous CSV export cannot access data')
        print(f'\n{checks} checks passed. Live database untouched; temporary uploads removed.')
    finally:
        if testdb.exists():
            cleanup=sqlite3.connect(testdb)
            for row in cleanup.execute('select path from media'):
                file=ROOT/row[0].lstrip('/')
                if file.parent==ROOT/'uploads' and file.is_file():file.unlink()
            cleanup.close()
        process.terminate();process.wait(timeout=5)
