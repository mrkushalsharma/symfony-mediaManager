<?php


namespace MrkushalSharma\MediaManager\Controller;

use AndreaSprega\Bundle\BreadcrumbBundle\Annotation\Breadcrumb;
use Doctrine\ORM\EntityManagerInterface;
use MrkushalSharma\MediaManager\Entity\Media;
use MrkushalSharma\MediaManager\Form\MediaType;
use MrkushalSharma\MediaManager\Service\MediaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MediaController
 * @package MrkushalSharma\MediaManager\Controller
 */
class MediaController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * MediaAjaxController constructor.
     * @param MediaService $mediaService
     * @param EntityManagerInterface $em
     */
    public function __construct(MediaService $mediaService, EntityManagerInterface $em)
    {
        $this->mediaService = $mediaService;
        $this->em = $em;
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
        return $this->render('@MrkushalSharma/Media/index.html.twig', [
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
        return $this->render('@MrkushalSharma/Media/add.html.twig');
    }

}