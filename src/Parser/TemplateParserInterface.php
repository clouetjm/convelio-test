<?php

namespace App\Parser;

require_once __DIR__ . '/ParserInterface.php';

interface TemplateParserInterface extends ParserInterface
{
    public function parse($text, array $data);
}
