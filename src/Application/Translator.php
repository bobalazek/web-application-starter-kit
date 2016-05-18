<?php

namespace Application;

use Symfony\Bridge\Twig\Translation\TwigExtractor;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Dumper;
use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Translator
{
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Sets the locale (if it can).
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $app = $this->app;

        $app['translator']->setLocale($locale);

        $localeMessagesFile = APP_DIR.'/locales/'.$app['locale'].'/messages.yml';
        if (file_exists($localeMessagesFile)) {
            $app['translator']->addResource(
                'yaml',
                $localeMessagesFile,
                $app['locale']
            );
        }

        $localeValidatorsFile = APP_DIR.'/locales/'.$app['locale'].'/validators.yml';
        if (file_exists($localeValidatorsFile)) {
            $app['translator']->addResource(
                'yaml',
                $localeValidatorsFile,
                $app['locale'],
                'validators'
            );
        }
    }

    /**
     * Prepares and finds all the translated and untranslated string in tempates and controllers.
     *
     * @param Application $app
     * @param string      $locale
     *
     * @return array
     */
    public function prepare(Application $app, $locale)
    {
        $templatesPath = APP_DIR.'/templates';
        $untranslatedMessagesFile = APP_DIR.'/locales/'.$locale.'/messages_untranslated.yml';

        $extractor = new TwigExtractor($app['twig']);

        /***** All translations *****/
        $catalogueAll = new MessageCatalogue($locale);
        $extractor->extract($templatesPath, $catalogueAll);
        $allMessages = $catalogueAll->all('messages');

        // String from controller, controller provider, etc.
        $finder = new Finder();
        $finder->files()->in(ROOT_DIR.'/src');

        foreach ($finder as $file) {
            $fileMessageStrings = array();

            $filePath = $file->getRealpath();
            $fileContent = file_get_contents($filePath);

            $pregMatch = "#->trans.*\(\s*'(.+?)(?=')#m";
            preg_match_all($pregMatch, $fileContent, $matches);

            $matches = $matches[1];

            if ($matches) {
                foreach ($matches as $match) {
                    $fileMessageStrings[] = $match;
                }
            }

            if (!empty($fileMessageStrings)) {
                foreach ($fileMessageStrings as $fileMessageString) {
                    if (!isset($allMessages[$fileMessageString])) {
                        $allMessages[$fileMessageString] = $fileMessageString;
                    }
                }
            }
        }

        /***** Already translated *****/
        $app['application.translator']->setLocale($locale);
        $translatedMessages = $app['translator']->getCatalogue($locale);
        $translatedMessages = $translatedMessages->all('messages');

        /***** Untranslated *****/
        $untranslatedMessages = array();

        if (!empty($allMessages)) {
            foreach ($allMessages as $singleMessageKey => $singleMessage) {
                if (!isset($translatedMessages[$singleMessageKey])) {
                    $untranslatedMessages[$singleMessageKey] = $singleMessage;
                }
            }
        }

        if (!empty($untranslatedMessages)) {
            $dumper = new Dumper();

            $yaml = $dumper->dump($untranslatedMessages, 1);

            if (file_exists($untranslatedMessagesFile)) {
                unlink($untranslatedMessagesFile);
            }

            file_put_contents($untranslatedMessagesFile, $yaml);
        }

        return array(
            'allMessages' => $allMessages,
            'translatedMessages' => $translatedMessages,
            'untranslatedMessages' => $untranslatedMessages,
        );
    }
}
