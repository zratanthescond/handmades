<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductCategoryRepository;
//use App\Service\Image\ImagePathGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(
        Request $request,
        ProductRepository $productRepository,
        ProductCategoryRepository $productCategoryRepository,
        BlogRepository $blogRepository
    ): Response {
        // Récupérer le nom d'hôte
        $hostname = "https://www.paramall.tn/";

        // On initialise un tableau pour lister les URL
        $urls = [];
        $fs = new FileSystem();
        $fs->dumpFile('/home/fsxflhi/app/sitemap.xml', 'zebby');
        // On ajoute les URLs statiques
        // $urls[] = ['loc' => $this->generateUrl('home')];
        // $urls[] = ['loc' => $this->generateUrl('blog_index')];
        // $urls[] = ['loc' => $this->generateUrl('app_login')];

        // On ajoute les URLs dynamiques
        $content = '<?xml version="1.0" encoding="UTF-8"?>
       <urlset
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/</loc>        
             </url>';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/content/1</loc>        
             </url>';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/shop</loc>        
             </url>';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/promo</loc>        
             </url>';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/shop/brands</loc>        
             </url>';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/blog</loc>        
             </url>';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/contact</loc>        
             </url>';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/shop/type/offrets</loc>        
             </url>';
        $content .= '
            <url>
              <loc>https://www.paramall.tn/help</loc>        
             </url>';
        foreach ($productCategoryRepository->findAll() as $post) {


            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $post->getTitle())));

            $content .= '
            <url>
              <loc>' . $hostname . 'shop/category/' . htmlspecialchars($slug) . '-' . $post->getId()   . '</loc>        
             </url>';
        }
        foreach ($blogRepository->findAll() as $post) {
            //dd($post->getThumbnail());



            $imagexml = '
                <image:image>
                  <image:loc> https://api.paramall.tn/upload/img/' . htmlspecialchars($post->getImage()) . '</image:loc>
                  <image:title>' . htmlspecialchars($post->getTitle()) . '</image:title>
                </image:image>';


            $content .= '
            <url>
              <loc>' . $hostname . 'blog/' . $post->getId() . '/' . htmlspecialchars($post->getSlug())  . '</loc>
               <lastmod>' . $post->getUpdatedAt()->format('Y-m-d') . '</lastmod>
              ' . $imagexml . '
             </url>';
        }
        foreach ($productRepository->findAll() as $post) {
            //dd($post->getThumbnail());



            $imagexml = '
                <image:image>
                  <image:loc> https://api.paramall.tn/upload/img/' . htmlspecialchars($post->getThumbnail()) . '</image:loc>
                  <image:title>' . htmlspecialchars($post->getTitle()) . '</image:title>
                </image:image>';


            $content .= '
            <url>
              <loc>' . $hostname . 'shop/product/' . $post->getId() . '/' . htmlspecialchars($post->getSlug())  . '</loc>
               <lastmod>' . $post->getCreatedAt()->format('Y-m-d') . '</lastmod>
              ' . $imagexml . '
             </url>';
        }
        $content .= '</urlset>';
        //  print_r($urls);

        // Fabriquer la réponse
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [

                'hostname' => $hostname
            ]),
            200
        );

        $fs->dumpFile('/home/fsxflhi/app/sitemap.xml', $content);
        // Ajout des entêtes
        $response->headers->set('Content-Type', 'text/xml');

        // Envoyer la réponse
        return $response;
    }
}
