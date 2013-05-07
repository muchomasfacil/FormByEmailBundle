# FormByEmailBundle
Nothing new under the sun but this bundle, greatly simplifies sending your Symfony2 forms by email

## Install bundle with composer.json
Add to your composer.json in the repositories (create it if not exist) section:

``` json
        {
            "type": "vcs",
            "url": "git@github.com:muchomasfacil/FormByEmailBundle.git"
        }
```

You must add this entries to your app/AppKernel.php
``` php
            new MuchoMasFacil\FormByEmailBundle\MuchoMasFacilFormByEmailBundle(),
```

Enable translation in your app/config/config.yml (optional but recommended)
``` yaml
# ...
framework:
# ...
    translator:      { fallback: %locale% }
# ...    
```

Finally run on your project (take care of "minimum-stability" if necesary)
``` bash
composer.phar require muchomasfacil/FormByEmailBundle dev-master
```
## Configure
In your app/config/config.yml you can:
``` yaml
mucho_mas_facil_form_by_email:
    definitions: 
        # overwrite bundle default configuration
        default:
            template: MuchoMasFacilFormByEmailBundle:Default:formByEmail.html.twig
            is_html: true        
            recipients_setBcc: [{ info@mycompany.com: 'My company info'}, mypersonal@gmail.com: 'My personal mail'}]
        # create your custom
        my_txt_contact: 
            template: MuchoMasFacilFormByEmailBundle:Default:formByEmail.txt.twig
            is_html: false
            recipients_addBcc: [ {mypersonal2@yahoo.es: 'Another recipient for the my_txt_contact form'} ]
        
```

When you call
``` php
$render_params = $form_by_email->formByEmail($form, $default_data, $subject, $definition_to_load, $params);
```
The final configuration used is formed by:
- getting 'default' definition (you can overwrite it in your app/config/config.yml)
- merging previous with definition set by the value in $definition_to_load
- merging previous result with values in $params array

> If you need to pass custom params to your email template add them in $params

## Using FormByEmailBundle
in your twig template add
```
{{ include('MuchoMasFacilFormByEmailBundle:Default:flash.html.twig', {'flash': flash}) }}
 
<form {{ form_enctype(form) }} method="post">
    {{ form_widget(form) }}

    <input type="submit" />
</form>
```
Of course you can customize the way flashes are included or form is rendered

And in your controller:

### Minimalistic
You have your alredy defined you entity, form class and validation
and you have overwrite in your app/config/config.yml you recipients.setBcc to something like
``` yaml
mucho_mas_facil_form_by_email:
    definitions: 
        # overwrite bundle default configuration
        default:
            recipients_setBcc: [{ info@mycompany.com: 'My company info'}, mypersonal@gmail.com: 'My personal mail'}]
```

and then in your controller

``` php
// make sure you've imported the Request namespace above the class
use Symfony\Component\HttpFoundation\Request;
// ...

public function contactAction(Request $request)
{
    $my_entity = ...;
    $form = $this->createForm(new $my_entity_form_class(), $my_entity);
    
    $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s').': '. 'contact form';       
        
    return $this->render('myBundle:myController:MyTemplate.html.twig', $this->container->get('mmf_form_by_email')->formByEmail($form, $my_entity, $subject));
}
```

### Normal use
``` php
// make sure you've imported the Request namespace above the class
use Symfony\Component\HttpFoundation\Request;
// ...

public function contactAction(Request $request)
{
    $my_entity = ...;
    $form = $this->createForm(new $my_entity_form_class(), $my_entity);
    
    $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s').': '. 'contact form';    

    $definition_to_load = 'my_definition'; //you can define it in your app/config/config.yml

    //your custom params, for example
    $params['recipients.addBcc'] = array(
        array('address2@mycompany.com'=> 'Address 2 name')
        ,array('address3@mycompany.com'=> 'Address 3 name')
    );

    $form_by_email = $this->container->get('mmf_form_by_email');
    list($result, $flash, $form) = $form_by_email->formByEmail($form, $my_entity, $subject, $definition_to_load, $params);
    return $this->render('MuchoMasFacilTestBundle:Default:index.html.twig', array('flash' => $flash, 'form' => $form, ...));
}
```

### Using a form without a class (adapting Symfony book example)
``` php
// make sure you've imported the Request namespace above the class
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
// ...

public function contactAction(Request $request)
{
    $defaultData = array('message' => 'Type your message here');
    $form = $this->createFormBuilder($defaultData)
        ->add('name', 'text', , array(
          'constraints' => new Length(array('min' => 3)),
        ))
        ->add('email', 'email')
        ->add('message', 'textarea', array(
            'constraints' => array(
               new NotBlank(),
               new Length(array('min' => 3)),
            ),
        ))
        ->getForm();

}
```

