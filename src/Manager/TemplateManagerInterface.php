<?php

namespace App\Manager;

require_once __DIR__ . '/ManagerInterface.php';

use Template;

interface TemplateManagerInterface extends ManagerInterface
{
    /**
     * @param Template $template
     * @param array    $data
     *
     * @return Template
     */
    public function getTemplateComputed(Template $template, array $data);
}