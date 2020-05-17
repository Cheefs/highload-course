window.addEventListener('load', () => {
   const $table = document.querySelector('.table');

   if ($table) {
       $table.addEventListener('click', (e) => {
           const $target = e.target;
           console.log($target);
           if ($target.parentElement.classList.contains('table__row')) {
               if ($target.classList.contains('actions') ) {
                   location.href = $target.dataset.href;
               } else {
                   location.href = $target.parentElement.dataset.href;
               }
           } else if ($target.classList.contains('btn')) {
               location.href = $target.dataset.href;
           }
       });
   }

   const $cancelBtn = document.querySelector('.cancel');
   if ($cancelBtn) {
       $cancelBtn.addEventListener('click', (e) => {
           location.href = e.target.dataset.href;
       });
   }
});
