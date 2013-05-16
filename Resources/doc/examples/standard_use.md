## Standard use
in your app/config/config.yml configure with your settings, for example:
``` yaml
mucho_mas_facil_form_by_email:
    definitions: 
        default:
            sender_setFrom: { info@mycompany.com: 'Info mycompany.com' }
            recipients_setBcc: { info@mycompany.com: 'Info mycompany.com' }
            skip_fields: [_token, save]

```

create your entity (as in http://symfony.com/doc/2.3/book/forms.html)
``` php
namespace ...;

class Task
{
    protected $task;

    protected $dueDate;

    public function getTask()
    {
        return $this->task;
    }
    public function setTask($task)
    {
        $this->task = $task;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }
    public function setDueDate(\DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;
    }
}
```
and add your validation as usual (http://symfony.com/doc/2.3/book/forms.html#form-validation)

In your controller
``` php
// ...
use Symfony\Component\HttpFoundation\Request;
use MuchoMasFacil\TestBundle\Entity\Task;
// ...

    public function indexAction()
    {
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($task)
            ->add('task', 'text')
            ->add('dueDate', 'date')
            ->add('save', 'submit')
            ->getForm();
        
        $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s') . ': contact form';           
        
        $form_by_email = $this->container->get('mmf_form_by_email');
        //this, returns, $result, $flash, $form_view
        extract($form_by_email->sendFormByEmail($form, $task, $subject));
        //if you want to redirect on success uncomment this
        //if ($result) {
        //    $this->get('session')->getFlashBag()->add('flash', $flash);
        //    return $this->redirect($this->generateUrl(...your success url...));
        //}

        return $this->render('MuchoMasFacilTestBundle:Default:index.html.twig', array('flash' => $flash, 'form_view' => $form_view));
    }
```

In your view template
``` twig
{{ include('MuchoMasFacilFormByEmailBundle:Default:flash.html.twig') }}

{{ form(form_view) }}
```
