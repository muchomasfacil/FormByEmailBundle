## Passing special params in the controller...

in your app/config/config.yml configure with your settings, for example:
``` yaml
mucho_mas_facil_form_by_email:
    definitions: 
        default:
            sender_setFrom: { info@mycompany.com: 'Info mycompany.com' }
            recipients_setBcc: { info@mycompany.com: 'Info mycompany.com' }
        my_contact_form:
            recipients_setBcc: { info2@mycompany.com: 'Info2 mycompany.com' }
```

``` php
// in your controller

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
// ...

    public function contactAction()
    {
        //Using a form without a class
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', 'text',  array(
              'constraints' => new Length(array('min' => 3)),
            ))
            ->add('email', 'email')
            ->add('message', 'textarea', array(
                'constraints' => array(
                   new NotBlank(),
                   new Length(array('min' => 3)),
                ),
            ))
            ->add('save', 'submit')
            ->getForm();

        $flash = array();        
        
        $params = array();
        
        $form_by_email = $this->container->get('mmf_form_by_email');

        $form->handleRequest($request);
        if ($form->isValid()) {
            $params['recipients_addBcc'] = array(
                array($form->get('email')->getData() => $form->get('name')->getData())
            );
            $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s') . ': contact form3';           
            //this returns $result & $flash
            extract($form_by_email->sendBindedformByEmail($request->getLocale(), $form, $subject, 'my_contact_form', $params));      
            //the email will be send from info@mycompany.com: 'Info mycompany.com' (default definition)
            // and will be bcc sent to info2@mycompany.com: 'Info2 mycompany.com' (my_contact_form definition) and to the name and email which comes in the form data
            if ($result) {
               $this->get('session')->getFlashBag()->add('flash', $flash);
               return $this->redirect($this->generateUrl('your route'));
            }
        }
        else if ($form->isSubmitted()){
            $flash = array('type' => 'error', 'message' =>'error.form_invalid');
        }

        return $this->render('YourBundle:Default:contact.html.twig', array('flash' => $flash, 'form_view' => $form_view));
    }```

In your view template
``` twig
{{ include('MuchoMasFacilFormByEmailBundle:Default:flash.html.twig') }}

{{ form(form_view) }}
```

