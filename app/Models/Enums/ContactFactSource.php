<?php

namespace App\Models\Enums;

enum ContactFactSource: string
{
    case AiExtracted = 'ai_extracted';
    case ManuallyPinned = 'manually_pinned';
}
