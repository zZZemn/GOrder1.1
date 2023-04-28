var editLinks = document.getElementsByClassName('edit-deliver-link');
for (var i = 0; i < editLinks.length; i++) {
    editLinks[i].addEventListener('click', function(e) {
        e.preventDefault();
        var deliverId = this.classList[1];
        document.getElementById('deliver-id-input').value = deliverId;
        document.querySelector('h5').innerHTML = 'Edit Deliver ' + deliverId;
        document.getElementById('deliverEditForm').style.visibility = 'visible';
    });
}



const editform = document.getElementById('deliverEditForm');
const editclose = document.getElementById('closeEditDeliver');

  editclose.addEventListener('click', () => {
    editform.style.visibility = 'hidden';
  });