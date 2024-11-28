<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Your ToDo List!</title>
  <meta name="description" content="The small framework with powerful features">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="/favicon.ico">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- STYLES -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <style {csp-style-nonce}>
    .dataTable {
      text-align: center;
      background-color: #ffffff;
      /* White background */
    }

    /* Style the table header */
    .dataTable thead th {
      /* background-color: #343a40; Dark background */
      color: #ffffff;
      /* White text */
      font-weight: bold;
      /* Bold text */
      text-align: center;
      /* Center text */
    }

    /* Style the table rows */
    .dataTable tbody tr {
      transition: background-color 0.3s;
      /* Smooth transition */
    }

    .dataTable tbody tr:hover {
      background-color: #f1f1f1;
      /* Light gray on hover */
    }

    /* Style the pagination */
    .dataTables_paginate {
      margin-top: 20px;
      /* Space above pagination */
    }

    /* Style the pagination buttons */
    .dataTables_paginate .paginate_button {
      padding: 0.5em 1em;
      /* Padding for buttons */
      margin: 0 0.1em;
      /* Space between buttons */
      border: 1px solid #007bff;
      /* Border color */
      border-radius: 5px;
      /* Rounded corners */
      background-color: #007bff;
      /* Button background */
      color: white;
      /* Button text color */
    }

    .dataTables_paginate .paginate_button:hover {
      background-color: #0056b3;
      /* Darker blue on hover */
    }


    /* Style the search input */
    .dataTables_filter input {
      margin-left: 10px;
      /* Space to the left of the input */
      border-radius: 5px;
      /* Rounded corners */
      border: 1px solid #ced4da;
      /* Border color */
      padding: 0.5em;
      /* Padding */
    }

    .dataTables_filter label {
      font-weight: bolder;
    }

    .dataTables_filter input:hover,
    .dataTables_filter input:active {
      border: 2px solid green !important;
    }


    #todoTable_filter {
      margin-bottom: 10px;
    }
  </style>
</head>

