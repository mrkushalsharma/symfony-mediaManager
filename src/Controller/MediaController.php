<?php


namespace MrkushalSharma\MediaManager\Controller;

use AndreaSprega\Bundle\BreadcrumbBundle\Annotation\Breadcrumb;
use Doctrine\ORM\EntityManagerInterface;
use MrkushalSharma\MediaManager\Service\MediaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MediaController
 * @package MrkushalSharma\MediaManager\Controller
 * @Breadcrumb({"label" = "Media", "route" = "fn_media_list" })
 */
class MediaController extends AbstractController
{
    private $em;

    private $mediaService;

    public function __construct(EntityManagerInterface $em, MediaService $mediaService)
    {
        $this->em = $em;
        $this->mediaService = $mediaService;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/media/list", name="fn_media_list")
     * @Breadcrumb({"label" = "List" })
     */
    public function listMedia(Request $request){
        $mode = ($request->get('mode') == 'grid') ? 'grid' : 'list';
        $filters = $request->query->all();
        $medias = $this->mediaService->getPaginatedMedia($filters);
        return $this->render('Media/Templates/Media/index.html.twig', [
            'medias' => $medias,
            'mode'=>$mode
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/media/add", name="fn_media_add")
     */
    public function addMedia(Request $request){
        return $this->render('Media/Templates/Media/add.html.twig');
    }

}