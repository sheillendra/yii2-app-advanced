<?php
/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'skipFiles'  => [
 *             // list of files that should only be copied once and skipped if they already exist
 *         ],
 *         'setWritable' => [
 *             // list of directories that should be set writable
 *         ],
 *         'setExecutable' => [
 *             // list of files that should be set executable
 *         ],
 *         'setCookieValidationKey' => [
 *             // list of config files that need to be inserted with automatically generated cookie validation keys
 *         ],
 *         'createSymlink' => [
 *             // list of symlinks to be created. Keys are symlinks, and values are the targets.
 *         ],
 *     ],
 * ];
 * ```
 */
return [
    /**
     * local PC
     */
    'Alpha' => [
        'path' => 'alpha',
        'setWritable' => [
            'backend/runtime',
            'backend/web/assets',
            'console/runtime',
            'frontend/runtime',
            'frontend/web/assets',
            'frontend/web/admin/assets',
            'api/runtime',
        ],
        'setExecutable' => [
            'yii',
            'yii_test',
        ],
        'setCookieValidationKey' => [
            'api/config/main-local.php',
            'backend/config/main-local.php',
            'common/config/codeception-local.php',
            'frontend/config/main-local.php'
        ],
    ],
    /**
     * Auto .env like heroku
     */
    'Autoenv' => [
        'path' => 'autoenv',
        'setWritable' => [
            'backend/runtime',
            'backend/web/assets',
            'console/runtime',
            'frontend/runtime',
            'frontend/web/assets',
            'frontend/web/admin/assets',
            'api/runtime',
        ],
        'setExecutable' => [
            'yii',
            'yii_test',
        ],
        'setCookieValidationKey' => [
            'api/config/main-local.php',
            'backend/config/main-local.php',
            'common/config/codeception-local.php',
            'frontend/config/main-local.php'
        ],
    ],
    /**
     * server development
     */
    'Beta' => [
        'path' => 'beta',
        'setWritable' => [
            'backend/runtime',
            'backend/web/assets',
            'console/runtime',
            'frontend/runtime',
            'frontend/web/assets',
            'frontend/web/admin/assets',
            'api/runtime',
        ],
        'setExecutable' => [
            'yii',
            'yii_test',
        ],
        'setCookieValidationKey' => [
            'api/config/main-local.php',
            'backend/config/main-local.php',
            'common/config/codeception-local.php',
            'frontend/config/main-local.php'
        ],
    ],
    /**
     * server production with debug and test
     */
    'Staging' => [
        'path' => 'staging',
        'setWritable' => [
            'backend/runtime',
            'backend/web/assets',
            'console/runtime',
            'frontend/runtime',
            'frontend/web/assets',
            'frontend/web/admin/assets',
            'api/runtime',
        ],
        'setExecutable' => [
            'yii',
            'yii_test',
        ],
        'setCookieValidationKey' => [
            'api/config/main-local.php',
            'backend/config/main-local.php',
            'common/config/codeception-local.php',
            'frontend/config/main-local.php'
        ],
    ],
    /**
     * server production
     */
    'Production' => [
        'path' => 'prod',
        'setWritable' => [
            'backend/runtime',
            'backend/web/assets',
            'console/runtime',
            'frontend/runtime',
            'frontend/web/assets',
            'frontend/web/admin/assets',
            'api/runtime',
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'api/config/main-local.php',
            'backend/config/main-local.php',
            'frontend/config/main-local.php'
        ],
    ],
];
