<?php

namespace App\Enums;

enum ProgramKind: string
{
    /** A structured journey with weeks (e.g. Bump Strength · Trimester 2). */
    case Program = 'program';

    /** A flat library of classes (e.g. Stretch Library). */
    case Collection = 'collection';
}
