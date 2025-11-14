<?php

namespace App\Enums\Trail;

enum TrailDifficulty: string
{
    case EXPERT = 'expert';
    case HARD = 'hard';
    case MODERATE = 'moderate';
    case EASY = 'easy';
}
