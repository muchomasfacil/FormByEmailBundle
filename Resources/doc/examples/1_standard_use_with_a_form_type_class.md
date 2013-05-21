## Standard use with a form type class
in your app/config/config.yml configure with your settings, for example:
``` yaml
mucho_mas_facil_form_by_email:
    definitions: 
        default:
            sender_setFrom: { info@mycompany.com: 'Info mycompany.com' }
            recipients_setBcc: { info@mycompany.com: 'Info mycompany.com' }
            skip_fields: [_token, send]

```

create your entity as usual (as in http://symfony.com/doc/2.3/book/forms.html)
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

Create your form type class as usual
``` php
namespace ...;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('task');
        $builder->add('dueDate', 'date');
        $builder->add('send', 'submit', array('label'=>'Send'));
    }

    public function getName()
    {
        return 'task';
    }
}
```

And in your controller

``` php
// ...
use Symfony\Component\HttpFoundation\Request;
use MuchoMasFacil\TestBundle\Entity\Task;
use MuchoMasFacil\TestBundle\Form\TaskType;
// ...

    public function indexAction()
    {
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createForm(new TaskType(), $task);

        $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s') . ': contact form2';           
        
        $form_by_email = $this->container->get('mmf_form_by_email');
        //this, returns, $result, $flash, $form_view
        extract($form_by_email->sendFormByEmail($form, $task, $subject));
        //if you want to redirect on success uncomment this
        //if ($result) {
        //    $this->get('session')->getFlashBag()->add('flash', $flash);
        //    return $this->redirect($this->generateUrl(...your success url...));
        //}

        return $this->render('YourBundle:Default:index.html.twig', array('flash' => $flash, 'form_view' => $form_view));
    }
```

or simpler
``` php
// ...
use Symfony\Component\HttpFoundation\Request;
use MuchoMasFacil\TestBundle\Entity\Task;
use MuchoMasFacil\TestBundle\Form\TaskType;
// ...

    public function indexAction()
    {
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s') . ': contact form2';           
        
        $form_by_email = $this->container->get('mmf_form_by_email');
        //this, returns, $result, $flash, $form_view
        extract($form_by_email->sendFormByEmail(new TaskType(), $task, $subject));
        //if you want to redirect on success uncomment this
        //if ($result) {
        //    $this->get('session')->getFlashBag()->add('flash', $flash);
        //    return $this->redirect($this->generateUrl(...your success url...));
        //}

        return $this->render('YourBundle:Default:index.html.twig', array('flash' => $flash, 'form_view' => $form_view));
    }
```

or minimalistc
``` php
// ...

    public function indexAction()
    {
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s') . ': contact form2';           
        
        return $this->render('YourBundle:Default:index.html.twig', $this->container->get('mmf_form_by_email')->sendFormByEmail(new TaskType(), $task, $subject));
    }
```


In your view template
``` twig
{{ include('MuchoMasFacilFormByEmailBundle:Default:flash.html.twig') }}

{{ form(form_view) }}
```
