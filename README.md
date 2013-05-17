# FormByEmailBundle

> Note this bundle works only with symfony >= 2.3.x

Nothing new under the sun. This bundle just tryes to simplify sending your Symfony2 forms by email.
It will help if you need to:
- send forms by email (even if you use ajax forms)
- just send emails

setting sender and recipient and other data in an easy way

Here you have some [examples](./Resources/doc/examples).

Fell free to send us your opinion, or issues.

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
        # create your custom definitions
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

## Parameters reference

Take a look at default values in [./Resources/config/services.yml](./Resources/config/services.yml)

<table>
    <tr>
        <th>Param</th>
        <th>Type</th>
        <th>Reference</th>
    </tr>

<tr>
    <td>template</td>
    <td>string</td>
    <td>Email render template (take default template as a reference for your custom templates)</td>
</tr>
<tr>    
    <td>is_html        
    <td>boolean</td>
    <td>If sets email body content-type to text/html or text/plain</td>
</tr>
<tr>    
    <td>locale
    <td>string</td>
    <td>By default will use request locale. If set forces locale</td>
</tr>
<tr>    
    <td>translation_domain
    <td>string</td>
    <td>Bundle translation domain http://symfony.com/doc/current/book/translation.html</td>
</tr>
<tr>    
    <td>skip_fields
    <td>array</td>
    <td>Form fields that will not show in the email</td>
</tr>
<tr>    
    <td>sender_setFrom
    <td>array</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#setting-the-from-address</td>
</tr>
<tr>    
    <td>sender_setSender
    <td>string: single email address</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#setting-the-sender-address</td>
</tr>
<tr>    
    <td>sender_setReturnPath
    <td>string: single email address</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#setting-the-return-path-bounce-address</td>
</tr>
<tr>    
    <td>sender_setReplyTo 
    <td>string: single email address</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#setting-the-return-path-bounce-address</td>
</tr>
<tr>    
    <td>recipients_setTo
    <td>array</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message</td>
</tr>
<tr>    
    <td>recipients_setCc
    <td>array</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message</td>
</tr>
<tr>    
    <td>recipients_setBcc
    <td>array</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message</td>
</tr>
<tr>    
    <td>recipients_addTo
    <td>array</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message</td>
</tr>
<tr>    
    <td>recipients_addCc
    <td>array</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message</td>
</tr>
<tr>    
    <td>recipients_addBcc
    <td>array</td>
    <td>Look at SwiftMailer reference http://swiftmailer.org/docs/messages.html#adding-recipients-to-your-message</td>
</tr>    
</table>

## TODO
- test if comunity finds it useful
- Tests
- Publish on packagist, updating documentation
- Travis integration
