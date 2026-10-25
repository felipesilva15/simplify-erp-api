<?php

namespace App\Modules\Integration\Enums;

enum IntegrationDirection: string
{
    case OUTBOUND = 'outbound';
    case INBOUND = 'inbound';
}
