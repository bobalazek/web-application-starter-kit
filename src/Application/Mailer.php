<?php

namespace Application;

use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Mailer
{
    protected $app;

    protected $swiftMessageInstance;
    protected $swiftMessageInstanceTemplate;

    /**
     * @param Silex\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Prepares the (swift) email and sends it.
     */
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

    /**
     * Sends the (swift) email
     */
    public function send($swiftMessage = false)
    {
        if (!$swiftMessage) {
            $swiftMessage = $this->getSwiftMessageInstance();
        }

        return $this->app['mailer']->send($swiftMessage);
    }

    /**
     * Short for swift image
     */
    public function image($path)
    {
        return \Swift_Image::fromPath($path);
    }
}
