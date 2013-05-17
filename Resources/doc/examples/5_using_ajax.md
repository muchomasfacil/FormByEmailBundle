## Using Ajax
Your twig template should be something like this (we use jquey in these example, you must include it in your layout template)
> may be [jquery Form Plugin](http://www.malsup.com/jquery/form/) [jquery block plugin](http://www.malsup.com/jquery/block/) are usefull

In your controller, you have two actions:
``` php
// ...

    public function ajaxContactFormAction()
    {
        //define your task entity and form taskType as usual
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $subject = $_SERVER['HTTP_HOST'] . ' ' . date('Y/m/d H:i:s') . ': contact form2';           
        
        return $this->render('YourBundle:Default:ajaxForm.html.twig', $this->container->get('mmf_form_by_email')->sendFormByEmail(new TaskType(), $task, $subject));
    }
    
    public function contactAction()
    {        
        return $this->render('YourBundle:Default:contact.html.twig');
    }
```

in your routing.yml you must define a route for each action. For example:
``` yaml
#...
fbe_contact:
    pattern:  /contact
    defaults: { _controller: YourBundle:Default:contact }        
    
fbe_ajax_contact:
    pattern:  /ajax_contact
    defaults: { _controller: YourBundle:Default:ajaxContact }                
#...
```

Finally your twig templates. One for the YourBundle:Default:ajaxForm.html.twig 
This one should not extend any other as it is rendered either by a the twig render (see next twig template) or by an ajax call...
```
{{ include('MuchoMasFacilFormByEmailBundle:Default:flash.html.twig') }}
 
{{ form(form_view) }}

<script>
$(document).ready(function() {
    $('#form_by_email_target form').submit(function(event){        
        event.preventDefault();    // prevent form submission
        $.ajax('{{ path('fbe_ajax_contact') }}', {
            type: $(this).attr('method'),
            data: $(this).serialize(),
            success: function(data) {
                $('#form_by_email_target').html(data);
            }
        })        
        return false;
    });
});
</script>
```

And the other template YourBundle:Default:contact.html.twig (remember to have jquery in your layout for this to work)
``` 
{% extends '::whatever'%}
...
<div id="form_by_email_target">
    {{ render(controller('MuchoMasFacilTestBundle:Default:ajaxContact')) }}
</div>
...

```

And you have it. Not quite difficult isn't it?
