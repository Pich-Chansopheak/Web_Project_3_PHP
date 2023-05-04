<?php
    include("includes/head.php");
    include "../libraries/auth.php";
    $error = 0;
    if(isset($_POST['username']))
    {
      $username = $_POST['username'];
      $fullname = $_POST['fullname'];
      $password = $_POST['password'];
      $email = $_POST['email'];
      if($username !=""){
        $result = checkSignup($username, $password,$fullname,$email);
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


.register-form{
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
}

</style>

<!-- Layout wrapper -->

<div class="container-lg d-flex justify-content-center register-form">
  <div class="row">


      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register Card -->
          <div class="card">
            <div class="card-body w-px-400">
              
              <div class="text-center py-3">
                <h4 class="mb-2">Signup</h4>
                <?php 
                  if($error ==1){
                ?>
                  <p class="mb-4 text-danger">
                    Username or password already exist
                  </p>
                <?php 
                  }
                ?>
              </div>
              <!-- form -->
              <form id="formAuthentication" class="mb-3" action="signup.php" method="POST">
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    autofocus
                  />
                </div>
                <div class="mb-3">
                  <label for="fullname" class="form-label">Full name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="fullname"
                    name="fullname"
                    placeholder="Enter your full name"
                    autofocus
                  />
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" />
                </div>
                <div class="mb-3 form-password-toggle">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>

                
                <button class="btn btn-primary d-grid w-100">Sign up</button>
              </form>

              <p class="text-center">
                <span>Already have an account?</span>
                <a href="login.php">
                  <span>Login instead</span>
                </a>
              </p>
            </div>
          </div>
          <!-- Register Card -->
        </div>
      </div>
      </div>
    </div>

<?php include("includes/foot.php") ?>