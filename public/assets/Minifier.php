<?php

require_once(dirname(__DIR__, 2). "/vendor/Minifier/minifier.php");

$js = [
    'JS/main.js' => 'JS/main.min.js',
    'JS/timeline.js' => 'JS/timeline.min.js',
    'JS/timeline_discente.js' => 'JS/timeline_discente.min.js',
    'JS/timeline_docente.js' => 'JS/timeline_docente.min.js',
];

$css = [
    'SCSS/resetar.css'=> 'CSS/resetar.min.css',
    'SCSS/style.css'=> 'CSS/style.min.css',
    'SCSS/timeline.css'=> 'CSS/timeline.min.css',
];

//minifyJS($js);
minifyCSS($css);