<?php

namespace App\Services;

/**
 * File Service - Centralized file operations
 * Eliminates redundancy across controllers
 */
class FileService
{
    protected $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    protected $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'pdf', 'txt', 'doc', 'docx'
    ];

    protected $maxFileSize = 5 * 1024 * 1024; // 5MB

    /**
     * Upload file with validation
     */
    public function uploadFile($file, string $directory, array $options = []): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'errors' => []
        ];

        // Validate file
        $validation = $this->validateFile($file, $options);
        if (!$validation['success']) {
            return $validation;
        }

        // Create directory if it doesn't exist
        $uploadPath = WRITEPATH . $directory;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate filename
        $filename = $this->generateFilename($file, $options['prefix'] ?? '');

        // Move file
        if (!$file->move($uploadPath, $filename)) {
            $result['errors'][] = 'Failed to upload file';
            return $result;
        }

        $result['success'] = true;
        $result['data'] = [
            'filename' => $filename,
            'original_name' => $file->getName(),
            'path' => $directory . '/' . $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getExtension()
        ];

        return $result;
    }

    /**
     * Validate uploaded file
     */
    public function validateFile($file, array $options = []): array
    {
        $errors = [];

        if (!$file || !$file->isValid()) {
            $errors[] = 'No file uploaded or invalid file';
            return ['success' => false, 'errors' => $errors];
        }

        // Check file size
        $maxSize = $options['max_size'] ?? $this->maxFileSize;
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum limit of ' . $this->formatBytes($maxSize);
        }

        // Check MIME type
        $allowedMimes = $options['allowed_mimes'] ?? $this->allowedMimeTypes;
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'File type not allowed';
        }

        // Check extension
        $allowedExts = $options['allowed_extensions'] ?? $this->allowedExtensions;
        if (!in_array(strtolower($file->getExtension()), $allowedExts)) {
            $errors[] = 'File extension not allowed';
        }

        // Additional custom validations
        if (isset($options['custom_validations'])) {
            foreach ($options['custom_validations'] as $validation) {
                $customErrors = $validation($file);
                if (!empty($customErrors)) {
                    $errors = array_merge($errors, $customErrors);
                }
            }
        }

        return [
            'success' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Delete file
     */
    public function deleteFile(string $path): bool
    {
        $fullPath = WRITEPATH . $path;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    /**
     * Get file info
     */
    public function getFileInfo(string $path): ?array
    {
        $fullPath = WRITEPATH . $path;
        
        if (!file_exists($fullPath)) {
            return null;
        }

        return [
            'path' => $path,
            'full_path' => $fullPath,
            'size' => filesize($fullPath),
            'mime_type' => mime_content_type($fullPath),
            'extension' => pathinfo($fullPath, PATHINFO_EXTENSION),
            'modified_time' => filemtime($fullPath)
        ];
    }

    /**
     * Generate unique filename
     */
    public function generateFilename($file, string $prefix = ''): string
    {
        $extension = $file->getExtension();
        $baseName = $prefix . time() . '_' . uniqid();
        
        return $baseName . '.' . $extension;
    }

    /**
     * Format bytes to human readable format
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture($file, int $userId): array
    {
        $options = [
            'prefix' => 'profile_' . $userId . '_',
            'allowed_mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'max_size' => 2 * 1024 * 1024 // 2MB
        ];

        return $this->uploadFile($file, 'uploads/profiles', $options);
    }

    /**
     * Upload document
     */
    public function uploadDocument($file, string $category = 'general'): array
    {
        $options = [
            'prefix' => $category . '_',
            'allowed_mimes' => array_merge(
                $this->allowedMimeTypes,
                ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            ),
            'max_size' => 10 * 1024 * 1024 // 10MB
        ];

        return $this->uploadFile($file, 'uploads/documents/' . $category, $options);
    }

    /**
     * Create image thumbnail
     */
    public function createThumbnail(string $sourcePath, string $destPath, int $width = 300, int $height = 300): bool
    {
        $fullSourcePath = WRITEPATH . $sourcePath;
        $fullDestPath = WRITEPATH . $destPath;

        if (!file_exists($fullSourcePath)) {
            return false;
        }

        // Create destination directory
        $destDir = dirname($fullDestPath);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // Get image info
        $imageInfo = getimagesize($fullSourcePath);
        if (!$imageInfo) {
            return false;
        }

        $mime = $imageInfo['mime'];
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];

        // Create image resource
        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($fullSourcePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($fullSourcePath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($fullSourcePath);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($fullSourcePath);
                break;
            default:
                return false;
        }

        // Calculate dimensions
        $aspectRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $width / $height;

        if ($aspectRatio > $targetRatio) {
            $newWidth = $width;
            $newHeight = $width / $aspectRatio;
        } else {
            $newHeight = $height;
            $newWidth = $height * $aspectRatio;
        }

        // Create thumbnail
        $thumbnail = imagecreatetruecolor($width, $height);
        
        // Handle transparency for PNG
        if ($mime === 'image/png') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $width, $height, $transparent);
        }

        // Resize and center
        $x = ($width - $newWidth) / 2;
        $y = ($height - $newHeight) / 2;
        
        imagecopyresampled($thumbnail, $source, $x, $y, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        // Save thumbnail
        $result = false;
        switch ($mime) {
            case 'image/jpeg':
                $result = imagejpeg($thumbnail, $fullDestPath, 90);
                break;
            case 'image/png':
                $result = imagepng($thumbnail, $fullDestPath, 9);
                break;
            case 'image/gif':
                $result = imagegif($thumbnail, $fullDestPath);
                break;
            case 'image/webp':
                $result = imagewebp($thumbnail, $fullDestPath, 90);
                break;
        }

        // Clean up
        imagedestroy($source);
        imagedestroy($thumbnail);

        return $result;
    }

    /**
     * Get file URL
     */
    public function getFileUrl(string $path): string
    {
        return base_url() . $path;
    }

    /**
     * Clean old files
     */
    public function cleanOldFiles(string $directory, int $days = 30): int
    {
        $fullPath = WRITEPATH . $directory;
        $cutoffTime = time() - ($days * 24 * 60 * 60);
        $deletedCount = 0;

        if (!is_dir($fullPath)) {
            return 0;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() < $cutoffTime) {
                unlink($file->getPathname());
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get directory size
     */
    public function getDirectorySize(string $directory): int
    {
        $totalSize = 0;
        $fullPath = WRITEPATH . $directory;

        if (!is_dir($fullPath)) {
            return 0;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $totalSize += $file->getSize();
            }
        }

        return $totalSize;
    }

    /**
     * Validate image dimensions
     */
    public function validateImageDimensions($file, int $maxWidth = null, int $maxHeight = null): array
    {
        $errors = [];

        $imageInfo = getimagesize($file->getTempName());
        if (!$imageInfo) {
            $errors[] = 'Invalid image file';
            return ['success' => false, 'errors' => $errors];
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if ($maxWidth && $width > $maxWidth) {
            $errors[] = "Image width exceeds maximum of {$maxWidth}px";
        }

        if ($maxHeight && $height > $maxHeight) {
            $errors[] = "Image height exceeds maximum of {$maxHeight}px";
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'dimensions' => ['width' => $width, 'height' => $height]
        ];
    }
}
