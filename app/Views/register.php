<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
<header>
   <?= $this->include('layouts/header') ?>
  </header>


  <section class="container">
  <?php if (session()->getFlashdata('errors')): ?>
        <div style="color: red;">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <p><?= esc($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
  <div class="modal-body container shadow-sm p-4 mt-5" style="max-width:400px;">
    <h2 class="text-center mt-3 mb-3 fw-bold">Register</h2>
            <form method="POST" action="/addUser">
              <div class="mb-3">
                <label for="name" class="form-label">Enter Name</label>
                <input type="text" class="form-control" placeholder="Enter name..." id="name" name="name" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Enter Email</label>
                <input type="email" class="form-control" placeholder="Enter Email..." id="email" name="email" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Enter Password</label>
                <input type="password" class="form-control" placeholder="Enter Password..." id="password" name="password" aria-describedby="emailHelp">
              </div>
              <div class="mb-3">
                <label for="cpassword" class="form-label">Enter Confirm Password</label>
                <input type="password" class="form-control" id="cpassword" placeholder="Enter Confirm Password..." name="cpassword" aria-describedby="emailHelp">
              </div>



              <button type="submit" class="btn btn-success">Submit</button>
              <a href="/login" class="d-block mt-2">Already have an Account?</a>
            </form>
          </div>
  </section>
  <?= $this->include('layouts/footer') ?>

</body>
</html>
