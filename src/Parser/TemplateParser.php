<?php

namespace App\Parser;

use ApplicationContext;
use DestinationRepository;
use Quote;
use QuoteRepository;

require_once __DIR__ . '/TemplateParserInterface.php';

class TemplateParser implements TemplateParserInterface
{
    /**
     * @param string $text
     * @param array  $data
     *
     * @return string
     */
    public function parse($text, array $data)
    {
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if (null !== $quote) {
            $quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $quoteDestination = DestinationRepository::getInstance()->getById($quote->destinationId);

            $tags = [
                '[quote:summary_html]' => Quote::renderHtml($quoteFromRepository),
                '[quote:summary]' => Quote::renderHtml($quoteFromRepository),
                '[quote:destination_name]' => $quoteDestination->countryName,
                '[user:first_name]' => ucfirst(mb_strtolower((ApplicationContext::getInstance()->getCurrentUser())->firstname)),
            ];

            foreach ($tags as $tagName => $tagValue) {
                if (strpos($text, $tagName) !== false) {
                    $text = str_replace(
                        $tagName,
                        $tagValue,
                        $text
                    );
                }
            }
        }

        return $text;
    }
}
