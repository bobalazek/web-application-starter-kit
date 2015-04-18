<?php

namespace Application;

class Mailer
{
    protected $app;

    protected $swiftMessageInstance;
    protected $swiftMessageInstanceTemplate;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /***** Swiftmailer stuff *****/
    public function swiftMessageInitializeAndSend(array $data = array())
    {
        $swiftMessageInstance = \Swift_Message::newInstance();
        $templateData = array(
            'app' => $this->app,
            'user' => $this->app['user'],
        );
        $emailType = isset($data['type'])
            ? $data['type']
            : ''
        ;

        if (isset($data['subject'])) {
            $swiftMessageInstance->setSubject($data['subject']);
        }

        if (isset($data['from'])) {
            $swiftMessageInstance->setFrom($data['from']);
        } else {
            $swiftMessageInstance->setFrom(array(
                $this->app['email'] => $this->app['emailName'],
            ));
        }

        if (isset($data['to'])) {
            $swiftMessageInstance->setTo($data['to']);
        }

        if (isset($data['cc'])) {
            $swiftMessageInstance->setCc($data['cc']);
        }

        if (isset($data['bcc'])) {
            $swiftMessageInstance->setBcc($data['bcc']);
        }

        $templateData['email'] = isset($data['to'])
            ? $data['to']
            : false
        ;
        $templateData['emailType'] = $emailType;
        $templateData['swiftMessage'] = $swiftMessageInstance;

        if (isset($data['templateData'])) {
            $templateData = array_merge(
                $templateData,
                $data['templateData']
            );
        }

        if (isset($data['body'])) {
            $bodyType = isset($data['bodyType'])
                ? $data['bodyType']
                : 'text/html'
            ;
            $isTwigTemplate = isset($data['contentIsTwigTemplate'])
                ? $data['contentIsTwigTemplate']
                : true
            ;

            $swiftMessageBody = $this->app['mailer.css_to_inline_styles_converter'](
                $data['body'],
                $templateData,
                $isTwigTemplate
            );

            $swiftMessageInstance->setBody($swiftMessageBody, $bodyType);
        }

        return $this->app['mailer']->send($swiftMessageInstance);
    }

    public function emailEntityInitialize($swiftMessage, $type = null)
    {
        $trackerImageUrl = false;

        $email = new \Application\Entity\EmailEntity();

        $emailFrom = emailsArrayToString($swiftMessage->getFrom());
        $emailTo = emailsArrayToString($swiftMessage->getTo());

        $email
            ->setSwiftMessageId($swiftMessage->getId())
            ->setSubject($swiftMessage->getSubject())
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setType($type)
        ;

        $this->app['orm.em']->persist($email);
        $this->app['orm.em']->flush();

        return $email;
    }

    /***** Swift Message Instance *****/
    public function getSwiftMessageInstance()
    {
        return $this->swiftMessageInstance;
    }

    public function setSwiftMessageInstance(\Swift_Message $swiftMessageInstance)
    {
        $this->swiftMessageInstance = $swiftMessageInstance;

        return $this;
    }

    public function send($swiftMessage = false)
    {
        if (! $swiftMessage) {
            $swiftMessage = $this->getSwiftMessageInstance();
        }

        return $this->app['mailer']->send($swiftMessage);
    }

    public function image($path)
    {
        return \Swift_Image::fromPath($path);
    }
}
