<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\VerifyUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\UserToken;
use Exception;
use App\Models\PasswordReset;


class AuthController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(){
        $title = "Login";
        return view('auth.login', compact('title'));
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (DB::table('users')->where('email', $request->email)->doesntExist()) {
            return back()->withInput()->withErrors([
                'message' => 'Email id is not registered.',
            ]);
        } else {
            $user = User::where('email', $request->email)->first();
            if ($user->email_verified_at) { // verified user
                $credentials = $request->only('email', 'password');
                if (Auth::attempt($credentials)) {
                    $user = Auth::user();
                    return redirect()->intended('dashboard');
                } else {
                    return back()->withInput()->withErrors([
                        'message' => 'The provided credentials do not match with our records.',
                    ]);
                }
            } else { // email is not verified
                // generate a new token and send email for verification
                $token = Str::random(64);
                UserToken::UpdateOrCreate([
                    'user_id' => $user->id,
                ],
                [
                    'token' => $token,
                ]);

                Mail::send('emails.verify_email', ['token' => $token], function ($message) use ($request) {
                    $message->to($request->email);
                    $message->subject('Email Verification Mail');
                });
                return back()->withInput()->withErrors([
                    'message' => 'Your email is not verified yet. Please check email sent to ' . $request->email . ' and click on verify email link.',
                ]);
            }
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */

    public function register()
    {
        $title = "Register";
        return view('auth.register', compact('title'));
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegister(Request $request)
    {
        $request->validate(
            [
                'name'           => 'required|max:255',
                'email'          => 'required|email|unique:users|max:255',
                'password'       => 'required|min:6',
                'confirm_password'  => 'required|same:password',
            ]
        );

        try {
            $user = new User;
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->password =  Hash::make($request->password);
            $user->save();

            // generate a token
            $token = Str::random(64);
            UserToken::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);

            Mail::send('emails.verify_email', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Email Verification Mail');
            });

            return redirect("register")->withSuccess('A verification email is sent to ' . $request->email . ', please click the link in the email to verify your email.');
        } catch (Exception $e) {
            return redirect("register")->withErrors('Some Error occurred, please try later');
        }
    }

   
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function verifyEmail($token)
    {
        $verifyUser = UserToken::where('token', $token)->first();
        if (!is_null($verifyUser)) {
            $verifyUser->user->email_verified_at = Carbon::now();
            $verifyUser->user->save();

            // delete token
            $verifyUser->delete();
            $user = User::find($verifyUser->user_id);
            Auth::login($user);
            $message = "Email verified Successfully";

            session(['user_name' => $user->name, 'user_id' => $user->id, 'user_email' => $user->email]);
            return redirect('dashboard')->withSuccess($message);
        } else {
            $message = 'Token Error: Email can not be verified.';
            return redirect()->route('page.error')->withError($message);
        }
    }

    public function showErrorPage()
    {
        $title = "Error";
        return view('error_page', compact('title'));
    }
    public function changePasswordForm()
    {
        $title = "Change Password";
        return view('Auth.change_password', compact('title'));
    }

    public function changePasswordPost(Request $request)
    {
        $request->validate([
            'current_password'  => 'required',
            'new_password'      => 'required|min:6',
            'confirm_password' => 'required|same:new_password|min:6',
        ]);

        if (!(Hash::check($request->current_password, Auth::user()->password))) {

            return redirect()->back()->with("error", "Your current password does not match with the password you entered.");
        }

        if (strcmp($request->current_password, $request->new_password) == 0) {
            // Current password and new password same
            return redirect()->back()->with("error", "New Password cannot be same as your current password.");
        }

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->new_password);
        $user->save();
        return redirect()->back()->with("success", "Password successfully changed!");
    }

    public function forgetPasswordForm()
    {
        $title = "Forgot Password";
        return view('auth.forget_password', compact('title'));
    }

    public function forgetPasswordPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);

        PasswordReset::updateOrCreate([
            'email' => $request->email,
            ],
            [
            'token' => $token,
            ]);

        Mail::send('emails.forget_password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return back()->with('message', 'We have e-mailed your password reset link!');
    }

    public function resetPasswordForm($token)
    {
        $title = "Reset Password";
        return view('auth.reset_password_form', ['token' => $token], compact('title'));
    }

    public function resetPasswordPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'new_password'      => 'required|min:6',
            'confirm_password' => 'required|same:new_password|min:6',
        ]);

        $verifyToken = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$verifyToken) {
            return back()->withInput()->with('error', 'Invalid Token!');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->new_password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('login')->with('message', 'Your password is Reset. Please login with new password!');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect('login');
    }
}
