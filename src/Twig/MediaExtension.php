<?php


namespace MrkushalSharma\MediaManager\Twig;


use MrkushalSharma\MediaManager\Entity\Media;
use MrkushalSharma\MediaManager\Service\MediaService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaExtension extends AbstractExtension
{

    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * MediaExtension constructor.
     * @param MediaService $mediaService
     */
    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('render_media_date_filter', [
                $this,
                'renderMediaDateFilter'
            ]),

            new TwigFunction(
                'render_media_modal', [$this, 'getModal'], ['is_safe' => ['html']]
            )
        );
    }

    public function renderMediaDateFilter($class = '', $id = 'filterTypeDate'){
        $medias = $this->mediaService->getAllMedia();
        $dates = [];
        $uniqueDates = [];
        foreach ($medias as $media){
            if($media instanceof Media){
                $date = $media->getCreatedAt();
                if($date instanceof \DateTime){
                    $dates[] = [
                        'label' => $date->format('F , Y'),
                        'value' => $date->format('Y-m')
                    ];
                }
            }
        }

        foreach ($dates as $date) {
            $uniqueDates[$date['value']] = $date;
        }

        $html = "<select class='{$class}' id='{$id}'><option value='all'>All dates</option>";
        foreach($uniqueDates as $d){
            $html .= "<option value='{$d['value']}'>{$d['label']}</option>";
        }
        $html .= "</select>";
        return $html;
    }


    public function getModal($modalName, $modalSize = ''){
        return  "
         <div class=\"modal fade custom-modal\" id=\"{$modalName}\" role=\"dialog\" aria-label=\"true\">
        <div class=\"modal-dialog {$modalSize}\">
            <div class=\"modal-content\">
                <div class=\"modal-header bg-indigo\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                    <h6 class=\"modal-title\"></h6>
                </div>
                <div class=\"modal-body\" id=\"detail\"></div>
                <div class=\"modal-footer\" id=\"footer-detail\"></div>
            </div>
        </div>
    </div>
        ";
    }
}