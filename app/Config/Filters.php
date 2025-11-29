<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\AuthFilter;
use App\Filters\ApiSecurityFilter;
use App\Filters\CSRFProtectionFilter;
use App\Filters\JWTAuthFilter;
use App\Filters\RoleFilter;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        // 'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => AuthFilter::class,
        'apisecurity'   => ApiSecurityFilter::class,
        'csrfprotection' => CSRFProtectionFilter::class,
        'jwtauth'       => JWTAuthFilter::class,
        'role'          => RoleFilter::class,
    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * Filters set by default provide framework functionality. If removed,
     * those functions will no longer work.
     *
     * @see https://codeigniter.com/user_guide/incoming/filters.html#provided-filters
     *
     * @var array<string, array<string, array<string, string>>|array<string, string>>|array<string, string>
     */
    public array $required = [
        'before' => [
            'forcehttps',
            'pagecache',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    /**
     * Filters that are always applied globally.
     *
     * @var array<string, array<string, string>>|array<string, string>
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf' => ['except' => ['api/*']],
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * Filters applied to HTTP methods.
     *
     * @var array<string, string>
     */
    public array $methods = [];

    /**
     * Filter aliases that are applied to specific URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        'apisecurity' => ['before' => ['api/*']],
        'jwtauth' => ['before' => ['api/*']],
        'csrfprotection' => ['before' => ['api/*'], 'except' => ['api/auth/*']],
    ];
}