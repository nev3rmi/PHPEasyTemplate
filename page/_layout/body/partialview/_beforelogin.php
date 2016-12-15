<li>
  <p class="navbar-text">Already have an account?</p>
</li>
<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b>Login</b> <span class="caret"></span></a>
  <ul id="login-dp" class="dropdown-menu">
   <li>
   	<div class="row" id="loginMessage">
   		
   	</div>
   </li>
    <li>
      <div class="row">
        <div class="col-md-12"> Login via
          <div class="social-buttons"> <a href="#" class="btn btn-fb"><i class="fa fa-facebook"></i> Facebook</a> <a href="#" class="btn btn-tw"><i class="fa fa-twitter"></i> Twitter</a> </div>
          or
          <form class="form" accept-charset="UTF-8" id="userLoginForm">
            <div class="form-group">
              <label class="sr-only" for="exampleInputEmail2" id="userEmail">Email address</label>
              <input type="email" class="form-control" id="exampleInputEmail2" placeholder="Email address" name="userEmail" required>
            </div>
            <div class="form-group">
              <label class="sr-only" for="exampleInputPassword2" id="userPassword">Password</label>
              <input type="password" class="form-control" id="exampleInputPassword2" placeholder="Password" name="userPassword" required>
              <div class="help-block text-right"><a href="">Forget the password ?</a></div>
            </div>
            <div class="form-group"> <a class="btn btn-primary btn-block" id="userRequestLogin" onClick="submitForm('#userLoginForm')">Sign in</a> </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="userKeepLogin">
                <span>Keep me logged-in</span> </label>
            </div>
          </form>
        </div>
        <div class="bottom text-center"> New here ? <a href="<?php echo $_url;?>user/register"><b>Join Us</b></a> </div>
      </div>
    </li>
  </ul>
</li>
