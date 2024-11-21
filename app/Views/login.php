<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
<header>
   <?= $this->include('layouts/header') ?>
  </header>


  <section class="container">
  <div class="modal-body container w-25 h-50 shadow-sm p-4 mt-5">
    <h2 class="text-center mt-3 mb-3 fw-bold">Log in</h2>
            <form method="POST">
             

              <div class="mb-3">
                <label for="email" class="form-label">Enter Email</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Enter Password</label>
                <input type="password" class="form-control" id="password" name="password" aria-describedby="emailHelp">
              </div>
          <button type="submit" class="btn btn-success">Submit</button>
          <a href="/register" class="d-block mt-2">Don't have an Account?</a>

            </form>
          </div>
  </section>
  <?= $this->include('layouts/footer') ?>

</body>
</html>
