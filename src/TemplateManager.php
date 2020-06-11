<?php

require_once __DIR__ . '/Manager/TemplateManager.php';
require_once __DIR__ . '/Manager/TemplateManagerInterface.php';

use App\Manager\TemplateManager as NewTemplateManager;
use App\Manager\TemplateManagerInterface;

/**
 * @deprecated The TemplateManager class is deprecated, use App\Manager\TemplateManager instead.
 */
class TemplateManager implements TemplateManagerInterface
{
    /**
     * @param Template $template
     * @param array    $data
     *
     * @return Template
     */
    public function getTemplateComputed(Template $template, array $data)
    {
        return $this->getNewTemplateManager()->getTemplateComputed($template, $data);
    }

    private function getNewTemplateManager()
    {
        return (new NewTemplateManager());
    }
}
