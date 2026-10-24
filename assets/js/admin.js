const sidebarToggle=document.querySelector('.sidebar-toggle');
const sidebar=document.querySelector('.sidebar');
const mobileSidebar=matchMedia('(max-width:720px)');
function syncSidebar(){if(sidebar)sidebar.inert=mobileSidebar.matches&&sidebarToggle?.getAttribute('aria-expanded')!=='true';}
sidebarToggle?.addEventListener('click',()=>{const open=sidebarToggle.getAttribute('aria-expanded')!=='true';sidebarToggle.setAttribute('aria-expanded',String(open));sidebar?.classList.toggle('open',open);syncSidebar();});
mobileSidebar.addEventListener('change',syncSidebar);syncSidebar();
document.addEventListener('keydown',event=>{if(event.key==='Escape'&&mobileSidebar.matches&&sidebarToggle?.getAttribute('aria-expanded')==='true'){sidebarToggle.click();sidebarToggle.focus();}});
document.querySelectorAll('[data-confirm]').forEach(form=>form.addEventListener('submit',event=>{if(!confirm(form.dataset.confirm))event.preventDefault();}));
document.querySelectorAll('.image-select').forEach(select=>select.addEventListener('change',()=>{const wrapper=select.closest('label');let image=wrapper.querySelector('img');if(!select.value){image?.remove();return;}if(!image){image=document.createElement('img');image.className='field-image-preview';image.alt='Selected image preview';wrapper.append(image);}image.src=select.value;}));
document.querySelectorAll('.enquiry-row').forEach(row=>{
 row.addEventListener('toggle',async()=>{
  if(!row.open||row.dataset.status!=='New'||row.dataset.loading==='true')return;
  row.dataset.loading='true';
  const data=new FormData();data.set('csrf',document.querySelector('meta[name="csrf-token"]').content);data.set('action','mark_viewed');data.set('id',row.dataset.requestId);
  try{const response=await fetch(location.pathname,{method:'POST',body:data,headers:{Accept:'application/json'}});if(!response.ok)throw new Error();const result=await response.json();
   row.dataset.status='Viewed';row.querySelector('.unread-dot')?.classList.add('viewed-dot');const badge=row.querySelector('.status-cell .badge');if(badge){badge.textContent='Viewed';badge.className='badge viewed';}
   document.querySelectorAll('[data-count]').forEach(el=>{const count=result[el.dataset.count]??0;el.textContent=count;if(el.classList.contains('count-badge'))el.hidden=!count;});
  }catch{row.querySelector('.viewed-feedback').textContent='The viewed status could not be saved. Reload the page and try again.';}finally{row.dataset.loading='false';}
 });
});
if(location.hash.startsWith('#request-')){const target=document.getElementById(location.hash.slice(1));if(target){target.open=true;target.scrollIntoView({block:'center'});}}
