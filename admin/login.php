<?php 
  
  include "includes/head.php";
  include "../libraries/auth.php";
  $error = 0;
  if(isset($_POST['txtusername']))
  {
    $username = $_POST['txtusername'];
    $password = $_POST['txtpassword'];
    if($username !=""){
      $result = checkLogin($username, $password);
      if($result)
      {
        header("location: index.php");
        exit (0);
      }
      else
      {
        $error = 1;
      }
    }
    
  }

?>
<style>


.login-form{
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
}

</style>


<!-- Layout wrapper -->

<div class="container-lg d-flex justify-content-center login-form">
  <div class="row my-auto">
      <div class="authentication-wrapper authentication-basic">
        <div class="authentication-inner">

          <!-- Register -->
          <div class="card">
            <div class="card-body w-px-400">
              
              <div class="text-center py-3">
                <h4 class="mb-2">Login</h4>
                <?php 
                  if($error ==1){
                ?>
                  <p class="mb-4 text-danger">
                    Invalid username or password
                  </p>
                <?php 
                  }
                ?>
              </div>
              <!-- Form -->
                <form id="formAuthentication" class="mb-3" action="login.php" method="post">
                  <div class="mb-3">
                    <label for="txtusername" class="form-label">Email or Username</label>
                    <input
                      type="text"
                      class="form-control"
                      id="txtusername"
                      name="txtusername"
                      autofocus
                    />
                  </div>
                  <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                      <label class="form-label" for="txtpassword">Password</label>
                      
                    </div>
                    <div class="input-group input-group-merge">
                      <input
                        type="password"
                        id="txtpassword"
                        class="form-control"
                        name="txtpassword"
                      />
                      <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                  </div>
                </form>
              <!-- End of Form -->
              
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>

      </div>
    </div>


<?php include "includes/foot.php" ?>