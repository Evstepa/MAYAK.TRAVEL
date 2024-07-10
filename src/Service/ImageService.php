<?php

namespace App\Service;

use DOMDocument;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageService extends AbstractController
{
    /**
     * общий размер найденных изображений
     * @var int
     */
    public $filesSize = 0;

    /**
     * валидация строки URL
     * @param string $url
     * @return bool
     */
    public function checkUrl(string $url): bool
    {
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }

    /**
     * получение текста html-страницы по URL
     * @param string $url
     * @return string
     */
    private function getHTMLFromURL(string $url): string
    {
        $data = file_get_contents($url);
        if (gettype($data) === 'boolean')
            return '';
        return $data;
    }

    /**
     * извлечение изображений со страницы
     * @param string $url
     * @return array
     */
    public function parseImagesFromHTML(string $url): array
    {
        $HTMLtext = $this->getHTMLFromURL($url);
        if (empty($HTMLtext)) {
            return [];
        };
        $doc = new DOMDocument();
        @$doc->loadHTML($HTMLtext);
        $tags = $doc->getElementsByTagName('img');
        $images = [];
        foreach ($tags as $tag) {
            if ($this->checkUrl($tag->getAttribute('src'))) {
                $images[] = $tag->getAttribute('src');
                $headers = get_headers($tag->getAttribute('src'), 1);
                $this->filesSize += (int) $headers["Content-Length"];
            } else if ($this->checkUrl($url . $tag->getAttribute('src'))) {
                $images[] = $url . $tag->getAttribute('src');
                $headers = get_headers($url . $tag->getAttribute('src'), 1);
                $this->filesSize += (int) $headers["Content-Length"];
            }
        }

        return $images;
    }
}
