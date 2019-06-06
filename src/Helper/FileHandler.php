<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FileHandler
{

    private $targetDirectory;

    public function __construct(String $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function uploadFile(UploadedFile $file, String $subDir, String $extension = 'png')
    {
        $fileName = md5(uniqid()).'.'.$extension;

        try {
            $file->move($this->getTargetDirectory().$subDir, $fileName);
        } catch (FileException $e) {
            return new JsonResponse('', Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        return $fileName;
    }

    public function downloadFile(?String $fileName, String $subDir)
    {
        if($fileName && (file_exists($this->getTargetDirectory().$subDir.'/'.$fileName)))
            return new BinaryFileResponse($this->getTargetDirectory().$subDir.'/'.$fileName);
        return new Response('', Response::HTTP_NOT_FOUND);

    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}