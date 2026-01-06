<?php

namespace App\Core;

class Logger
{
    public static function log( string $message, string $level = 'debug', array $context = []) {
        $date = date('Y-m-d H:i:s');
        
        // Use flags for cleaner JSON output
        $contextString = !empty($context) ? json_encode($context, JSON_UNESCAPED_SLASHES) : '';

        $logDir = __DIR__ . '/../../storage/logs/';
        $logPath = $logDir . date('Y-m-d') . '.log';

        // 2. Format the line (added a space before context for readability)
        $formattedMessage = sprintf("%s | %s | %s | %s%s", 
            $date, 
            ucfirst($message), 
            strtoupper($level), 
            $contextString, 
            PHP_EOL
        );

        // 3. Write to file
        file_put_contents($logPath, $formattedMessage, FILE_APPEND);
    }

    public static function info(string $message, array $context = []){
        self::log($message, 'info', $context);
    }

    public static function warning(string $message, array $context = []){
        self::log($message, 'warning', $context);
    }

    public static function error(string $message, array $context = []){
        self::log($message, 'error', $context);
    }
}