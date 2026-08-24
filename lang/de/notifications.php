<?php

return [
    'run_completed' => [
        'subject' => 'Lauf abgeschlossen: :name',
        'eyebrow' => 'Lauf beendet',
        'intro' => 'Dein Optimierungslauf ist fertig. Das ist das Ergebnis:',
        'score' => 'Bester Score',
        'target' => 'Ziel-Score',
        'benchmark' => 'Benchmark',
        'cta' => 'Lauf ansehen',
        'footer' => 'Du erhältst diese Mail, weil du diesen Lauf in Octavia gestartet hast.',
    ],

    'welcome' => [
        'subject' => 'Willkommen bei Octavia',
        'greeting' => 'Willkommen an Bord, :name!',
        'line1' => 'Dein Prompt-Labor ist bereit. Ein Starter-Prompt und ein lauffähiger Benchmark warten in deiner Bibliothek.',
        'line2' => 'Öffne deinen Prompt und klicke auf Optimieren — Octavia entwickelt ihn Schritt für Schritt weiter, bis dein Benchmark-Ziel erreicht ist.',
        'cta' => 'Ersten Lauf starten',
        'footer' => 'Viel Erfolg beim Optimieren,\nDein Octavia-Team',
    ],

    'report_resolved' => [
        'subject' => 'Meldung bearbeitet',
        'greeting' => 'Hallo :name,',
        'intro' => 'Danke, dass du den Octavia-Marktplatz sauber hältst. Wir haben deine Meldung zu „:title" geprüft.',
        'kept' => 'Deine Meldung wurde geprüft, das Listing bleibt verfügbar.',
        'unlisted' => 'Deine Meldung wurde geprüft, das Listing wurde deaktiviert.',
        'action' => 'Zum Marktplatz',
    ],

    'listingUpdated' => [
        'subject' => "Update: ':title' ist jetzt v:version",
        'line1' => "Eine neue Version (:version) von ':title' ist im Marketplace verfügbar.",
        'cta' => 'Update ansehen',
        'footer' => 'Du erhältst diese Mail, weil du das Listing installiert hast. Du kannst sie in den Einstellungen deaktivieren.',
    ],
];
