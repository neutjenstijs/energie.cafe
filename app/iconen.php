<?php
declare(strict_types=1);

/** Kleine lijn-iconen (stroke = currentColor), passend bij de lijntekening. */
function icoon(string $naam): string
{
    $p = [
        'pijl' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'pin' => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'klok' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'kalender' => '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
        'mensen' => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M15.5 14.2c3 .2 5.5 2.5 5.5 5.8"/>',
        'glas' => '<path d="M7 3h10l-1 8a4 4 0 0 1-8 0L7 3zM12 15v6M8.5 21h7"/>',
        'euro' => '<path d="M17 6.5A6.5 6.5 0 1 0 17 17.5M4 10h9M4 14h9"/>',
        'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'balans' => '<path d="M12 4v16M7 20h10M5 7h14M5 7l-3 7a3 3 0 0 0 6 0L5 7zM19 7l-3 7a3 3 0 0 0 6 0l-3-7"/>',
        'lamp' => '<path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1 2V16h5v-.1c0-.8.4-1.5 1-2A6 6 0 0 0 12 3z"/>',
        'vraag' => '<path d="M4 5h16v11H9l-5 4V5z"/><path d="M10 9a2 2 0 1 1 2.6 1.9c-.4.1-.6.5-.6.9v.2M12 14v.01"/>',
        'huis' => '<path d="M3 11l9-7 9 7M5 9.5V20h14V9.5M10 20v-5h4v5"/>',
        'telefoon' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6l8.5 7 8.5-7"/>',
        'web' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.5 5.7 3.5 9s-1 6.3-3.5 9c-2.5-2.7-3.5-5.7-3.5-9s1-6.3 3.5-9z"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'sluit' => '<path d="M6 6l12 12M18 6L6 18"/>',
        'bel' => '<path d="M6 16V11a6 6 0 1 1 12 0v5l2 2H4l2-2zM10 20a2 2 0 0 0 4 0"/>',
        'download' => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
    ][$naam] ?? '';
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
}
