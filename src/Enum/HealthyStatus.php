<?php

namespace App\Enum;

enum HealthyStatus: string
{
    case HEALTHY = 'Healthy';
    case SICK = 'Sick';
    case HUNGRY = "Hungry";
}
