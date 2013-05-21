## Using a form without a class (adapting Symfony book example)
in your app/config/config.yml configure with your settings, for example:
``` yaml
mucho_mas_facil_form_by_email:
    definitions: 
        default:
            sender_setFrom: { info@mycompany.com: 'Info mycompany.com' }
            recipients_setBcc: { info@mycompany.com: 'Info mycompany.com' }

```

``` php
// in your controller

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
// ...

    public function contactAction()
    {
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
        
        $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s') . ': contact form';           
        
        $form_by_email = $this->container->get('mmf_form_by_email');
        //this, returns, $result, $flash, $form_view
        extract($form_by_email->sendFormByEmail($form, $defaultData, $subject));
        //if you want to redirect on success uncomment this
        //if ($result) {
        //    $this->get('session')->getFlashBag()->add('flash', $flash);
        //    return $this->redirect($this->generateUrl(...your success url...));
        //}
        return $this->render('YourBundle:Default:contact.html.twig', array('flash' => $flash, 'form_view' => $form_view));
    }
```

In your view template
``` twig
{{ include('MuchoMasFacilFormByEmailBundle:Default:flash.html.twig') }}

{{ form(form_view) }}
```
