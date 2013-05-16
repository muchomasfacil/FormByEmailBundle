## Using Ajax
Your twig template should be something like (we use jquey in these example, you must include it in your template)
> may be [jquery Form Plugin](http://www.malsup.com/jquery/form/) is usefull

```
<div id="form_by_email_target">
{{ include('MuchoMasFacilFormByEmailBundle:Default:flash.html.twig', {'flash': flash}) }}
 
<form id="form_by_email_form" {{ form_enctype(form) }} method="post" action="{{ path(your_form_controller_action) }}">
    {{ form_widget(form) }}

    <input type="button" value="send">
</form>

</div>
<script>
$(document).ready(function() {
    $('#form_by_email_target').submit(function(event){        
        event.preventDefault();    // prevent form submission

        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
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

