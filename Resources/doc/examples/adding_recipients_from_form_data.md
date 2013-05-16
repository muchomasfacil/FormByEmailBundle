## Adding recipients from the form data
in your app/config/config.yml configure with your settings, for example:
``` yaml
mucho_mas_facil_form_by_email:
    definitions: 
        default:
            sender_setFrom: { info@mycompany.com: 'Info mycompany.com' }
        contact:
            recipients_setBcc: { info@mycompany.com: 'Info mycompany.com' }
            skip_fields: [_token, send]
        other:
            recipients_setBcc: { info2@mycompany.com: 'Info2 mycompany.com' }
```


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



In your view template
``` twig
{{ include('MuchoMasFacilFormByEmailBundle:Default:flash.html.twig', {'flash': flash}) }}

{{ form(form_view) }}
```
