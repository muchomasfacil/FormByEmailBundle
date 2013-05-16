## Passing special params in the controller...
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

