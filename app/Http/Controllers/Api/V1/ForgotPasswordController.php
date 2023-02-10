<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        // validate the email

        // generate a random code

        // send the code by email

        // return a response
    }

    public function checkResetCode(Request $request)
    {
        // validate the code and the password

        // check if the code is not expired

        // update the user's password

        // delete the code

        // return a response
    }
}
