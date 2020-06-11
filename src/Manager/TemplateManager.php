<?php

namespace App\Manager;

require_once __DIR__ . '/TemplateManagerInterface.php';

use ApplicationContext;
use DestinationRepository;
use Quote;
use QuoteRepository;
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
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if (null !== $quote) {
            $quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $quoteDestination = DestinationRepository::getInstance()->getById($quote->destinationId);

            if (strpos($text, '[quote:summary_html]') !== false) {
                $text = str_replace(
                    '[quote:summary_html]',
                    Quote::renderHtml($quoteFromRepository),
                    $text
                );
            }

            if (strpos($text, '[quote:summary]') !== false) {
                $text = str_replace(
                    '[quote:summary]',
                    Quote::renderText($quoteFromRepository),
                    $text
                );
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]', $quoteDestination->countryName, $text);
        }

        if ($user = ApplicationContext::getInstance()->getCurrentUser()) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }
}
