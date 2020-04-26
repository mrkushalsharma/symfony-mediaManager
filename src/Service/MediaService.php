<?php


namespace MrkushalSharma\MediaManager\Service;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use MrkushalSharma\MediaManager\Entity\Media;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

class MediaService
{

    private $pager;

    private $firstIndex = 1;

    private $requestStack;

    private $request;

    private $limit;

    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var KernelInterface
     */
    private $appKernel;

    public $uploadDir = 'uploads/media';

    /**
     * MediaService constructor.
     * @param EntityManagerInterface $em
     * @param KernelInterface $appKernel
     * @param RequestStack $request
     * @param RouterInterface $router
     */
    public function __construct(
        KernelInterface $appKernel,
        EntityManagerInterface $em,
        RequestStack $request,
        RouterInterface $router)
    {
        $this->em = $em;
        $this->appKernel = $appKernel;
        $this->requestStack = $request;
        $this->request = $request->getCurrentRequest();
        $this->router = $router;
    }

    public function apiPaginationService(QueryBuilder $qb, $perPage, $currentPage)
    {
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($perPage);
        $pagerfanta->setCurrentPage($currentPage);
        return $pagerfanta;
    }

    public function getPaginatedMedia($filters = []){

        if(array_key_exists('mode', $filters)) {
            if ($filters['mode'] == 'grid') {
                return $this->getPagerFanta(
                    $this->em->getRepository(Media::class)->getAllMediaQuery($filters)
                )->setMaxPerPage(40);
            }
        }
        return $this->getPagerFanta(
            $this->em->getRepository(Media::class)->getAllMediaQuery($filters)
        );
    }

    public function getAllMedia($filters=[]){
        $qb = $this->em->getRepository(Media::class)->getAllMediaQuery($filters);
        return $qb->getQuery()->getResult();
    }

