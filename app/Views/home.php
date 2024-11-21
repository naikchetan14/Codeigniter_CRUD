<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Your ToDo List!</title>
  <meta name="description" content="The small framework with powerful features">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="/favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- STYLES -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <style {csp-style-nonce}>
.dataTable {
  text-align: center;
    background-color: #ffffff; /* White background */
}

/* Style the table header */
.dataTable thead th {
    /* background-color: #343a40; Dark background */
    color: #ffffff; /* White text */
    font-weight: bold; /* Bold text */
    text-align: center; /* Center text */
}
/* Style the table rows */
.dataTable tbody tr {
    transition: background-color 0.3s; /* Smooth transition */
}

.dataTable tbody tr:hover {
    background-color: #f1f1f1; /* Light gray on hover */
}

/* Style the pagination */
.dataTables_paginate {
    margin-top: 20px; /* Space above pagination */
}

/* Style the pagination buttons */
.dataTables_paginate .paginate_button {
    padding: 0.5em 1em; /* Padding for buttons */
    margin: 0 0.1em; /* Space between buttons */
    border: 1px solid #007bff; /* Border color */
    border-radius: 5px; /* Rounded corners */
    background-color: #007bff; /* Button background */
    color: white; /* Button text color */
}

.dataTables_paginate .paginate_button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}


/* Style the search input */
.dataTables_filter input {
    margin-left: 10px; /* Space to the left of the input */
    border-radius: 5px; /* Rounded corners */
    border: 1px solid #ced4da; /* Border color */
    padding: 0.5em; /* Padding */
}
.dataTables_filter label{
  font-weight: bolder;
}
.dataTables_filter input:hover{
  border:1px solid green;
}
#todoTable_filter{
  margin-bottom: 10px;
}
  </style>
</head>

<body>

  <!-- HEADER: MENU + HEROE SECTION -->
  <header>

    <nav class="navbar navbar-expand-lg bg-success border-bottom border-body" data-bs-theme="dark">
      <div class="container-fluid">
        <div>
          <i class="fa-solid fa-clipboard-check" style="color:#fff; font-size:38px;"></i>
          <a class="navbar-brand" href="#" style="font-size: 30px;font-weight:bolder">TODO</a>
        </div>

        <div>
       <button type="button" class="btn btn-sm btn-danger"> <i class="fa-solid fa-arrow-right-from-bracket" style="color:#fff;"></i>Log out</button>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

      </div>
    </nav>

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
      <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <?= implode('<br>', session()->getFlashdata('errors')) ?>
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
            <form method="POST" id="editTodoForm" >
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
                <label for="date" class="form-label">Enter Date</label>
                <input type="date" class="form-control" id="date" name="date" value="" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="status" class="form-label">Update Status</label>
                <input type="number" class="form-control"  min="0" max="1" step="1" value="0" id="status" name="status" value="" aria-describedby="emailHelp">
              </div>
             <button type="submit" class="btn btn-success">Update</button>
            </form>
          </div>
    
        </div>
      </div>
    </div>


    <h1 class="text-center mt-4 text-primary-emphasis">ToDo List</h1>
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
      <tbody >
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
                  data-title="<?= esc($todo['title']); ?>"
                  data-description="<?= esc($todo['description']); ?>"
                  data-date="<?= esc($todo['date']); ?>">
                  <i class="mx-1 fa-regular fa-pen-to-square"></i>
                </button>
                </td>
              
              
                  <td><a href="/delete/<?= $todo["Id"]?>" onclick="confirm('confirm You want to Delete!');"><button type="button" class="btn btn-danger btn-sm" action="/delete/<?= $todo["Id"]?>"><i class="fa-sharp fa-solid fa-trash"></i></button></a></td>
                  
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
                  data-title="<?= esc($todo['title']); ?>"
                  data-description="<?= esc($todo['description']); ?>"
                  data-date="<?= esc($todo['date']); ?>">
                  <i class="mx-1 fa-regular fa-pen-to-square"></i>
                </button>
                </td>
              
              
                  <td><a href="/delete/<?= $todo["Id"]?>" onclick="confirm('confirm You want to Delete!');"><button type="button" class="btn btn-danger btn-sm" action="/delete/<?= $todo["Id"]?>"><i class="fa-sharp fa-solid fa-trash"></i></button></a></td>
                  
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

  <footer class="p-3 bg-success" style="position: absolute;bottom:0;width:100%">
<h4 class="text-center text-white">www.todos.com</h4>
  </footer>

  <!-- SCRIPTS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
<script {csp-script-nonce}>
   $(document).ready(function() {
        $('#todoTable').DataTable({
          "pageLength": 5 
        }); // Initialize DataTables on the table with id 'todoTable'
   var myModal = document.getElementById('staticBackdrop');
    myModal.addEventListener('show.bs.modal', function(event) {
      // Get the button that triggered the modal
      var button = event.relatedTarget;

      // Extract info from data-* attributes
      var id = button.getAttribute('data-id');
      var title = button.getAttribute('data-title');
      var description = button.getAttribute('data-description');
      var date = button.getAttribute('data-date');

      // Update the modal's content
      var modalTitle = myModal.querySelector('.modal-title');
      var todoId = myModal.querySelector('#todoId');
      var titleInput = myModal.querySelector('#title');
      var descriptionInput = myModal.querySelector('#desc');
      var dateInput = myModal.querySelector('#date');

      modalTitle.textContent = 'Update ToDo - ID: ' + id;
      todoId.value = id;
      titleInput.value = title;
      descriptionInput.value = description;
      dateInput.value = date;
      var editTodoForm = myModal.querySelector('#editTodoForm');
      editTodoForm.action = '/edit/' + id;
    });
  
  });

  
</script>

<!-- -->

</body>

</html>