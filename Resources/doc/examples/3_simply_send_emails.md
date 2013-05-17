## Simply send mails
in your app/config/config.yml configure with your settings, for example:
``` yaml
mucho_mas_facil_form_by_email:
    definitions: 
        default:
            sender_setFrom: { info@mycompany.com: 'Info mycompany.com' }
            recipients_setBcc: { info@mycompany.com: 'Info mycompany.com' }
        custom_1:
            sender_setFrom: { info2@mycompany.com: 'Info 2 mycompany.com' }
            recipients_setBcc: { info2@mycompany.com: 'Info 2 mycompany.com' }

```

In your controller

``` php
public function sendMailAction()
{
    
    $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s') . ': my email';           
    
    $body = '
        What ever body you need.        
        What ever body you need.
    ';
    
    //or may be
    //$body = $this->container->get('templating')->render('my_template', array('my_params' ...));

    
    $form_by_email = $this->container->get('mmf_form_by_email');    
    //using the 'default' definition
    $default_form_by_email_answer = $form_by_email->sendEmail($body, $subject);
    //This returns an array like array(
    //    'return'=> true
    //    'flash' => array(
    //        'type' => 'success or error'
    //        , 'message' => 'error message'
    //    )
    //)
    
    //using the 'custom_1' definition
    $default_form_by_email_answer = $form_by_email->sendEmail($body, $subject, 'custom_1');

    // ...    
    
}

```

