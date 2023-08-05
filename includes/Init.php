<?php
namespace CodeIT\SMTP;

class Init 
{
    private array $options = array();

    function __construct()
    {
        $this->options = get_option('codeit-smtp');
        
        if( isset( $this->options['smtp-enable'] ) && $this->options['smtp-enable'] === 'on' ) 
        {
            add_action('phpmailer_init', array( $this, 'phpmailer_init' ), 10, 1);
        }
    }

    function phpmailer_init(\PHPMailer\PHPMailer\PHPMailer $mail)
    {
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Timeout = 15;

        if( $this->options['smtp-security'] === 'SSL' ) 
        {
            $mail->SMTPSecure = 'ssl';
        }
        elseif( $this->options['smtp-security'] === 'TLS' )
        {
            $mail->SMTPSecure = 'tls';
        }
        else
        {
            $mail->SMTPSecure = '';
        }
        
        $mail->SMTPAuth = true;
        $mail->Host     = $this->options['smtp-host'];
        $mail->Port     = $this->options['smtp-port'];
        $mail->Username = $this->options['smtp-username'];
        $mail->Password = $this->options['smtp-password'];
    }
}