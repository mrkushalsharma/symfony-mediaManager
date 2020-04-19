<?php


namespace MrkushalSharma\MediaManager\Controller;


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
 * Class MediaAjaxController
 * @package MrkushalSharma\MediaManager\Controller
 */
class MediaAjaxController extends AbstractController
{
    /**
     * @var MediaService
     */
    public $mediaService;
    /**
     * @var EntityManagerInterface
     */
    public $em;

    public $uploadDir = 'uploads/media';

    /**
     * @var KernelInterface
     */
    private $appKernel;

    /**
     * MediaAjaxController constructor.
     * @param MediaService $mediaService
     * @param EntityManagerInterface $em
     * @param KernelInterface $appKernel
     */
    public function __construct(MediaService $mediaService, EntityManagerInterface $em, KernelInterface $appKernel)
    {
        $this->mediaService = $mediaService;
        $this->em = $em;
        $this->appKernel = $appKernel;

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/ajax/media/file/upload", name="fn_media_file_upload")
     */
    public function ajaxMediaFileUpload(Request $request){
        $files = $request->files->all();
        $webPath = $this->appKernel->getProjectDir() . '/public/'; ;
        foreach ($files as $file){
            if($file instanceof UploadedFile){
                $maxSize = $this->mediaService->getFileUploadMaxSize();
                $fileSize = $file->getSize();

                if($maxSize < $fileSize or !$fileSize or $fileSize <= 0){
                    $maxSize = $maxSize / (1024 * 1024);
                    return new JsonResponse([
                        'success' => false,
                        'message' => "File too large. Maximum file size allowed is {$maxSize} MB."
                    ]);
                }

                if(strpos($file->getClientMimeType(), 'image') !== false){
                    $info = getimagesize($file);
                    $info = "{$info[0]} x {$info[1]}";
                }    else    {
                    $info = '';
                }

                $fileName = explode('.',$file->getClientOriginalName());
                $originalFileName = $fileName[0];
                $fileExtension = $fileName[1];

                $media = new Media();
                $media->setFilename($originalFileName.'_'.date('YmdHis').'.'.$fileExtension);
                $media->setFile($file);
                $media->setFileSize($fileSize);
                $media->setFileType($file->getClientMimeType());
                $media->setTitle($file->getClientOriginalName());
                $media->setDimensions($info);
                $media->getFile()->move(
                    $webPath.$this->uploadDir,
                    $media->getFilename()

                );
                $url = $this->uploadDir.'/'.$media->getFilename();
                $media->setUrl($url);
//                $this->mediaService->resizeImage($media);
                $this->em->persist($media);
                try{
                    $this->em->flush();
                    return new JsonResponse([
                        'success' => true,
                        'message' => 'File uploaded',
                        'image'=>$media->getFilename()
                    ]);
                }    catch (\Throwable $e){
                    return new JsonResponse([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
            }
        }
        return new JsonResponse(['success' => false, 'message' => 'Something went wrong.']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/ajax/media/{id}/edit", name="fn_media_edit_detail")
     */
    public function editMedia(Request $request){
        $id = $request->get('id');
        if(!$id){
            return new JsonResponse([
                'success' => false,
                'message' => 'Something went wrong.'
            ]);
        }

        $media = $this->em->getRepository(Media::class)->findOneBy(['id'=>$id, 'deleted'=>false]);
        if(!$media){
            return new JsonResponse([
                'success' => false,
                'message' => 'Something went wrong.'
            ]);
        }

        $form = $this->createForm(MediaType::class,$media);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $media = $form->getData();
            $this->em->persist($media);
            try{
                $this->em->flush();
                $data['success'] = true;
                $data['message'] = 'Media Updated Successfully.';
            }   catch (\Throwable $e){
                $data['success'] = false;
                $data['message'] = $e->getMessage();
            }
        }   else    {
            $data['success'] = false;
            $data['message'] = "Something went wrong.";
        }

        $data['template'] = $this->renderView('@MrkushalSharma/Media/mediaDetail.html.twig', [
            'media' => $media,
            'form' => $form->createView()
        ]);
        $data['success'] = true;

        return new JsonResponse($data);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/media/{id}/delete", name="fn_media_ajax_delete")
     */
    public function deleteMedia(Request $request){
        $id = $request->get('id');
        $media = $this->em->getRepository(Media::class)->findOneBy(['id'=>$id, 'deleted' => false]);
        if($media instanceof Media){
            $media->setDeleted(true);
            try{
                $this->em->flush();
                $data['success'] = true;
                $data['message'] = "Media Deleted Successfully.";
            }   catch (\Exception $e){
                $data['success'] = false;
                $data['message'] = $e->getMessage();
            }
        }   else    {
            $data['success'] = false;
            $data['message'] = "Media Not found.";
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/multimedia/ajax/list",name="fn_site_multimedia_ajax_list")
     * @param Request $request
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $searchValue = $request->get('value');
        $currentPage = $request->get('page') ? $request->get('page') : 1;
        $isUpdate = $request->get('isUpdate') ? true : false;
        $limit = 20;
        $filters = $request->query->all();
        if(!empty($searchValue)){
            $filters['title'] = $searchValue;
        }else{
            $filters['title'] = '';
        }
        $medias = $this->em->getRepository(Media::class)
            ->getAllMediaQuery($filters);
        $data['medias'] = $this->mediaService->apiPaginationService($medias, $limit,$currentPage);
        $countMedia =$this->em->getRepository(Media::class)->countMedia($filters);
        $totalPage = ceil($countMedia/$limit);
        $data['totalPage']=$totalPage;
        $data['currentPage'] = $currentPage;
        if($request->isXmlHttpRequest() && $currentPage >1) {
            $response['template'] = $this->renderView('@MrkushalSharma/Media/Ajax/mediaTemplateList.html.twig', $data);
            return new JsonResponse($response);
        }
        if($request->isXmlHttpRequest() && $isUpdate == true){
            $response['template'] = $this->renderView('@MrkushalSharma/Media/Ajax/mediaTemplateList.html.twig', $data);
            return new JsonResponse($response);
        }
        $response['template'] = $this->renderView('@MrkushalSharma/Media/Ajax/mediaModal.html.twig', $data);
        return new JsonResponse($response);
    }

    /**
     * @Route("/multimedia/ajax/{id}/edit",name="fn_media_ajax_edit")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxImageEdit(Request $request){
        $id = $request->get('id');
        $name = $request->get('name');
        $val = $request->get('value');
        $media = $this->em->getRepository(Media::class)
            ->findOneBy(['id'=>$id, 'deleted'=>false]);
        if($media instanceof Media){
            if($name == 'title'){
                $media->setTitle($val);
            }
            if($name == 'altname'){
                $media->setAltName($val);
            }
            if($name == 'caption'){
                $media->setCaption($val);
            }
            if($name == 'description'){
                $media->setDescription($val);
            }
            $this->em->persist($media);
            try {
                $this->em->flush();
                $response['status'] = true;
                $response['message'] = 'success';
            }catch (\Exception $ex){
                $response['status'] = false;
                $response['message'] = $ex->getMessage();
            }
        }else{
            $response['status'] = false;
            $response['message'] = 'error';
        }
        return new JsonResponse($response);
    }

}