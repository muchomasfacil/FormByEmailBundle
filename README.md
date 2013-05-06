# FormByEmailBundle
Simplifies sending your Symfony2 forms by email

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
composer.phar require muchomasfacil/InPageEditBundle dev-master
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

## Using FormByEmailBundle
``` yaml
// make sure you've imported the Request namespace above the class
use Symfony\Component\HttpFoundation\Request;
// ...

public function contactAction(Request $request)
{
    $defaultData = array('message' => 'Type your message here');
    $form = $this->createFormBuilder($defaultData)
        ->add('name', 'text')
        ->add('email', 'email')
        ->add('message', 'textarea')
        ->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
        }

    // ... render the form
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
    <td></td>
</tr>
<tr>    
    <td>is_html        
    <td>boolean</td>
    <td>false</td>
    <td></td>
</tr>
<tr>    
    <td>locale
    <td>string</td>
    <td>~</td>
    <td></td>
</tr>
<tr>    
    <td>translation_domain
    <td>string</td>
    <td>messages</td>
    <td></td>
</tr>
<tr>    
    <td>skip_fields
    <td>array</td>
    <td>[_token]</td>
    <td></td>
</tr>
<tr>    
    <td>sender_setFrom
    <td>array</td>
    <td>[]</td>
    <td></td>
</tr>
<tr>    
    <td>sender_setSender
    <td>email address</td>
    <td>~</td>
    <td></td>
</tr>
<tr>    
    <td>sender_setReturnPath
    <td>email address</td>
    <td>~</td>
    <td></td>
</tr>
<tr>    
    <td>sender_setReplyTo 
    <td>email address</td>
    <td>~</td>
    <td></td>
</tr>
<tr>    
    <td>recipients_setTo
    <td>array</td>
    <td>[]</td>
    <td></td>
</tr>
<tr>    
    <td>recipients_setCc
    <td>array</td>
    <td>[]</td>
    <td></td>
</tr>
<tr>    
    <td>recipients_setBcc
    <td>array</td>
    <td>[]</td>
    <td></td>
</tr>
<tr>    
    <td>recipients_addTo
    <td>array</td>
    <td>[]</td>
    <td></td>
</tr>
<tr>    
    <td>recipients_addCc
    <td>array</td>
    <td>[]</td>
    <td></td>
</tr>
<tr>    
    <td>recipients_addBcc
    <td>array</td>
    <td>[]</td>
    <td></td>
</tr>    
</table>


## TODO
traducir mensajes de error (hacer un template flash...)
DEFAULT FORM DATA??