### More control about the form treatment
``` php
// make sure you've imported the Request namespace above the class
use Symfony\Component\HttpFoundation\Request;
// ...

public function contactAction(Request $request)
{
    $my_entity = ...;
    $form = $this->createForm(new $my_entity_form_class(), $my_entity);

    $subject = ...;
    
    $params = array();



    if ($request->getMethod() == 'POST') {
        $form->bindRequest($request);
        if ($form->isValid()) {                
            $form_by_email = $this->container->get('mmf_form_by_email');
            //this returns $result & $flash
            //so can either:
            //extract($form_by_email->sendBindedformByEmail($request->getLocale(), $form, $subject, null, $params));      
            //or
            list($result, $flash) = $form_by_email->sendBindedformByEmail($request->getLocale(), $form, $subject, null, $params);
            if ($result) {
                // may be you want to add $flash to your session Flash Messages
                // http://symfony.com/doc/current/book/controller.html#flash-messages
                return $this->redirect($this->generateUrl('task_success'));
            }            
        }
        else {
            $flash = array('type' => 'error', 'message' =>'error.form_invalid');
        }
    }
    // ...    
}
```

### You can even simply send an email
``` php
public function sendMailAction()
{
    sendMail($body, $subject, null, $params = array())
    $subject = ...;
    
    $body = '
        ...
    ';

    $form_by_email = $this->container->get('mmf_form_by_email');    

    $params = array(
        'is_html' => true
        ,'recipients.setBcc' => array('mycompany@mydomain.com')
    );
    //you could have even written this params in a definition 'my_definition' and then call
    //list($result, $flash) = $form_by_email->sendMail($body, $subject, 'my_definition');    
    list($result, $flash) = $form_by_email->sendMail($body, $subject, null, $params);
    
    // ...    
}
```

## Parameters reference

<table>
    <tr>
        <th>Param</th>
        <th>Type</th>
        <th>Default</th>
        <th>Reference</th>
    </tr>

<tr>
    <td>template</td>
    <td>string<td>
    <td>MuchoMasFacilFormByEmailBundle:Default:formByEmail.txt.twig</td>
    <td>Email render template (take default template as a reference for your custom templates)</td>
</tr>
<tr>    
    <td>is_html        
    <td>boolean</td>
    <td>false</td>
    <td>If sets email body content-type to text/html or text/plain</td>
</tr>
<tr>    
    <td>locale
    <td>string</td>
    <td>~</td>
    <td>By default will use request locale. If set forces locale</td>
</tr>
<tr>    
    <td>translation_domain
    <td>string</td>
    <td>messages</td>
    <td>Bundle translation domain [http://symfony.com/doc/current/book/translation.html]</td>
</tr>
<tr>    
    <td>skip_fields
    <td>array</td>
    <td>[_token]</td>
    <td>Form fields that will not show in the email</td>
</tr>
<tr>    
    <td>sender_setFrom
    <td>array</td>
    <td>[]</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#setting-the-from-address]</td>
</tr>
<tr>    
    <td>sender_setSender
    <td>string: single email address</td>
    <td>~</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#setting-the-sender-address]</td>
</tr>
<tr>    
    <td>sender_setReturnPath
    <td>string: single email address</td>
    <td>~</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#setting-the-return-path-bounce-address]</td>
</tr>
<tr>    
    <td>sender_setReplyTo 
    <td>string: single email address</td>
    <td>~</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#setting-the-return-path-bounce-address]</td>
</tr>
<tr>    
    <td>recipients_setTo
    <td>array</td>
    <td>[]</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message]</td>
</tr>
<tr>    
    <td>recipients_setCc
    <td>array</td>
    <td>[]</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message]</td>
</tr>
<tr>    
    <td>recipients_setBcc
    <td>array</td>
    <td>[]</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message]</td>
</tr>
<tr>    
    <td>recipients_addTo
    <td>array</td>
    <td>[]</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message]</td>
</tr>
<tr>    
    <td>recipients_addCc
    <td>array</td>
    <td>[]</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message]</td>
</tr>
<tr>    
    <td>recipients_addBcc
    <td>array</td>
    <td>[]</td>
    <td>Look at SwiftMailer reference [http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message]</td>
</tr>    
</table>

## TODO
Check everything works as expected
