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
