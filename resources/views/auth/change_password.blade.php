@extends('layouts.master')
@section('main-content')
    <div class="container">
        <div class="row">

            <div class="col-md-10 offset-2">
                <div class="panel panel-default">
                    <h2>Change password</h2>

                    <div class="panel-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                     
                        <form class="form-horizontal" method="POST" action="{{ route('password.change.post') }}">
                            {{ csrf_field() }}
                            <div class="form-group mt-3">
                                <label for="new-password" class="col-md-4 control-label">Current Password</label>

                                <div class="col-md-6">
                                    <input id="current_password" type="password" class="form-control"
                                        name="current_password">

                                    <div class="error">
                                        @error('current_password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="new-password" class="col-md-4 control-label">New Password</label>

                                <div class="col-md-6">
                                    <input id="new_password" type="password" class="form-control" name="new_password">
                                    <div class="error">
                                        @error('new_password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="form-group mt-3">
                                <label for="new-password-confirm" class="col-md-4 control-label">Confirm New
                                    Password</label>

                                <div class="col-md-6">
                                    <input id="new-password-confirm" type="password" class="form-control"
                                        name="confirm_password">
                                    <div class="error">
                                        @error('confirm_password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 mt-5">
                                    <button type="submit" class="btn btn-primary">
                                        Change Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
