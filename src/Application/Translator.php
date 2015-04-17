<?php

namespace Application;

class Translator
{
    protected $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function setLocale($locale, $ignoreUntranslated = false)
    {
        $this->app['translator']->setLocale($locale);

        $localeFile = APP_DIR.'/locales/'.$this->app['locale'].'.yml';
        if (file_exists($localeFile)) {
            $this->app['translator']->addResource(
                'yaml',
                $localeFile,
                $this->app['locale']
            );
        }
    }

    public function prepare(\Silex\Application $app, $locale)
    {
        $templatesPath = APP_DIR.'/templates';
        $untranslatedMessagesFile = APP_DIR.'/locales/'.$locale.'_untranslated.yml';

        $extractor = new \Symfony\Bridge\Twig\Translation\TwigExtractor($app['twig']);

        /***** All translations *****/
        $catalogueAll = new \Symfony\Component\Translation\MessageCatalogue($locale);
        $extractor->extract($templatesPath, $catalogueAll);
        $allMessages = $catalogueAll->all('messages');

        // String from controller, controller provider, etc.
        $finder = new \Symfony\Component\Finder\Finder();
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

            if ($fileMessageStrings) {
                foreach ($fileMessageStrings as $fileMessageString) {
                    if (! isset($allMessages[$fileMessageString])) {
                        $allMessages[$fileMessageString] = $fileMessageString;
                    }
                }
            }
        }

        /***** Already translated *****/
        $app['application.translator']->setLocale($locale, $ignoreUntranslated = true);
        $translatedMessages = $app['translator']->getMessages($locale);
        $translatedMessages = $translatedMessages['messages'];

        /***** Untranslated *****/
        $untranslatedMessages = array();

        if ($allMessages) {
            foreach ($allMessages as $singleMessageKey => $singleMessage) {
                if (! isset($translatedMessages[$singleMessageKey])) {
                    $untranslatedMessages[$singleMessageKey] = $singleMessage;
                }
            }
        }

        if (! empty($untranslatedMessages)) {
            $dumper = new \Symfony\Component\Yaml\Dumper();

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
