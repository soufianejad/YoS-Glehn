<?php

// Simple script to find and prepare translations for a Laravel project.
// WARNING: Run this in a development environment. Do not leave it on a production server.

set_time_limit(300); // Allow script to run for 5 minutes

header('Content-Type: text/plain; charset=utf-8');

echo "Starting translation discovery...\n";
echo "=================================\n\n";

// --- Configuration ---
$projectRoot = __DIR__.'/../';
$scanDirectories = [
    $projectRoot.'app',
    $projectRoot.'resources/views',
];
$langFiles = [
    'fr' => $projectRoot.'lang/fr.json',
    'en' => $projectRoot.'lang/en.json',
];
$reportFile = __DIR__.'/translation_report.txt';

$foundStrings = [];
$reportContent = 'Translation Report - '.date('Y-m-d H:i:s')."\n";
$reportContent .= "===============================================\n\n";

// --- Functions ---

/**
 * Recursively find all files in a directory.
 */
function getFiles(string $dir): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade.php'])) {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

/**
 * Clean and validate a potential translation string.
 */
function cleanString(string $string): ?string
{
    $string = trim($string);

    // Basic filtering rules
    if (
        strlen($string) < 4 ||                 // Too short
        is_numeric($string) ||                 // Is a number
        filter_var($string, FILTER_VALIDATE_EMAIL) || // Is an email
        filter_var($string, FILTER_VALIDATE_URL) || // Is a URL
        strpos($string, '{{') !== false ||     // Contains Blade syntax
        strpos($string, '->') !== false ||     // Contains object access
        strpos($string, '::') !== false ||     // Contains static access
        strpos($string, '$') !== false ||      // Contains a variable
        preg_match('/^[a-z0-9_.-]+$/', $string) // Looks like a route, key, or filename
    ) {
        return null;
    }

    return html_entity_decode($string);
}

// --- Main Logic ---

// 1. Load existing translations
$existingTranslations = [];
foreach ($langFiles as $lang => $path) {
    if (file_exists($path)) {
        $existingTranslations[$lang] = json_decode(file_get_contents($path), true);
        echo 'Loaded '.count($existingTranslations[$lang])." existing keys for '$lang'.\n";
    } else {
        $existingTranslations[$lang] = [];
        echo "Warning: Language file not found for '$lang' at $path. A new one will be created.\n";
    }
}
$allExistingKeys = array_keys($existingTranslations['fr'] + $existingTranslations['en']);
echo "\n";

// 2. Scan files and find potential strings
echo "Scanning directories...\n";
$totalFiles = 0;
foreach ($scanDirectories as $dir) {
    $files = getFiles($dir);
    $totalFiles += count($files);
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $relativePath = str_replace($projectRoot, '', $file);

        // Regex for different patterns
        $patterns = [
            // Text inside blade tags: > Hello World <
            '/>([^<]{4,})</u',
            // Text in simple quotes: 'Hello World'
            "/'([^'\d][^']{3,})'/u",
            // Text in double quotes: "Hello World"
            '//"([^"\d][^"]{3,})"/u',
            // Translatable attributes
            '/(?:placeholder|title|alt)=\"([^\"]{4,})"/u',
            '/(?:placeholder|title|alt)=\'([^\]{4,})\'/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $cleaned = cleanString($match);
                    if ($cleaned && ! in_array($cleaned, $allExistingKeys)) {
                        if (! isset($foundStrings[$cleaned])) {
                            $foundStrings[$cleaned] = [];
                        }
                        if (! in_array($relativePath, $foundStrings[$cleaned])) {
                            $foundStrings[$cleaned][] = $relativePath;
                        }
                    }
                }
            }
        }
    }
}
echo "Scanned $totalFiles files.\n";
echo 'Found '.count($foundStrings)." new potential translation strings.\n\n";

// 3. Update language files
if (empty($foundStrings)) {
    echo "No new strings to add. Files are up to date.\n";
    exit;
}

echo "Updating language files...\n";
$newKeysCount = 0;
foreach ($foundStrings as $string => $files) {
    if (! array_key_exists($string, $existingTranslations['fr'])) {
        $existingTranslations['fr'][$string] = $string; // Default to self
        $newKeysCount++;
    }
    if (! array_key_exists($string, $existingTranslations['en'])) {
        $existingTranslations['en'][$string] = ''; // Leave English empty
    }

    $reportContent .= "--- STR ---\n";
    $reportContent .= "$string\n";
    $reportContent .= "--- FILES ---\n";
    $reportContent .= implode("\n", $files)."\n\n";
}

foreach ($langFiles as $lang => $path) {
    // Sort keys alphabetically
    ksort($existingTranslations[$lang]);

    $jsonOutput = json_encode($existingTranslations[$lang], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($path, $jsonOutput);
    echo "Updated $path\n";
}

// 4. Create report
file_put_contents($reportFile, $reportContent);
echo "\nSuccessfully added $newKeysCount new keys.\n";
echo "A report has been generated at: public/translation_report.txt\n";
echo "\n--- SCRIPT FINISHED ---\n";
echo "REMINDER: Please delete this script from the public folder when you are done.\n";
