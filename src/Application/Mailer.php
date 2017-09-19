<?php

namespace Application;

use Silex\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class Mailer
{
    protected $app;
    protected $swiftMessageInstance;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Prepares the (swift) email and sends it.
     *
     * @param array $data Swiftmailer data
     *
     * @return int Number of successfully sent emails
     *
     * @throws \Exception If subject or recipient (to) are not specified
     */
    public function swiftMessageInitializeAndSend(array $data = [])
    {
        $swiftMessageInstance = \Swift_Message::newInstance();

        if (!isset($data['subject'])) {
            throw new \Exception('You need to specify a subject');
        }

        if (!isset($data['to'])) {
            throw new \Exception('You need to specify a recipient');
        }

        $from = isset($data['from'])
            ? $data['from']
            : $this->app['email']
        ;
        $to = $data['to'];

        $swiftMessageInstance
            ->setSubject($data['subject'])
            ->setTo($to)
            ->setFrom($from)
        ;

        if (isset($data['cc'])) {
            $swiftMessageInstance->setCc($data['cc']);
        }

        if (isset($data['bcc'])) {
            $swiftMessageInstance->setBcc($data['bcc']);
        }

        $templateData = [
            'app' => $this->app,
            'user' => $this->app['user'],
            'email' => $to,
            'swiftMessage' => $swiftMessageInstance,
        ];

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

    /**
     * @return \Swift_Message
     */
    public function getSwiftMessageInstance()
    {
        return $this->swiftMessageInstance;
    }

    /**
     * @param \Swift_Message $swiftMessageInstance
     *
     * @return Mailer
     */
    public function setSwiftMessageInstance(\Swift_Message $swiftMessageInstance)
    {
        $this->swiftMessageInstance = $swiftMessageInstance;

        return $this;
    }

    /**
     * Sends the (swift) email.
     *
     * @param mixed $swiftMessage
     */
    public function send($swiftMessage = false)
    {
        if (!$swiftMessage) {
            $swiftMessage = $this->getSwiftMessageInstance();
        }

        return $this->app['mailer']->send($swiftMessage);
    }

    /**
     * Short for swift image.
     *
     * @param string $path
     */
    public function image($path)
    {
        return \Swift_Image::fromPath($path);
    }
}
