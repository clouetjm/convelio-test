<?php

namespace App\Manager;

require_once __DIR__ . '/TemplateManagerInterface.php';

use ApplicationContext;
use DestinationRepository;
use Quote;
use QuoteRepository;
use SiteRepository;
use Template;

class TemplateManager implements TemplateManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getTemplateComputed(Template $template, array $data)
    {
        if (!$template) {
            throw new \RuntimeException('No template given.');
        }

        $template = clone($template);
        $template->subject = $this->computeText($template->subject, $data);
        $template->content = $this->computeText($template->content, $data);

        return $template;
    }

    private function computeText($text, array $data)
    {
        $applicationContext = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote) {
            $quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $site = SiteRepository::getInstance()->getById($quote->siteId);
            $quoteDestination = DestinationRepository::getInstance()->getById($quote->destinationId);

            if (strpos($text, '[quote:destination_link]') !== false) {
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            }

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($quoteFromRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($quoteFromRepository),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]', $quoteDestination->countryName, $text);
        }

        $replace = '';

        if (isset($destination)) {
            $replace = $site->url . '/' . $destination->countryName . '/quote/' . $quoteFromRepository->id;
        }

        $text = str_replace('[quote:destination_link]', $replace, $text);

        /*
         * USER [user:*]
         */
        $user = (isset($data['user']) and ($data['user'] instanceof User)) ? $data['user'] : $applicationContext->getCurrentUser();
        if ($user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }
}
