<?php
// api/src/Controller/MediaObjectController.php

namespace App\Controller;

use App\Entity\MediaObject;
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
    public function downloadObjectAction($id, DownloadHandler $downloadHandler, Security $security, MediaObjectRepository $mediaObjectRespository)
    {
        $authenticatedUser = $security->getUser();
        $object = $mediaObjectRespository->find($id);
        if ($authenticatedUser) {
            return $downloadHandler->downloadObject($object, $fileField = 'file' , $objectClass = null, $fileName = null, $forceDownload = false);
        }
        return $this->json([
            'error' => 'Authentication required.'
        ], 401);
    }
}