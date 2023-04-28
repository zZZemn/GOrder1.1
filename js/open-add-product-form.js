const button = document.getElementById('addProduct');
const form = document.getElementById('addingProduct-form');

const close = document.getElementById('closeAddProduct');
const cancel = document.getElementById('cancelAddProduct');
  
  button.addEventListener('click', () => {
    form.style.display = 'block';
  });

  close.addEventListener('click', () => {
    form.style.display = 'none';
  });

  cancel.addEventListener('click', () => {
    form.style.display = 'none';
  });
