const button = document.getElementById('addDeliverOpen');
const form = document.getElementById('deliverAddForm');

const close = document.getElementById('closeAddDeliver');
  
  button.addEventListener('click', () => {
    form.style.visibility = 'visible';
  });

  close.addEventListener('click', () => {
    form.style.visibility = 'hidden';
  });