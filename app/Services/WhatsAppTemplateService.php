<?php

namespace App\Services;

use App\Models\WhatsappTemplate;

class WhatsAppTemplateService
{
    public function active(string $name, string $language='en'): ?WhatsappTemplate
    {
        return WhatsappTemplate::where('name',$name)
            ->where('language_code',$language)
            ->where('is_active',true)
            ->where('status','approved')
            ->first();
    }

    public function variables(WhatsappTemplate $template): array
    {
        return is_array($template->variables_json) ? $template->variables_json : [];
    }
}
