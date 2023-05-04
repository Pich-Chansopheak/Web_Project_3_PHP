<?php include "includes/head.php"?>

<div class="con-signup">
    <div class="form-signin">
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register Card -->
          <div class="card">
            <div class="card-body">
              
              <h3 class="sign-title">Sign Up</h3>


              <form id="formAuthentication" class="mb-3" action="signupControl.php" method="POST">
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
                    placeholder="Enter your Full name"
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

                <button class="btn btn-primary d-grid w-100" type="submit" name="sign-up">Sign up</button>
              </form>

              <p class="text-center pb-3">
                <span>Already have an account?</span>
                <a href="signin.php">
                  <span>Sign in instead</span>
                </a>
              </p>
            </div>
          </div>
          <!-- Register Card -->
        </div>
      </div>
    </div>

    </div>
</div>