    public function getFileUploadMaxSize() {
        static $max_size = -1;

        if ($max_size < 0) {
            $post_max_size = $this->parseSize(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            $upload_max = $this->parseSize(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    public function parseSize($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }


    public function resizeImage(Media $media, $desiredWidth = Media::MEDIA_THUMB_WIDTH, $desiredHeight = Media::MEDIA_THUMB_HEIGHT, $destinationFolder = Media::MEDIA_SIZE_THUMBNAIL){

        if(strpos($media->getFile()->getClientMimeType(), 'image') !== false){


            $imageName = $media->getFilename();
            $path = $this->uploadDir . '/' . $imageName;
            $thumbDir = $this->uploadDir . '/' . $destinationFolder;

            $rootDir =  $this->appKernel->getProjectDir();

            if(!is_dir($rootDir . '/public/' . $thumbDir)){
                mkdir($rootDir . '/public/' . $thumbDir, 0777, true);
            }

            $thumbPath = $thumbDir . '/' . $imageName;
            $dimensions = $this->getImageDimensions($path);

            $width = $dimensions[0];
            $height = $dimensions[1];
            $thumb = imagecreatetruecolor($desiredWidth, $desiredHeight);
            imagesavealpha($thumb, true);

            $imageType = $dimensions[4];

            $isPng = $dimensions[3]['mime'] == 'image/png';

            $color = ($imageType == 'png' or $imageType == 'gif')
                ? imagecolorallocatealpha($thumb, 0, 0, 0, 127)
                : imagecolorallocate($thumb, 255, 255, 255);

            imagefill($thumb, 0, 0, $color);

            if($width <= $desiredWidth and $height <= $desiredHeight)
            {
                $top = $height != $desiredHeight ? round(($desiredHeight - $height) / 2) : 0;
                $left = $width != $desiredWidth ? round(($desiredWidth - $width) / 2) : 0;
                $desiredWidth = $width;
                $desiredHeight = $height;
                imagecopyresampled($thumb, $dimensions[2], $left, $top, 0, 0, $desiredWidth, $desiredHeight, $width, $height);
            }

            if($height <= $desiredHeight and $width > $desiredWidth){
                $top = $height != $desiredHeight ? round(($desiredHeight - $height) / 2) : 0;
                $left = round(($desiredWidth - $width) / 2);
                $desiredHeight = $height;
                $desiredWidth = $width;
                imagecopyresampled($thumb, $dimensions[2], $left, $top, 0, 0, $desiredWidth, $desiredHeight, $width, $height);
            }

            if($width <= $desiredWidth and $height > $desiredHeight){
                $top = round(($desiredHeight - $height) / 2);
                $left = $width != $desiredWidth ? round(($desiredWidth - $width) / 2) : 0;
                $desiredWidth = $width;
                $desiredHeight = $height;
                imagecopyresampled($thumb, $dimensions[2], $left, $top, 0, 0, $desiredWidth, $desiredHeight, $width, $height);
            }

            if( $width > $desiredWidth && $height > $desiredHeight){

                $targetRatio = $desiredWidth / $desiredHeight;
                $imageRatio = $width / $height;

                if($imageRatio < $targetRatio) {
                    $thumbRatio = $desiredWidth / $width;
                    $thumbHeight = $desiredHeight;
                    $desiredHeight = $height * $thumbRatio;
                    $topthumbstart = round(($thumbHeight - $desiredHeight)/2);
                    $leftthumbstart = 0;
                }else{
                    $thumbRatio = $desiredHeight / $height;
                    $thumbWidth = $desiredWidth;
                    $desiredWidth = $width * $thumbRatio;
                    $topthumbstart = 0;
                    $leftthumbstart = round(($thumbWidth - $desiredWidth)/2);
                }

                imagecopyresampled($thumb, $dimensions[2], $leftthumbstart, $topthumbstart, 0, 0, $desiredWidth, $desiredHeight, $width, $height);
            }

            if($thumb)
            {
                if($imageType == 'gif')
                {
                    imagegif($thumb,$thumbPath);
                }elseif($imageType == 'jpg'){
                    imagejpeg($thumb,$thumbPath,80);
                }else{
                    imagepng($thumb,$thumbPath,8);
                }
                if($destinationFolder == Media::MEDIA_SIZE_THUMBNAIL)
                {
                    $thumbnail_url = $thumbDir.'/'.$media->getFilename();
                    $media->setThumbnailUrl($thumbnail_url);
                }
                imagedestroy($thumb);
                imagedestroy($dimensions[2]);
            }
        }

    }

    public function getImageDimensions($path){

        $mime = getimagesize($path);
        $sourceImage = null;
        $type = 'png';

        if($mime['mime']=='image/png') {
            $sourceImage = imagecreatefrompng($path);
            $type = 'png';
        }

        if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
            $sourceImage = imagecreatefromjpeg($path);
            $type = 'jpg';
        }

        if($mime['mime']=='image/gif') {
            $sourceImage = imagecreatefromgif($path);
            $type = 'gif';
        }

        if (!$sourceImage) {
            throw new \Exception("ERROR:could not create image handle ");
        }

        return [imageSX($sourceImage), imageSY($sourceImage), $sourceImage, $mime, $type];
    }

    public function getArrayPagerFanta($array)
    {
        $this->request = $this->requestStack->getCurrentRequest();

        $adapter = new ArrayAdapter($array);

        $this->pager = new Pagerfanta($adapter);

        $this->limit = $this->setLimit();

        $this->pager->setMaxPerPage($this->limit);

        if ($this->request->get('page')) {
            $this->pager->setCurrentPage($this->request->get('page'));
        }

        return $this->pager;
    }

    public function getPagerFanta(QueryBuilder $builder)
    {
        $this->request = $this->requestStack->getCurrentRequest();

        $this->limit = $this->setLimit();

        $adapter = new DoctrineORMAdapter($builder, false);

        $this->pager = new Pagerfanta($adapter);

        $this->pager->setMaxPerPage($this->limit);

        if ($this->request->get('page')) {
            $this->pager->setCurrentPage($this->request->get('page'));
        }

        $this->firstIndex = (($this->pager->getCurrentPage() - 1) * $this->pager->getMaxPerPage()) + 1;

        return $this->pager;
    }

    public function getFirstIndex()
    {
        return $this->firstIndex;
    }

    private function setLimit()
    {
        $this->request = $this->requestStack->getCurrentRequest();


        if ($this->request->get('limit') and is_numeric($this->request->get('limit'))) {
            return $this->request->get('limit');
        }

        return 20;
    }

    public function getRoute($route = '', $field = '', $extraParams = [])
    {
        if ($route == "" or $route == '#') {
            return '#';
        }

        $this->request = $this->requestStack->getCurrentRequest();

        $params = $this->request->query->all();

        unset($params['page']);
        unset($params['limit']);

        if ($field != '') {
            $params['sort'] = $field;
            $params['order'] = (isset($params['order']))
                ? ($params['order'] == 'desc') ? 'asc' : 'desc'
                : 'asc';
        }

        $params = array_merge($params, $extraParams);

        $router = $this->router->generate($route, $params);

        return $router;
    }
}