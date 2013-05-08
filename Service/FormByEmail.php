<?php

namespace MuchoMasFacil\FormByEmailBundle\Service;

class FormByEmail
{
    private $container;
    
    private $definitions = array();

    function __construct($container) {
        $this->container = $container;
        $this->definitions =  $this->container->getParameter('mucho_mas_facil_form_by_email.definitions');    
        //var_export($this->definitions);
        //die();
    }
    
    public function sendMail($body, $subject, $definition_to_load = null, $params = array())
    {
        $result = false;
        $params = $this->mergeParams($definition_to_load, $params);        
        $message = \Swift_Message::newInstance()
            ->setFrom($params['sender_setFrom'])
            ->setSender($params['sender_setSender'])
            ->setReturnPath($params['sender_setReturnPath'])
            ->setReplyTo($params['sender_setReplyTo'])        
            ->setSubject($subject)
            ->setBody($body, ($params['is_html'])? 'text/html' : 'text/plain')
            ->setTo($params['recipients_setTo'])
            ->setCc($params['recipients_setCc'])
            ->setBcc($params['recipients_setBcc'])
        ;

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
            $sended_mails = $this->container->get('mailer')->send($message);
            if ($sended_mails) {
                $result = true;
                $flash = array('type' => 'success', 'message' =>'success.email_sent');
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
        $body = $this->container->get('templating')->render($params['template'], array('subject' => $subject, 'form' => $form, 'params' => $params));

        return $this->sendMail($body, $subject, null, $params);
    }

    public function formByEmail($form, $empty_form_data, $subject, $definition_to_load = null, $params = array())
    {
        $result = false;
        $flash = null;

        $request = $this->container->get('request');
        $params = $this->mergeParams($definition_to_load, $params);
        if (is_null($params['locale'])) {
            $params['locale'] = $request->getLocale();
        }        

        $cloned_empty_form = clone $form;
        $cloned_empty_form->setData($empty_form_data);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {                
                //this returns $result & $flash
                extract($this->sendBindedformByEmail(null, $form, $subject, null, $params));      
                $form = $cloned_empty_form;
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
        $default_params = $this->definitions['default'];
        $definition_loaded_params = array();
        if (isset($this->definitions[$definition_to_load])) {
            $definition_loaded_params = $this->definitions[$definition_to_load];
        }
                        
        $params['skip_merge_params'] = true; //this not to run mergeParams more than once

        return array_merge($default_params, $definition_loaded_params, $params);
    }
    
}
