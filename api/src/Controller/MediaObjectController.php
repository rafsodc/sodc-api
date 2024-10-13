<?php
// api/src/Controller/MediaObjectController.php

namespace App\Controller;

use App\Entity\MediaObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;
use App\Repository\MediaObjectRepository;

class MediaObjectController extends AbstractController
{
    /**
     * @Route("/files/{id}/{file}", name="app_media", methods={"GET"})
     */
    public function downloadObjectAction($id, DownloadHandler $downloadHandler, Security $security, MediaObjectRepository $mediaObjectRepository): Response
    {
        $authenticatedUser = $security->getUser();

        // Check if the user is authenticated and has appropriate permissions
        if (!$authenticatedUser || !$this->isGranted('ROLE_USER', $authenticatedUser)) {
            return new JsonResponse(['error' => 'Authentication required.'], 401);
        }

        // Find the media object by its ID
        $mediaObject = $mediaObjectRepository->find($id);
        if (!$mediaObject) {
            return new JsonResponse(['error' => 'File not found.'], 404);
        }

        // Use the DownloadHandler to serve the file, checking the 'file' field
        return $downloadHandler->downloadObject(
            $mediaObject,
            $fileField = 'file',
            $objectClass = null,
            $fileName = null,
            $forceDownload = false // Set to true to force download behavior
        );
    }
}