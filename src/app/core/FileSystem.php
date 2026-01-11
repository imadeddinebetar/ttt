<?php

namespace App\Core;

class FileSystem
{
    private int $maxSize;
    private array $allowedTypes;

    public function __construct()
    {
        $this->maxSize = config('filesystem')['max_size'];
        $this->allowedTypes = config('filesystem')['mime'];
    }

    public function exists(string $filePath): bool
    {
        return file_exists(storage_path('/uploads/' . $filePath));
    }

    public function getContents(string $filePath): string|false
    {
        return file_get_contents(storage_path('/uploads/' . $filePath));
    }

    public function putContents(string $filePath, string $data): bool
    {
        return file_put_contents(storage_path('/uploads/' . $filePath), $data) !== false;
    }

    public function delete(string $filePath): bool
    {
        return unlink(storage_path('/uploads/' . $filePath));
    }

    public function copy(string $oldDist, string $newDist): bool
    {
        return copy(storage_path('/uploads/' . $oldDist), storage_path('/uploads/' . $newDist));
    }

    public function rename(string $oldDist, string $newDist): bool
    {
        return rename(storage_path('/uploads/' . $oldDist), storage_path('/uploads/' . $newDist));
    }

    public function upload(array $file, string $destination): bool
    {
        return move_uploaded_file($file['tmp_name'], storage_path('/uploads/' . $destination));
    }

    public function size(string $fileName): float
    {
        return filesize(storage_path('/uploads/' . $fileName)) / (1024 * 1024); // size in MB
    }

    public function mimeType(string $file): string
    {
        return mime_content_type($file['tmp_name']);
    }

    public function extension(array $file): string
    {
        return pathinfo($file['name'], PATHINFO_EXTENSION);
    }

    public function uniqueName(string $name): string
    {
        return uniqid($name, true);
    }

    public function isValidType(array $file): bool
    {
        $mimeType = mime_content_type($file['tmp_name']);
        return in_array($mimeType, $this->allowedTypes);
    }

    public function isValidSize(float $size): bool
    {
        return $size <= $this->maxSize;
    }
}