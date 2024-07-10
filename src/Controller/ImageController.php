<?php

namespace App\Controller;

use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/', name: 'url')]
    /**
     * Ввод URL
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUrl(): Response
    {
        return $this->render('image/index.html.twig', []);
    }

    #[Route('/images', name: 'images')]
    /**
     * Вывод найденных изображений
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Service\ImageService $imageService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function imageList(Request $request, ImageService $imageService): Response
    {
        $check = $imageService->checkUrl($request->get('url'));
        $images = [];
        if ($check) {
            $images = $imageService->parseImagesFromHTML($request->get('url'));
        }

        return $this->render('image/imageList.html.twig', [
            'check' => $check,
            'imageCount' => count($images),
            'imageSize' => round($imageService->filesSize / 1024 / 1024, 4),
            'images' => $images,
        ]);
    }
}
