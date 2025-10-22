<?php

return [
    // Default provider key: deepseek | openai | gemini | ollama
    'default' => env('AI_PROVIDER', 'deepseek'),

    'providers' => [
        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY'),
            'base_url' => env('DEEPSEEK_BASE', 'https://api.deepseek.com/v1/chat/completions'),
            'model'   => env('DEEPSEEK_MODEL', 'deepseek-chat'),
            'timeout' => 30,
        ],

        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => env('OPENAI_BASE', 'https://api.openai.com/v1/chat/completions'),
            'model'   => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'timeout' => 30,
        ],

        // Gemini uses a different request shape, handled in provider
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'base_url' => env('GEMINI_BASE', 'https://generativelanguage.googleapis.com/v1beta/models'),
            'model'   => env('GEMINI_MODEL', 'gemini-1.5-flash-latest'),
            'timeout' => 30,
        ],

        // Local LLM via Ollama
        'ollama' => [
            'base_url' => env('OLLAMA_BASE', 'http://localhost:11434/api/generate'),
            'model'   => env('OLLAMA_MODEL', 'llama3'),
            'timeout' => 60,
        ],
    ],
];
