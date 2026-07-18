document.querySelectorAll('a[href^="#"]').forEach((link)=>{link.addEventListener('click',(event)=>{const target=document.querySelector(link.getAttribute('href'));if(target){event.preventDefault();target.scrollIntoView({behavior:'smooth',block:'start'});}});});

const textarea=document.querySelector('textarea[name="review_text"]');
if(textarea){const counter=document.createElement('small');counter.style.color='#64748b';textarea.insertAdjacentElement('afterend',counter);const update=()=>counter.textContent=`${textarea.value.length}/3000 characters`;textarea.addEventListener('input',update);update();}
