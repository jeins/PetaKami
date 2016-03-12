<?php


namespace PetaKami\Auth;

use PetaKami\Constants\PKConst;
use Phalcon\Di\Injectable;

class Mail extends Injectable
{

    public function send($to, $subject, $fullName, $hash)
    {
        $mailConfig = $this->di->get(PKConst::CONFIG)->mail;

        $transport = \Swift_SmtpTransport::newInstance(
            $mailConfig->smtp->server,
            $mailConfig->smtp->port,
            $mailConfig->smtp->security
        )
            ->setUsername($mailConfig->smtp->username)
            ->setPassword($mailConfig->smtp->password);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($to)
            ->setFrom($mailConfig->fromEmail)
            ->setBody($this->template($hash, $fullName), 'text/html')
        ;

        return $mailer->send($message);
    }

    private function template($hash, $fullName){
        $url = $this->di->get(PKConst::CONFIG)->clientHostName . '/#/active/' . $hash;

        return '
            <p style="color:#000;font-size: 16px;line-height:24px;font-family:\'HelveticaNeue\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;font-weight:normal;">

				<h2 style="font-size: 14px;font-family:\'HelveticaNeue\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;">Hallo, '. $fullName. '</h2>
				<p style="font-size: 13px;line-height:24px;font-family:\'HelveticaNeue\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;">You\'ve successfully created a PetaKami account. To activate it, please click below to verify your email address.

				<br>
				<br>
				<a style="background:#E86537;color:#fff;padding:10px" href="'. $url .'">Confirm</a>

				<br>
				<br>
				PetaKami
				<br>
			</p>
        ';
    }
}