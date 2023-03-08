<?php

namespace Frankie\Core\Logger;

enum LogLevel: int
{
    case DISABLED = 0;
    case DEBUG = 1;
    case INFO = 2;
    case NOTICE = 3;
    case WARNING = 4;
    case ERROR = 5;
    case CRITICAL = 6;
    case ALERT = 7;
    case EMERGENCY = 8;
}
