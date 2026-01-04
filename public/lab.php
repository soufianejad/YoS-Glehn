<?php

// generate_fsqm_quiz.php
// Exécuter via CLI ou navigateur (pas besoin de WordPress)

// === CONFIGURATION ===
$quiz_name = 'My Superhero Quiz';
$csv_file = 'questions.csv';

// === 1. LIRE LE CSV (avec $escape pour éviter les warnings) ===
$questions = [];

if (! file_exists($csv_file)) {
    exit("Erreur : Fichier '$csv_file' introuvable.\n");
}

$handle = fopen($csv_file, 'r');
if ($handle === false) {
    exit("Erreur : Impossible d'ouvrir le fichier.\n");
}

// PHP 8.1+ : $escape doit être fourni
$escape = '\\'; // Valeur par défaut actuelle (sera obligatoire plus tard)

// Ignorer l'en-tête (si présent)
$header = fgetcsv($handle, 1000, ';', '"', $escape);

while (($data = fgetcsv($handle, 1000, ';', '"', $escape)) !== false) {
    if (count($data) < 6) {
        continue;
    }

    $questions[] = [
        'title' => trim($data[0]),
        'correct' => trim($data[1]),
        'wrong' => trim($data[2]),
        'options_raw' => trim($data[3]),
        'score_correct' => trim($data[4]),
        'score_wrong' => trim($data[5]),
    ];
}
fclose($handle);

if (empty($questions)) {
    exit("Aucune question trouvée dans le CSV.\n");
}

// === 2. GÉNÉRER LES STRUCTURES FSQM ===
$layout = $design = $mcq = $pinfo = [];
$mcq_key = $design_key = 0;

// --- Boucle sur chaque question ---
foreach ($questions as $i => $q) {
    $mcq_key_str = (string) $mcq_key++;
    $correct_key = (string) $design_key++;
    $wrong_key = (string) $design_key++;

    // Options
    $options = [];
    foreach (array_map('trim', explode(',', $q['options_raw'])) as $opt) {
        $is_correct = ($opt === $q['correct']);
        $score = $is_correct ? $q['score_correct'] : '0';
        if (! $is_correct && ! in_array($q['score_wrong'], ['0', ''], true)) {
            $score = $q['score_wrong']; // Applique le malus une seule fois
            $q['score_wrong'] = '0'; // Évite de le réappliquer
        }
        $options[] = ['label' => $opt, 'score' => $score, 'num' => ''];
    }

    // --- MCQ ---
    $mcq[] = [
        'type' => 'radio', 'title' => $q['title'], 'validation' => ['required' => true],
        'subtitle' => '', 'description' => '', 'conditional' => ['active' => false, 'status' => false, 'change' => true, 'logic' => []],
        'm_type' => 'mcq', 'tooltip' => '',
        'settings' => [
            'hidden_label' => false, 'options' => $options, 'columns' => '4', 'vertical' => false,
            'centered' => false, 'button_type' => false, 'others' => false, 'o_label' => 'Others',
            'icon' => '57742', 'shuffle' => false, 'type' => 'none', 'parameter' => '',
        ],
    ];

    // --- Feedback Correct ---
    $design[] = [
        'type' => 'richtext', 'title' => '', 'subtitle' => '',
        'description' => '<blockquote style="color:green;"><strong>Correct !</strong><br>'.htmlspecialchars($q['correct']).' est la bonne réponse.</blockquote>',
        'conditional' => [
            'active' => true, 'status' => false, 'change' => true,
            'logic' => [
                ['m_type' => 'mcq', 'key' => $mcq_key_str, 'check' => 'val', 'operator' => 'eq', 'value' => $q['correct'], 'rel' => 'and'],
            ],
        ],
        'm_type' => 'design', 'settings' => ['icon' => '61510', 'styled' => false],
    ];

    // --- Feedback Incorrect ---
    $design[] = [
        'type' => 'richtext', 'title' => '', 'subtitle' => '',
        'description' => '<blockquote style="color:red;"><strong>Faux !</strong><br>'.htmlspecialchars($q['wrong']).'</blockquote>',
        'conditional' => [
            'active' => true, 'status' => false, 'change' => true,
            'logic' => [
                ['m_type' => 'mcq', 'key' => $mcq_key_str, 'check' => 'val', 'operator' => 'neq', 'value' => $q['correct'], 'rel' => 'and'],
            ],
        ],
        'm_type' => 'design', 'settings' => ['icon' => '61453', 'styled' => false],
    ];

    // --- Tab Question ---
    $layout[] = [
        'type' => 'tab', 'title' => 'Question '.($i + 1), 'subtitle' => '', 'description' => '',
        'moreover' => ['active' => false, 'status' => false, 'change' => true, 'logic' => []],
        'm_type' => 'layout', 'elements' => [['m_type' => 'mcq', 'type' => 'radio', 'key' => $mcq_key_str]],
        'icon' => 'none', 'timer' => '0',
    ];

    // --- Tab Feedback ---
    $layout[] = [
        'type' => 'tab', 'title' => 'Résultat', 'subtitle' => '', 'description' => '',
        'conditional' => ['active' => false, 'status' => false, 'change' => true, 'logic' => []],
        'm_type' => 'layout',
        'elements' => [
            ['m_type' => 'design', 'type' => 'richtext', 'key' => $correct_key],
            ['m_type' => 'design', 'type' => 'richtext', 'key' => $wrong_key],
        ],
        'icon' => 'none', 'timer' => '0',
    ];
}

