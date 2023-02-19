@include('layouts.header')
<body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar_inner">
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand" href="{{ route('home') }}">
                                Website Logo
                            </a>
                            <ul class="d-flex justify-content-end w-100">
                                @auth
                                    <div>Welcome {{Auth::user()->name}}</div>
                                    <li>
                                        <a class="custom-btn" href="{{ route('logout') }}"> Logout</a>
                                    </li>
                                    <li >
                                        <a class="custom-btn" href="{{ route('password.change.form') }}"> Change Password</a>
                                    </li>
                                @endauth
                                @guest
                                    <li>
                                        <a class="custom-btn" href="{{ route('login') }}"> Login</a>
                                    </li>
                                    <li>
                                        <a class="custom-btn" href="{{ route('register') }}">Registration</a>
                                    </li>
                                @endguest
                               
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            @yield('main-content')
        </div>
    @include('layouts.footer')
</body>

</html>
