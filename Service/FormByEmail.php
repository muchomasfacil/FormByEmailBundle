<?php

namespace MuchoMasFacil\FormByEmailBundle\Service;

class FormByEmail
{
    private $templating;
    
    private $mailer;

    private $default_params = array(
        'template' => 'MuchoMasFacilFormByEmailBundle:Default:formByEmail.txt.twig'
        , 'is_html' => false        
        , 'locale' => null
        , 'translation_domain' => 'messages'
        , 'skip_fields' => array('_token')
        , 'sender_setFrom' => array()
        , 'sender_setSender' => null
        , 'sender_setReturnPath' => null
        , 'sender_setReplyTo' => null
        , 'recipients_setTo' => null
        , 'recipients_setCc' => null
        , 'recipients_setBcc' => null
        , 'recipients_addTo' => array()
        , 'recipients_addCc' => array()
        , 'recipients_addBcc' => array()
        
    );

    function __construct($definitions, $mailer, $templating) {
        var_export($definitions);
        die('hoola');
        $this->templating = $templating;
        $this->mailer = $mailer;
    }
    
    public function sendMail($body, $subject, $definition_to_load = null, $params = array())
    {
        $result = true;

        $params = $this->mergeParams($definition_to_load, $params);

        $message = \Swift_Message::newInstance()
            ->setFrom($params['sender_setFrom'])
            ->setSender($params['sender_setSender'])
            ->setReturnPath($params['sender_setReturnPath'])
            ->setReplyTo($params['sender_setReplyTo'])
            ->setTo($params['recipients_setTo'])
            ->setCc($params['recipients_setCc'])
            ->setBcc($params['recipients_setBcc'])
            ->setSubject($subject)
            ->setBody($body, ($params['is_html'])? 'text/html' : 'text/plain');
        foreach ($params['recipients_addTo'] as $entry) {
            $message->addTo(key($entry), current($entry));
        }        
        foreach ($params['recipients_addCc'] as $entry) {
            $message->addCc(key($entry), current($entry));
        }
        foreach ($params['recipients_addBcc'] as $entry) {            
            $message->addBcc(key($entry), current($entry));
        }

        try {
            $sended_mails = $this->mailer->send($message);
            if ($sended_mails) {
                $result = true;
                $flash = array('type' => 'error', 'message' =>'success.email_sent');
            }
            else{
                $flash = array('type' => 'error', 'message' =>'error.email_not_sent');
            }
        }
        catch(\Exception $e){
            $flash = array('type' => 'error', 'message' =>'error.send_exception' . $e);
        }

        return array('result' => $result ,'flash' => $flash);
    }
    
    public function sendBindedformByEmail($locale, $form, $subject, $definition_to_load = null, $params = array())
    {   
        $params = $this->mergeParams($definition_to_load, $params);     
        if (!is_null($params['locale'])) {
            $params['locale'] = $locale;
        }
        $body = $this->templating->render($params['template'], array('subject' => $subject, 'form' => $form, 'params' => $params));

        return $this->sendMail($body, $subject, null, $params);
    }

    public function formByEmail($request, $form, $subject, $definition_to_load = null, $params = array())
    {
        $result = false;
        $flash = null;

        $params = $this->mergeParams($definition_to_load, $params);
        if (is_null($params['locale'])) {
            $params['locale'] = $request->getLocale();
        }        

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {                
                extract($this->sendBindedformByEmail(null, $form, $subject, null, $params));      
                //$form = ;                            
            }
            else {
                $flash = array('type' => 'error', 'message' =>'error.form_invalid');
            }
        }
            
        return array(
            'result' => $result
            ,'flash' => $flash
            ,'form' => $form->createView()            
        );
    }
    
    private function mergeParams($definition_to_load = null, $params = array())
    {
        if (isset($params['skip_merge_params'])) {
            return $params;
        }
        $default_params = $this->default_params;
        $definition_params = array();
        if (!is_null($definition_to_load)) {
            //load definition params
        }
        $params['skip_merge_params'] = true; //this not to run mergeParams more than once
        return array_merge($this->default_params, $definition_params, $params);
    }
    
}
