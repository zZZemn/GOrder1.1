const button = document.getElementById('addSupplier');
const form = document.getElementById('add-sup-form');

const close = document.getElementById('closeAddSupplier');
  
  button.addEventListener('click', () => {
    form.style.display = 'block';
  });

  close.addEventListener('click', () => {
    form.style.display = 'none';
  });
