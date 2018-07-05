<!--TODO: Add expand effect on admin login form-->
<header class="masthead-login d-flex">
    <div class="container my-auto">
        <div class="row">
            <div class="col-lg-5 mx-auto" style="display: block">
                <div class="card border-dark bg-light mb-3">
                    <div class="card-header bg-dark text-white"><i class="fas fa-sign-in-alt"></i> Login</div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <a  class="btn btn-info btn-block mt-2 text-white" aria-disabled="true">SSO Login</a>
                            <p class="text-center m-2 text-muted">or</p>
                            <!-- username -->
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id=""><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Username" name="name">
                            </div>
                            <!-- password -->
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id=""><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" placeholder="Password" name="password">
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="remember">
                                <label class="form-check-label">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-dark btn-block mt-5">Submit</button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</header>
