<?php


namespace PetaKami\Auth;

use PetaKami\Constants\PKConst;
use SendGrid;

class Mail
{

    protected $sendgrid;

    public function __construct()
    {
        $this->sendgrid = new SendGrid(PKConst::SENDGRID_API);
    }

    public function send($to, $key)
    {

    }
}