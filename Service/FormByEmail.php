<?php

namespace MuchoMasFacil\FormByEmailBundle\Service;



class FormByEmail
{
    private $templating;
    private $mailer;
        
    private function customSendEmail($body, $subject, $is_html = false, $get_default_recipients_and_sender_details = true, $yml_recipients_and_sender_details = '')
    {
        $recipients_and_sender_details = Yaml::parse($yml_recipients_and_sender_details);
        if ($get_default_recipients_and_sender_details) {
            $defaults = $this->container->getParameter('swiftmailer_defaults');
            $recipients_and_sender_details = array_replace($defaults, ($recipients_and_sender_details)? $recipients_and_sender_details : array());
        }
        $logger = $this->get('logger');
        $logger->info(print_r($recipients_and_sender_details, true));
                
        echo "Contenido de default to:" . $recipients_and_sender_details['default_to'];
                
        $message = \Swift_Message::newInstance()
          ->setFrom($recipients_and_sender_details['default_from'])
          ->setSender($recipients_and_sender_details['default_sender'])
          ->setReturnPath($recipients_and_sender_details['default_return_path'])
          ->setReplyTo($recipients_and_sender_details['default_reply_to'])
          ->setTo($recipients_and_sender_details['default_to'])
          ->setCc($recipients_and_sender_details['default_cc'])
          ->setBcc($recipients_and_sender_details['default_bcc'])
          ->setSubject($subject)
          ->setBody($body, ($is_html)? 'text/html' : 'text/plain');

        // TODO make this try catch work actually it is intercepted by symfony
        try {
            $sended_mails = $this->get('mailer')->send($message, $failures);
           // echo "entra en send mail";
                //$sended_mails =1;
            $exception = false;
        }
        catch(Exception $e){
            $exception = $e->getMessage();
            $sended_mails = 0;
            $failures = array();
        }
        return array($sended_mails, $failures, $exception);
    }

    private function customSendFormByEmail($form, $subject, $is_html = false, $template = null, $skip_fields = array('_token'), $get_default_recipients_and_sender_details = true, $yml_recipients_and_sender_details = '')
    {
        if (!$template) {
            $template = $this->getTemplateNameByDefaults('Includes/form_by_email', ($is_html)? 'html' : 'txt' );
        }
        $body = $this->renderView($template, array('subject' => $subject, 'form' => $form, 'skip_fields' => $skip_fields));
        return $this->customSendEmail($body, $subject, $is_html, $get_default_recipients_and_sender_details, $yml_recipients_and_sender_details);
    }

    private function genericFormByEmail(Request $request, $source_form, $subject, $is_html = false, $template = null, $skip_fields = array('_token'), $get_default_recipients_and_sender_details = true, $yml_recipients_and_sender_details = '')
    {
        $form = $source_form;
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                date_default_timezone_set('Europe/Madrid');
                list($sended_mails, $failures, $exception) = $this->customSendFormByEmail(
                    $form,
                    $subject,
                    $is_html,
                    $template,
                    $skip_fields,
                    $get_default_recipients_and_sender_details,
                    $yml_recipients_and_sender_details
                );                
                if ($sended_mails) {
                    $messages[] = array('class' => 'success', 'text' => 'El mensaje se ha enviado con éxito');
                    unset($form);
                    $form = $source_form;
                }
                else {
                    $messages[] = array('class' => 'error', 'text' => 'Se produjo un error al enviar el formulario. Inténtalo más tarde.');
                }
            }
            else {
                $messages[] = array('class' => 'error', 'text' => 'Corrija los errores del formulario');
            }
        }//method post

        if (isset($messages)) {
            $this->render_vars['messages'] = $messages;
        }

        $this->render_vars['form'] = $form->createView();
        return $this->render_vars;
    }      
    
}
