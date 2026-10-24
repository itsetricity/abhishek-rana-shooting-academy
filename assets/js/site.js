const navToggle=document.querySelector('.nav-toggle');
navToggle?.addEventListener('click',()=>{const expanded=navToggle.getAttribute('aria-expanded')==='true';navToggle.setAttribute('aria-expanded',String(!expanded));navToggle.setAttribute('aria-label',expanded?'Open navigation':'Close navigation');document.querySelector('#main-nav')?.classList.toggle('open',!expanded);});
document.addEventListener('keydown',event=>{if(event.key==='Escape'&&navToggle?.getAttribute('aria-expanded')==='true'){navToggle.click();navToggle.focus();}});
const backTop=document.querySelector('.back-top');
let scrollPending=false;
let hideTopTimer;
function updateBackTop(){
 const distance=document.documentElement.scrollHeight-window.innerHeight;const show=distance>0&&window.scrollY>=distance*.1;
 if(backTop){
  backTop.tabIndex=show?0:-1;backTop.inert=!show;backTop.setAttribute('aria-hidden',String(!show));
  if(show){clearTimeout(hideTopTimer);backTop.hidden=false;requestAnimationFrame(()=>backTop.classList.add('visible'));}
  else if(backTop.classList.contains('visible')){backTop.classList.remove('visible');hideTopTimer=setTimeout(()=>{backTop.hidden=true;},matchMedia('(prefers-reduced-motion: reduce)').matches?0:250);}
 }
 scrollPending=false;
}
window.addEventListener('scroll',()=>{if(!scrollPending){requestAnimationFrame(updateBackTop);scrollPending=true;}},{passive:true});
window.addEventListener('resize',updateBackTop);
backTop?.addEventListener('click',()=>{window.scrollTo({top:0,behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth'});document.querySelector('.brand')?.focus({preventScroll:true});});
updateBackTop();
// Progressive enhancement: content remains visible when scripting is unavailable.
if('IntersectionObserver' in window&&!matchMedia('(prefers-reduced-motion: reduce)').matches){
 const reveals=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('is-visible');reveals.unobserve(entry.target);}});},{threshold:.08});
 document.querySelectorAll('.section-heading,.program-card,.about-visual,.journey-grid article,.achievement-feature,.event-hub,.branch-panel').forEach(el=>{el.classList.add('reveal-ready');reveals.observe(el);});
}
document.querySelectorAll('[data-calendar]').forEach(hub=>{
 const today=new Date(), current=new Date(today.getFullYear(),today.getMonth(),1), days=hub.querySelector('[data-days]'),title=hub.querySelector('[data-month-title]'),status=hub.querySelector('[data-calendar-status]'),items=[...hub.querySelectorAll('[data-event-date]')],empty=hub.querySelector('[data-empty]');
 let selected='';const key=d=>`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
 function filter(){let count=0;items.forEach(item=>{item.hidden=!!selected&&item.dataset.eventDate!==selected;if(!item.hidden)count++;});empty.hidden=count>0;empty.textContent=selected?'No events announced for this date. Choose a marked date or show all events.':'No confirmed events yet. New dates will appear here as soon as they are announced.';status.textContent=selected?`Events for ${new Date(selected+'T12:00:00').toLocaleDateString('en-IN',{day:'numeric',month:'long',year:'numeric'})}`:'All published events, including dates to be announced.';}
 function render(){title.textContent=current.toLocaleDateString('en-IN',{month:'long',year:'numeric'});days.replaceChildren();const offset=(current.getDay()+6)%7;for(let i=0;i<offset;i++)days.append(document.createElement('span'));const end=new Date(current.getFullYear(),current.getMonth()+1,0).getDate();for(let n=1;n<=end;n++){const date=new Date(current.getFullYear(),current.getMonth(),n),id=key(date),events=items.filter(item=>item.dataset.eventDate===id),button=document.createElement('button');button.type='button';button.textContent=n;button.dataset.date=id;button.setAttribute('aria-label',date.toLocaleDateString('en-IN',{day:'numeric',month:'long',year:'numeric'})+(events.length?`, ${events.length} event${events.length===1?'':'s'}`:', no scheduled events'));button.setAttribute('aria-pressed',String(selected===id));if(key(today)===id)button.setAttribute('aria-current','date');if(events.length)button.classList.add('has-event');button.addEventListener('click',()=>{selected=selected===id?'':id;render();filter();days.querySelector(`[data-date="${id}"]`)?.focus();});days.append(button);}}
 hub.querySelector('[data-prev]').addEventListener('click',()=>{current.setMonth(current.getMonth()-1);selected='';render();filter();});hub.querySelector('[data-next]').addEventListener('click',()=>{current.setMonth(current.getMonth()+1);selected='';render();filter();});hub.querySelector('[data-today]').addEventListener('click',()=>{current.setFullYear(today.getFullYear(),today.getMonth(),1);selected='';render();filter();});hub.querySelector('[data-show-all]').addEventListener('click',()=>{selected='';render();filter();});render();filter();
});
