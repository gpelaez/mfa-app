<?php

namespace App\Services;

use OTPHP\TOTP;

class OTPService 
{
    
    /**
     * Send OTP to a user
     *
     * @param \App\Models\User $user [explicite description]
     *
     * @return void
     */
    public function sendOTP($user)
    {
        $secret = $this->getUserSecret($user);

        // we generate otp with that secret
        $code = $this->generateOTP($secret);

        // constuct your message
        $message =  "Your verification code is $code";

        // TODO
        // You can fire your event to send this message to your user as sms or email
    }
    
    /**
     * We retrieve the secret for creating our code
     *
     * @param \App\Models\User $user [explicite description]
     *
     * @return string
     */
    protected function getUserSecret($user)
    {
        // if user has a secret
        if($user->secret) {
            return $user->secret;
        }

        // user doesn't we create one for the user
        $otp = TOTP::create();
        $secret = $otp->getSecret();

        $user->update(['secret'=> $secret]);

        return $secret;
    }

    
    /**
     * Generates and OTP code from secret and timestamp (time token)
     *
     * @param string $secret [explicite description]
     *
     * @return string
     */
    protected function generateOTP($secret)
    {
        $timestamp = time();

        $otp = TOTP::create($secret);

        $code = $otp->at($timestamp);

        return $code;
    }


    
    /**
     * Verify that the incoming code is valid
     *
     * @param \App\Models\User $user [explicite description]
     * @param string $code [explicite description]
     *
     * @return bool
     */
    public function verifyOTP($user, $code)
    {
        $secret = $this->getUserSecret($user);

       $timestamp = time();

       $otp = TOTP::create($secret);

       $res = $otp->verify($code, $timestamp);

       return $res;
    }
}
