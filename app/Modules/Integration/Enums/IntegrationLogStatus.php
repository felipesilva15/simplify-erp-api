<?php

namespace App\Modules\Integration\Enums;

enum IntegrationLogStatus: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case TIMEOUT = 'timeout';
}
