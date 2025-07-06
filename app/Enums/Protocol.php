<?php

namespace App\Enums;

/**
 * Protocol Enum
 *
 * Represents the available protocols for domain configuration.
 * Used to specify whether a domain should use HTTP or HTTPS protocol
 * for serving shortened links and admin panel access.
 *
 * @see \App\Models\Domain
 */
enum Protocol: string
{
    case HTTP = 'http';
    case HTTPS = 'https';
}