<body>

  <!-- HEADER: MENU + HEROE SECTION -->
  <header>
    <?= $this->include('layouts/header') ?>
  </header>

  <!-- CONTENT -->

  <section class="container">
    <!-- Success Alert -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show w-25 mx-auto mt-3" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Error Alert -->
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger alert-dismissible fade w-25 mx-auto show mt-3" role="alert">
        <?= session()->getFlashdata('errors') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Add ToDo Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="add">
              <div class="mb-3">
                <label for="title" class="form-label">Enter Title</label>
                <input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="desc" class="form-label">Enter Description</label>
                <input type="text" class="form-control" id="desc" name="description" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="date" class="form-label">Enter Date</label>
                <input type="date" class="form-control" id="date" name="date" aria-describedby="emailHelp">
              </div>



              <button type="submit" class="btn btn-success">Submit</button>
            </form>
          </div>

        </div>
      </div>
    </div>

    <!-- UpdateTodoModal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Update ToDo</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" id="editTodoForm">
              <input type="hidden" id="todoId" name="id" value="">
              <div class="mb-3">
                <label for="title" class="form-label">Enter Title</label>
                <input type="text" class="form-control" id="title" name="title" value="" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="desc" class="form-label">Enter Description</label>
                <input type="text" class="form-control" id="desc" name="description" value="" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="dateField" class="form-label">Enter Date</label>
                <input type="date" class="form-control" id="dateField" name="date" value="" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="status" class="form-label">Update Status</label>
                <input type="number" class="form-control" min="0" max="1" value="0" id="status" name="status" aria-describedby="emailHelp">
              </div>
              <button type="submit" class="btn btn-success">Update</button>
            </form>
          </div>

        </div>
      </div>
    </div>
    <h2 class="text-center mt-4 text-primary-emphasis">ToDo List</h2>
   
    <div class="mt-3 text-right d-flex flex-row gap-3 flex-wrap" style="width: 100%;">
      <span class="fw-bold text-secondary">Filter By Name: </span><select data-placeholder="Select a Title" id="nameFilter"
        class="flex-grow-1 flex-shrink-1 nameDropdown" style="border:2px solid green;border-radius:4px;width:150px;">
        <option value="">Select a Title</option>
        <?php foreach ($nameList as $item): ?>
          <option value="<?= esc($item) ?>"><?= esc($item) ?></option>
        <?php endforeach; ?>
      </select>

      <span class="fw-bold text-secondary">Filter By description: </span><select data-placeholder="Select a Description" id="descFilter"
        class="flex-grow-1 flex-shrink-1 descDropdown" style="border:2px solid green;border-radius:4px;width:150px;">
        <option value="">Select a Description</option>
        <?php foreach ($descriptionList as $item): ?>
          <option value="<?= esc($item) ?>"><?= esc($item) ?></option>
        <?php endforeach; ?>
      </select>

      <span class="fw-bold text-secondary">Filter By Status: </span><select data-placeholder="Select a Status" id="statusFilter" class="flex-grow-1 flex-shrink-1 statusDropdown"
        style="border:2px solid green;border-radius:4px;width:150px;">
        <option value="">Select a Status</option>
        <option value="0">Pending</option>
        <option value="1">Completed</option>
      </select>
      <span class="fw-bold text-secondary">Filter By ID: </span><select data-placeholder="Select a ID" id="idFilter"
        class="flex-grow-1 flex-shrink-1 idDropdown" style="border:2px solid green;border-radius:4px;width:150px;">
        <option value="">Select a ID</option>
        <?php foreach ($todoIDS as $item): ?>
          <option value="<?= esc($item) ?>"><?= esc($item) ?></option>
        <?php endforeach; ?>
      </select>
      </select>
    </div>

    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-success btn-sm mt-2 mb-2"><i class="fa-regular fa-plus"></i></button>
    <div style="height: 450px; overflow-y: auto;">
      <table class="table text-center" id="todoTable" style="max-height:200px;">
        <thead class="table-dark">
          <tr>
            <th scope="col">ID</th>
            <th scope="col">TITLE</th>
            <th scope="col">DESCRIPTION</th>
            <th scope="col">DATE</th>
            <th scope="col">STATUS</th>
            <th scope="col">UPDATE</th>
            <th scope="col">DELETE</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty(($allTodos['mysql']))): ?>
            <?php foreach ($allTodos['mysql'] as $todo): ?>
              <?php if ($todo['status'] == 1): ?>
                <tr>

                  <th scope="row"><?= esc($todo['Id']) ?></th>

                  <td style=" text-decoration: line-through;"><?= esc($todo['title']) ?></td>
                  <td style=" text-decoration: line-through;"><?= esc($todo['description']) ?></td>
                  <td style=" text-decoration: line-through;"><?= esc($todo['date']) ?></td>

                  <td>
                    <div>
                      <p class="text-success fw-bold">Completed</p>
                    </div>
                  </td>
                  <td>
                    <button type="button" disabled="true" class="btn btn-primary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#staticBackdrop"
                      data-id="<?= esc($todo['Id']); ?>"
                      data-staus="<?= esc($todo['status']); ?>"
                      data-title="<?= esc($todo['title']); ?>"
                      data-description="<?= esc($todo['description']); ?>"
                      data-date="<?= esc($todo['date']); ?>">
                      <i class="mx-1 fa-regular fa-pen-to-square"></i>
                    </button>
                  </td>


                  <td><a href="/delete/<?= $todo["Id"] ?>" onclick="return confirmDelete();"><button type="button" class="btn btn-danger btn-sm" action="/delete/<?= $todo["Id"] ?>"><i class="fa-sharp fa-solid fa-trash"></i></button></a></td>

                </tr>
              <?php else: ?>
                <tr>

                  <th scope="row"><?= esc($todo['Id']) ?></th>

                  <td><?= esc($todo['title']) ?></td>
                  <td><?= esc($todo['description']) ?></td>
                  <td><?= esc($todo['date']) ?></td>

                  <td>
                    <div>
                      <p class="text-danger fw-bold">Pending</p>
                    </div>
                  </td>
                  <td>
                    <button type="button" class="btn btn-primary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#staticBackdrop"
                      data-id="<?= esc($todo['Id']); ?>"
                      data-staus="<?= esc($todo['status']); ?>"
                      data-title="<?= esc($todo['title']); ?>"
                      data-description="<?= esc($todo['description']); ?>"
                      data-date="<?= esc($todo['date']); ?>">
                      <i class="mx-1 fa-regular fa-pen-to-square"></i>
                    </button>
                  </td>


                  <td><a href="/delete/<?= $todo["Id"] ?>" onclick="return confirmDelete();"><button type="button" class="btn btn-danger btn-sm" action="/delete/<?= $todo["Id"] ?>"><i class="fa-sharp fa-solid fa-trash"></i></button></a></td>

                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php else: ?>
            <h3>No Todos found</h3>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- FOOTER: DEBUG INFO + COPYRIGHTS -->
  <?= $this->include('layouts/footer') ?>



  <!-- SCRIPTS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
