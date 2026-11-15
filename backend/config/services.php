<?php

return [

    // Rebuilding the static site from the dashboard "نشر" button (see App\Support\Publisher).
    'publish' => [
        // Fine-grained GitHub token with "Actions: read and write" on the repository only.
        'token' => env('PUBLISH_GITHUB_TOKEN'),
        'repo' => env('PUBLISH_GITHUB_REPO'),
        'workflow' => env('PUBLISH_WORKFLOW', 'publish.yml'),
        'branch' => env('PUBLISH_BRANCH', 'main'),
    ],

];
