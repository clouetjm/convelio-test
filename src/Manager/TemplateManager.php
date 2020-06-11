<?php

namespace App\Manager;

require_once __DIR__ . '/TemplateManagerInterface.php';
require_once __DIR__ . '/../Parser/TemplateParserInterface.php';

use App\Parser\TemplateParserInterface;
use RuntimeException;
use Template;

class TemplateManager implements TemplateManagerInterface
{
    /**
     * @var TemplateParserInterface
     */
    private $templateParser;

    public function __construct(TemplateParserInterface $templateParser)
    {
        $this->templateParser = $templateParser;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateComputed(Template $template, array $data)
    {
        if (!$template) {
            throw new RuntimeException('No template given.');
        }

        $template = clone($template);
        $template->subject = $this->templateParser->parse($template->subject, $data);
        $template->content = $this->templateParser->parse($template->content, $data);

        return $template;
    }
}
