<nav
  class="navbar navbar-expand-lg bg-success border-bottom border-body"
  data-bs-theme="dark">
  <div class="container-fluid">
    <div>
      <i
        class="fa-solid fa-clipboard-check "
        style="color: #fff; font-size: 38px;"></i>
      <a
        class="navbar-brand text-white fw-bold"
        href="/"
        method='GET'
        style="font-size: 25px; font-weight: bolder">TODO</a>
    </div>

    <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
      <div  class="mt-3">
        <?php if (session()->get('isLoggedIn')) : ?>
          <p class="text-white fw-bold" style="font-size: 18px;">Welcome <?= session()->get('userName') ?>!</p>

     
      <?php endif; ?>
      </div>

      <form action="/upload" method="post" enctype="multipart/form-data">
      <div class="text-left">
       <input type="file" name="file" id="file" style="display:none;" required>
      <button type="button" id="uploadButton" class="btn btn-sm" 
      style="background-color:rgba(159, 80, 133);"><i class="fa-solid fa-upload mx-1"></i>Upload CSV</button>
    </div>`
 </form>

      <div class="text-left">
      <a href="/download"><button class="btn btn-sm" style="background-color: rgba(159, 90, 253);">
        <i class="fa-solid fa-download mx-1"></i>Download CSV</button></a>
    </div>


      <form action="/logout" method="post" style="display: inline;">
        <button type="submit" class="btn btn-sm btn-danger">
          <i class="fa-solid fa-arrow-right-from-bracket" style="color: #fff"></i>
          Log out
        </button>
      </form>

    </div>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent"
      aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>