// === 4. PAGE FINALE : CERTIFICAT ===
$pinfo = [
    ['type' => 'f_name', 'title' => 'Prénom', 'validation' => ['required' => true], 'm_type' => 'pinfo', 'settings' => ['hidden_label' => true, 'placeholder' => 'Prénom', 'icon' => '61447']],
    ['type' => 'l_name', 'title' => 'Nom', 'validation' => ['required' => true], 'm_type' => 'pinfo', 'settings' => ['hidden_label' => true, 'placeholder' => 'Nom', 'icon' => '61447']],
    ['type' => 'email', 'title' => 'Email', 'validation' => ['required' => true], 'm_type' => 'pinfo', 'settings' => ['hidden_label' => true, 'placeholder' => 'Email', 'icon' => '61664']],
    ['type' => 'phone', 'title' => 'Téléphone', 'validation' => ['required' => false], 'm_type' => 'pinfo', 'settings' => ['hidden_label' => true, 'placeholder' => 'Téléphone', 'icon' => '57484']],
];

$col_left = (string) $design_key++;
$col_right = (string) $design_key++;

$design[] = [
    'type' => 'col_half', 'title' => '', 'subtitle' => '', 'description' => '',
    'conditional' => ['active' => false], 'm_type' => 'design',
    'elements' => [
        ['m_type' => 'pinfo', 'type' => 'f_name', 'key' => '0'],
        ['m_type' => 'pinfo', 'type' => 'l_name', 'key' => '1'],
    ],
];

$design[] = [
    'type' => 'col_half', 'title' => '', 'subtitle' => '', 'description' => '',
    'conditional' => ['active' => false], 'm_type' => 'design',
    'elements' => [
        ['m_type' => 'pinfo', 'type' => 'email', 'key' => '2'],
        ['m_type' => 'pinfo', 'type' => 'phone', 'key' => '3'],
    ],
];

$layout[] = [
    'type' => 'tab', 'title' => 'Obtenir votre certificat', 'subtitle' => '',
    'description' => '<p style="text-align:center;font-size:18px;">Félicitations ! Remplissez vos informations pour recevoir votre certificat.</p>',
    'conditional' => ['active' => false, 'status' => false, 'change' => true, 'logic' => []],
    'm_type' => 'layout',
    'elements' => [
        ['m_type' => 'design', 'type' => 'col_half', 'key' => $col_left],
        ['m_type' => 'design', 'type' => 'col_half', 'key' => $col_right],
    ],
    'icon' => '61603', 'timer' => '0',
];

// === 5. SETTINGS (exemple fonctionnel) ===
$settings = [
    'success_message' => 'Félicitations ! Votre certificat est prêt.',
    'success_action' => 'message',
    'email_admin' => true,
    'email_user' => true,
    'email_subject' => 'Votre certificat - {{f_name}} {{l_name}}',
    'certificate' => true,
    'passing_score' => '70',
    'show_results' => true,
    'progress_bar' => true,
    'one_per_page' => true,
    'button_next' => 'Suivant',
    'button_submit' => 'Terminer',
    'theme' => 'default',
    'language' => 'fr',
];

// === 6. ÉCHAPPER LES DONNÉES POUR SQL (sans $wpdb) ===
function mysql_escape($value)
{
    $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
    $replace = ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'];

    return "'".str_replace($search, $replace, $value)."'";
}

// === 7. GÉNÉRER LE SQL ===
$sql = "INSERT INTO `wp_fsq_form` (`id`, `name`, `settings`, `layout`, `design`, `mcq`, `freetype`, `pinfo`, `type`, `updated`, `category`) VALUES\n";
$sql .= '(NULL, '.mysql_escape($quiz_name).', ';
$sql .= mysql_escape(serialize($settings)).', ';
$sql .= mysql_escape(serialize($layout)).', ';
$sql .= mysql_escape(serialize($design)).', ';
$sql .= mysql_escape(serialize($mcq)).', ';
$sql .= mysql_escape(serialize([])).', ';
$sql .= mysql_escape(serialize($pinfo)).', ';
$sql .= '2, NOW(), 0);';

// === 8. SAUVEGARDER ===
file_put_contents('import_quiz.sql', $sql);

echo "<pre style='background:#f0f0f0;padding:15px;border-left:5px solid #4CAF50;'>";
echo "Quiz généré avec succès !\n";
echo "Fichier SQL : <strong>import_quiz.sql</strong>\n";
echo 'Questions : '.count($questions)."\n";
echo "Importe ce fichier dans phpMyAdmin ou via WP CLI.\n";
echo '</pre>';
