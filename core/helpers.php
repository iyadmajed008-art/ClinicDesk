<?php

declare(strict_types=1);

function url(string $page = 'dashboard', string $action = 'index', array $params = []): string
{
    $query = array_merge(['page' => $page, 'action' => $action], $params);
    return BASE_URL . '/index.php?' . http_build_query($query);
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function formatDate(?string $date): string
{
    return $date ? date('d M Y', strtotime($date)) : '';
}

function formatTime(?string $time): string
{
    return $time ? date('H:i', strtotime($time)) : '';
}

function currentPageNumber(): int
{
    return max(1, (int) ($_GET['p'] ?? 1));
}

function uploadImage(array $file, string $folder): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > MAX_IMAGE_UPLOAD) {
        throw new RuntimeException('Invalid image upload.');
    }

    if (!getimagesize($file['tmp_name'])) {
        throw new RuntimeException('Uploaded file is not a valid image.');
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
        throw new RuntimeException('Only JPG and PNG images are allowed.');
    }

    $name = bin2hex(random_bytes(16)) . '.' . $extension;
    $relative = 'public/uploads/' . $folder . '/' . $name;
    $target = __DIR__ . '/../' . $relative;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    return $relative;
}

function uploadPrescriptionPdf(array $file, int $appointmentId): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > MAX_PDF_UPLOAD) {
        throw new RuntimeException('Invalid PDF upload.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
    if ($finfo) {
        finfo_close($finfo);
    }

    if ($mime !== 'application/pdf') {
        throw new RuntimeException('Only PDF files are allowed.');
    }

    $name = 'prescription_' . $appointmentId . '_' . time() . '.pdf';
    $relative = 'public/uploads/prescriptions/' . $name;
    $target = __DIR__ . '/../' . $relative;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded PDF.');
    }

    return $relative;
}