<script {csp-script-nonce}>
   function confirmDelete() {
        return confirm('Are you sure you want to delete this ToDo?');
    }
  $(document).ready(function() {
    $('#titleFilter,#descFilter,#statusFilter,#idFilter').select2();
    $('#todoTable').DataTable({
      "pageLength": 5,
      "lengthMenu": [
        [5, 10, 25, -1],
        [5, 10, 25, "All"]
      ]
    }); // Initialize DataTables on the table with id 'todoTable'
   
    var myModal = document.getElementById('staticBackdrop');
    myModal.addEventListener('show.bs.modal', function(event) {
      // Get the button that triggered the modal
      var button = event.relatedTarget;

      // Extract info from data-* attributes
      var id = button.getAttribute('data-id');
      var title = button.getAttribute('data-title');
      var description = button.getAttribute('data-description');
      var dateVal = button.getAttribute('data-date');
      var statusVal = button.getAttribute('data-status');

      // Update the modal's content
      var modalTitle = myModal.querySelector('.modal-title');
      var todoId = myModal.querySelector('#todoId');
      var titleInput = myModal.querySelector('#title');
      var descriptionInput = myModal.querySelector('#desc');
      var dateInput = myModal.querySelector('#dateField');
      var status = myModal.querySelector('#status');
      console.log('Date val', dateVal, statusVal)


      modalTitle.textContent = 'Update ToDo - ID: ' + id;
      todoId.value = id;
      titleInput.value = title;
      descriptionInput.value = description;
      dateInput.value = dateVal;
      status.value = statusVal;

      var editTodoForm = myModal.querySelector('#editTodoForm');
      editTodoForm.action = '/edit/' + id;

    });
    $('.js-example-basic-single').select2({
      placeholder: 'Select an option'
    });
    $('.nameDropdown').select2({
      placeholder: "Select a Title",
      allowClear: true
    });
    $('.descDropdown').select2({
      placeholder: "Select a the description",
      allowClear: true
    });

    $('.statusDropdown').select2({
      placeholder: "Select the status",
      allowClear: true
    });
    $('.idDropdown').select2({
      placeholder: "Select the ID",
      allowClear: true
    });

    $('#nameFilter,#descFilter,#statusFilter,#idFilter').on('change', function() {
      let title = $('#nameFilter').val();
      let desc = $('#descFilter').val();
      let idVal = $('#idFilter').val();

      if(title === ''){
        title=null;
      }
      if(desc === ''){
        desc=null;
      }
      if(idVal === ''){
        idVal=null;
      }
      // Check if the status filter is selected
      let status = $('#statusFilter').val();
      if (status === '') {
        status = null; // Set to null if no status is selected
      } else {
        status = status === 'Completed' ? 1 : 0; // Convert to 1 or 0
      }
      console.log("Sending filter values:", {
        title: title,
        description: desc,
        status: status,
        id: idVal
    });

      $.ajax({
        url: '/todofilter',
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Adjust according to your CSRF setup
        },
        contentType: 'application/json',
        data: JSON.stringify({
          title: title,
          description: desc,
          status: status,
          id: idVal
        }),
        success: function(response) {
          console.log("Success", response);
          // Clear the existing table body
          const todoTableBody = $('#todoTable tbody');
          todoTableBody.empty();

          if (response.length !== 0) {
            response.forEach((item) => {
              console.log('status', typeof item.status)
              const statusText = item.status === '1' ? 'Completed' : 'Pending';
              const btnDisabled = item.status === '1' ? true : false;
              const statusTextclass = item.status == '1' ? 'text-success' : 'text-danger';
              todoTableBody.append(
                `<tr>         
                <th scope="row">${item.Id}</th>
                  <td style="text-decoration:  ${statusText === 'Completed'?'line-through':''};">${item.title}</td>
                  <td style=" text-decoration: ${statusText === 'Completed'?'line-through':''}">${item.description}</td>
                  <td style=" text-decoration:  ${statusText === 'Completed'?'line-through':''};">${item.date}</td>
                  <td>
                    <div>
                      <p class="${statusTextclass} fw-bold">${statusText}</p>
                    </div>
                  </td>
                  <td>
                <button type="button" ${btnDisabled ? 'disabled' : ''} class="btn btn-primary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#staticBackdrop"
                      data-id="${item.Id}"
                      data-staus=""
                      data-title="${item.title}"
                      data-description="${item.description}"
                      data-date="${item.date}">
                      <i class="mx-1 fa-regular fa-pen-to-square"></i>
                    </button>
                  </td>
                  <td><a href="/delete/${item.Id}" onclick="return confirmDelete();"><button type="button" class="btn btn-danger btn-sm" action="/delete/<?= $todo["Id"] ?>"><i class="fa-sharp fa-solid fa-trash"></i></button></a></td>
     </tr>      
           `
              );
            });
          } else {
            todoTableBody.append('<h6 class="text-danger text-center w-100 d-block">No Todos Found</h6>');
          }
        },
        error: function() {
          console.log("error");
        }
      })
    })


  });
</script>

<!-- -->

</body>

</html>