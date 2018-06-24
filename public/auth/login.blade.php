@extends('layouts.app')

@section('content')
    {{--<header class="masthead-login">--}}

    <header class="masthead-login d-flex">
        <div class="container my-auto">
            <div class="row">
                <div class="col-lg-10 mx-auto text-white text-center" style="display: none">
                    <p>請選擇登入身份 : </p>
                    <a name="" id="" class="btn btn-success btn-lg" href="#" role="button">淡江單一登入 SSO</a>
                    <span>OR</span>
                    <a name="" id="" class="btn btn-info btn-lg" href="#" role="button">Site Admin</a>
                </div>
                <div class="col-lg-5 mx-auto" style="display: block">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Login</h4>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Login</label>
                                    <input type="text" class="form-control" placeholder="Username" name="name">
                                </div>
                                <div class="form-group">
                                    <label>Password <a href="#">忘記密碼？</a></label>
                                    <input type="password" class="form-control" placeholder="Password" name="password">
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" name="remember">
                                    <label class="form-check-label">記住我</label>
                                </div>
                                <button type="submit" class="btn btn-dark btn-block">Submit</button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </header>

@endsection
