<nav
  class="navbar navbar-expand-lg border-bottom border-body p-0"
  data-bs-theme="dark" style="background-color:#778899">
  <div class="container-fluid">
    <div>
      <i
        class="fa-solid fa-clipboard-check "
        style="color: #fff; font-size: 38px;color:#ffc890;"></i>
      <a
        class="navbar-brand text-white fw-bold"
        href="/"
        method='GET'
        style="font-size: 29px; font-weight: bolder">TODO</a>
    </div>
    <?php if (session()->get('isLoggedIn')) : ?>

    <div class="d-flex flex-row justify-content-center gap-2 align-items-center" style="margin-right:6px;">
      
      <form action="/upload" method="post" enctype="multipart/form-data">
      <div class="text-left">
       <input type="file" name="file" id="file" style="display:none;" required>
      <button type="button" id="uploadButton" class="btn btn-sm bg-danger" 
      style="background-color: #fd7e14;font-weight:bold; color:#fff;"><i class="fa-solid fa-upload mx-1"></i>Upload CSV</button>
    </div>
 </form> 

      <div class="text-left">
      <a href="/download"><button class="btn btn-sm bg-dark" style="background-color: rgba(159, 90, 253);font-weight:bold;color:#fff;">
        <i class="fa-solid fa-download mx-1"></i>Download CSV</button></a>
    </div>


    
          <div  class="mt-3 d-flex flex-row gap-1">
          <div>
            <i class="fa-solid fa-circle-user" style="color:#fff; font-size:28px;"></i>
        </div>
        <div>
          <p class="text-white fw-bold" style="font-size: 18px;"><?= session()->get('userName') ?>!</p>
        </div>
     
      </div>
          <form action="/logout" method="post" style="display: inline;">
        <button type="submit" class="btn btn-sm text-dark fw-bold">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          Log out
        </button>
      </form>
  </div>
    <?php endif; ?>

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