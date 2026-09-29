<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/carteirinhaView.php';

function reportEscape($value): string
{
    $text = (string) ($value ?? '');
    // Permit wrapping of long, unbroken values without losing their contents.
    $text = preg_replace('/([^\s]{30})(?=[^\s])/u', "$1\u{200B}", $text);
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function reportDate($value): string
{
    if (!$value || substr((string) $value, 0, 10) === '0000-00-00') {
        return 'Não informado';
    }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string) $value, 0, 10));
    return $date ? $date->format('d/m/Y') : (string) $value;
}

function reportMoney(int $cents): string
{
    return 'R$ ' . number_format($cents / 100, 2, ',', '.');
}

function reportImage(string $relativePath): ?string
{
    $root = realpath(__DIR__ . '/..');
    $path = realpath($root . '/' . $relativePath);
    $allowed = [$root . '/assets/img/', $root . '/anexo_alunos/'];
    if (!$path || !is_file($path) || filesize($path) > 5 * 1024 * 1024) {
        return null;
    }
    if (!str_starts_with($path, $allowed[0]) && !str_starts_with($path, $allowed[1])) {
        return null;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
    if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
        return null;
    }
    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
}

function renderReportPdf(array $report): string
{
    $logo = reportImage('assets/img/logoR7.png');
    ob_start();
    try {
        require __DIR__ . '/../reports/template.php';
        $html = ob_get_contents();
    } finally {
        ob_end_clean();
    }
    $options = new Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $options->set('isPhpEnabled', false);
    $options->set('isJavascriptEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('chroot', realpath(__DIR__ . '/../assets'));
    $options->set('fontCache', sys_get_temp_dir());
    $pdf = new Dompdf\Dompdf($options);
    $pdf->setPaper('A4', 'portrait');
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $canvas = $pdf->getCanvas();
    $font = $pdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
    $canvas->page_text(420, 808, 'Página {PAGE_NUM} de {PAGE_COUNT}', $font, 8, [0.39, 0.49, 0.53]);
    return $pdf->output();
}
