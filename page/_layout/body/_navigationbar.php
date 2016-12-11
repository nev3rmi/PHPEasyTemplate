<nav class="navbar navbar-default" role="navigation" id="nav">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand" href="#"><?php echo $_siteName;?></a> </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li><a href="<?php echo $_url;?>">Home</a></li>
        <li><a href="<?php echo $_url;?>about">About</a></li>
        <li><a href="#">Page 2</a></li>
        <li><a href="#">Page 3</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li>
          <p class="navbar-text">Already have an account?</p>
        </li>
        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b>Login</b> <span class="caret"></span></a>
          <ul id="login-dp" class="dropdown-menu">
            <li>
              <div class="row">
                <div class="col-md-12"> Login via
                  <div class="social-buttons"> <a href="#" class="btn btn-fb"><i class="fa fa-facebook"></i> Facebook</a> <a href="#" class="btn btn-tw"><i class="fa fa-twitter"></i> Twitter</a> </div>
                  or
                  <form class="form" role="form" method="post" action="login" accept-charset="UTF-8" id="login-nav">
                    <div class="form-group">
                      <label class="sr-only" for="exampleInputEmail2">Email address</label>
                      <input type="email" class="form-control" id="exampleInputEmail2" placeholder="Email address" required>
                    </div>
                    <div class="form-group">
                      <label class="sr-only" for="exampleInputPassword2">Password</label>
                      <input type="password" class="form-control" id="exampleInputPassword2" placeholder="Password" required>
                      <div class="help-block text-right"><a href="">Forget the password ?</a></div>
                    </div>
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                    </div>
                    <div class="checkbox">
                      <label>
                        <input type="checkbox">
                        <span>Keep me logged-in</span> </label>
                    </div>
                  </form>
                </div>
                <div class="bottom text-center"> New here ? <a href="#"><b>Join Us</b></a> </div>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Need auto get page --> 
