$(document).ready(function () {
  //display supplier
  var search = $('#search-input').val();

  const suppliers = (search) => {
    var url = '../server/get-suppliers.php?search=' + search;
    $.ajax({
      type: "GET",
      url: url,
      dataType: "html",
      success: function (response) {
        $('#suppliers-container').html(response);
      }
    });
  }

  const addSupplierFormDataClose = () => {
    $('#add-sup-form').css('display', 'none');
    $('#supp-name').val('');
    $('#supp-address').val('');
    $('#contact-person').val('');
    $('#contact-no').val('');
  }

  const closeEditSupplier = () => {
    $('#edit-sup-form').css('display', 'none');
    $('#hidden-sup-id-edit').val('');
    $('#edit-supplier-title').text('');
    $('#edit-supp-name').val('');
    $('#edit-supp-address').val('');
    $('#edit-contact-person').val('');
    $('#edit-contact-no').val('');
  }

  const addSupplier = () => {
    var supplier_name = $('#supp-name').val();
    var supplier_address = $('#supp-address').val();
    var contact_person = $('#contact-person').val();
    var contact_no = $('#contact-no').val();

    if (supplier_name && contact_no) {
      $.ajax({
        type: "POST",
        url: "../process/add-supplier-process.php",
        data: {
          supp_name: supplier_name,
          supp_address: supplier_address,
          contact_person: contact_person,
          contact_number: contact_no,
          addSup: true
        },
        success: function (response) {
          if (response === 'ok') {
            $('.supplier-added').css('opacity', 1)
            setTimeout(function () {
              $('.supplier-added').css('opacity', 0)
            }, 2000);
            suppliers('');
            addSupplierFormDataClose();
          } else {
            $('.supplier-not-added').css('opacity', 1)
            setTimeout(function () {
              $('.supplier-not-added').css('opacity', 0)
            }, 2000);
          }
        }
      });
    } else {
      $('.input-contact-no').css('opacity', 1)
      setTimeout(function () {
        $('.input-contact-no').css('opacity', 0)
      }, 2000);
    }
  }

  const deleteSupplier = (sup_id) => {
    $.ajax({
      url: '../process/delete-supplier-process.php',
      method: 'GET',
      data: { sup_id: sup_id },
      success: function (response) {
        if (response === 'ok') {
          $('.deletion-success').css('opacity', 1);
          setTimeout(function () {
            $('.deletion-success').css('opacity', 0);
          }, 2000);
          $('#search-input').val('');
        } else {
          $('.deletion-unsuccessful').css('opacity', 1);
          setTimeout(function () {
            $('.deletion-unsuccessful').css('opacity', 0);
          }, 2000);
          $('#search-input').val('');
        }
        suppliers('');
        $('#myModal').modal('hide');
        $('#myModal').trigger('hidden.bs.modal');
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }


  $('#search-input').keyup(() => {
    var search = $('#search-input').val();
    suppliers(search);
  });


  //add supplier
  $('#addSupplier').click(() => {
    closeEditSupplier();
    $('#add-sup-form').css('display', 'block');
  })

  $('#closeAddSupplier').click(() => {
    addSupplierFormDataClose();
  })

  $('#btn-add-supplier').click((e) => {
    e.preventDefault();
    addSupplier();
  })

  //edit supplier
  $(document).on('click', '#btn-edit-supplier', (e) => {
    e.preventDefault();
    addSupplierFormDataClose();
    var supID = $(e.currentTarget).data('supplier_id');

    $.ajax({
      type: "GET",
      url: "../process/get-supplier-details.php",
      data: { supID: supID },
      success: function (response) {
        var parsedResponse = JSON.parse(response);
        $('#edit-supplier-title').text('Edit Supplier ID ' + supID);
        $('#hidden-sup-id-edit').val(supID);
        $('#edit-supp-name').val(parsedResponse[0]);
        $('#edit-supp-address').val(parsedResponse[1]);
        $('#edit-contact-person').val(parsedResponse[2]);
        $('#edit-contact-no').val(parsedResponse[3]);
        $('#edit-sup-form').css('display', 'block');
      }
    });
  });

  $('#closeEditSupplier').click((e) => {
    e.preventDefault();
    closeEditSupplier();
  });

  $('#btn-edit-supplier-save').click((e) => {
    e.preventDefault();
    var sup_id = $('#hidden-sup-id-edit').val();
    var sup_name = $('#edit-supp-name').val();
    var sup_address = $('#edit-supp-address').val();
    var contact_person = $('#edit-contact-person').val();
    var contact_no = $('#edit-contact-no').val();

    if (sup_id && sup_name && contact_no) {
      $.ajax({
        type: "POST",
        url: "../process/edit-supplier-process.php",
        data: {
          supp_id: sup_id,
          supp_name: sup_name,
          supp_address: sup_address,
          contact_person: contact_person,
          contact_number: contact_no,
          edit_product: true
        },
        success: function (response) {
          if (response === 'ok') {
            suppliers('');
            closeEditSupplier();
            $('.supplier-edited').css('opacity', 1)
            setTimeout(function () {
              $('.supplier-edited').css('opacity', 0)
            }, 2000);
          } else {
            $('.supplier-not-edited').css('opacity', 1)
            setTimeout(function () {
              $('.supplier-not-edited').css('opacity', 0)
            }, 2000);
          }
        }
      });
    } else {
      $('.input-contact-no').css('opacity', 1)
      setTimeout(function () {
        $('.input-contact-no').css('opacity', 0)
      }, 2000);
    }
  });

  // delete supplier
  $(document).on('click', '#btn-delete-supplier', (e) => {
    e.preventDefault();
    var supplier_id = $(e.currentTarget).data('supplier_id');

    $.ajax({
      url: '../process/get-supplier-details.php',
      method: 'GET',
      data: { supID: supplier_id },
      success: function (response) {
        var data = JSON.parse(response);
        var supplierName = data[0];

        var modalTitle = "Delete " + supplierName;
        $('.modal-title').text(modalTitle);
        $('#delete-this-supplier').attr('data-supplier_id', supplier_id);

        $('#myModal').modal('show');
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  })

  $('#delete-this-supplier').click((e) => {
    e.preventDefault();
    var supID = $('#delete-this-supplier').attr('data-supplier_id');
    deleteSupplier(supID)
  });

  $('#myModal').on('hidden.bs.modal', () => {
    $('#delete-this-supplier').attr('data-supplier_id', '');
  });

  $('#myModal').on('click', '#close-delete-this-supplier', () => {
    $('#myModal').modal('hide');
    $('#myModal').trigger('hidden.bs.modal');
  })


  suppliers(search);
});