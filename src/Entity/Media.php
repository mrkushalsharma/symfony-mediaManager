<?php


namespace MrkushalSharma\MediaManager\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Media
 * @author  Kushal Sharma <mrkushalsharma@gmail.com>
 * @package MrkushalSharma\MediaManager\Entity
 */
class Media
{
    const MEDIA_THUMB_WIDTH = 150;
    const MEDIA_THUMB_HEIGHT = 150;
    const MEDIA_MEDIUM_WIDTH = 300;
    const MEDIA_MEDIUM_HEIGHT = 200;
    const MEDIA_LARGE_WIDTH = 640;
    const MEDIA_LARGE_HEIGHT = 426;

    const MEDIA_SIZE_THUMBNAIL = 'thumbnail';
    const MEDIA_SIZE_MEDIUM = 'medium';
    const MEDIA_SIZE_LARGE = 'large';
    const MEDIA_SIZE_ORIGINAL = 'original';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var UploadedFile
     * @Assert\File(
     *     maxSize = "10M",
     *     mimeTypes = {"image/*"},
     *     maxSizeMessage = "The maximum allowed file size is 10MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed."
     * )
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=255, nullable=true)
     */
    private $fileType;

    /**
     * @var string
     * @ORM\Column(name="file_size", type="string", nullable=true)
     */
    private $fileSize;

    /**
     * @var string
     * @ORM\Column(name="dimensions", type="string", nullable=true)
     */
    private $dimensions;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="thumbnail_url", type="string", nullable=true)
     */
    private $thumbnailUrl;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="caption",type="string", nullable=true)
     */
    private $caption;

    /**
     * @var string
     *
     * @ORM\Column(name="altName", type="string", length=255, nullable=true)
     */
    private $altName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(name="options", type="array", nullable=true)
     */
    private $options = [];

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename( $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function removeFile($uploadDir){
        $file = $uploadDir.'/'.$this->filename;
        if(file_exists($file) and is_file($file)){
            unlink($file);
        }
    }

    public function removeThumbnail($thumbnailDir){
        $file = $thumbnailDir.'/'.$this->filename;
        if(file_exists($file) and is_file($file)){
            unlink($file);
        }
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType( $fileType)
    {
        $this->fileType = $fileType;
    }

    /**
     * @return string
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param string $fileSize
     */
    public function setFileSize( $fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return string
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * @param string $dimensions
     */
    public function setDimensions( $dimensions)
    {
        $this->dimensions = $dimensions;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl( $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * @param string $thumbnailUrl
     */
    public function setThumbnailUrl($thumbnailUrl)
    {
        $this->thumbnailUrl = $thumbnailUrl;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle( $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     */
    public function setCaption( $caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return string
     */
    public function getAltName()
    {
        return $this->altName;
    }

    /**
     * @param string $altName
     */
    public function setAltName($altName)
    {
        $this->altName = $altName;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription( $description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $sizes = [];

        if(strpos($this->getFileType(), 'image') !== false and strpos($this->getFileType(), 'gif') === false ){
            if($this->options and !empty($this->options)){
                foreach(array_reverse($this->options) as $o)
                {
                    if($map = $this->sizeMapping($o)){
                        $sizes[$o] = $map;
                    }
                }
            }else{
                $sizes[self::MEDIA_SIZE_THUMBNAIL] = $this->sizeMapping(self::MEDIA_SIZE_THUMBNAIL);
                $sizes[self::MEDIA_SIZE_ORIGINAL] = $this->sizeMapping(self::MEDIA_SIZE_ORIGINAL);
            }
        }else{
            $sizes[self::MEDIA_SIZE_ORIGINAL] = 'Original';
        }

        return $sizes;
    }

    public function sizeMapping($key)
    {
        $mappings = [
            self::MEDIA_SIZE_THUMBNAIL => sprintf('Thumbnail - %s x %s', self::MEDIA_THUMB_WIDTH, self::MEDIA_THUMB_HEIGHT),
            self::MEDIA_SIZE_MEDIUM => sprintf('Medium - %s x %s', self::MEDIA_MEDIUM_WIDTH, self::MEDIA_MEDIUM_HEIGHT),
            self::MEDIA_SIZE_LARGE => sprintf('Large - %s x %s', self::MEDIA_LARGE_WIDTH, self::MEDIA_LARGE_HEIGHT),
            self::MEDIA_SIZE_ORIGINAL => sprintf('Original - %s', $this->getDimensions()),
        ];

        return (isset($mappings[$key])) ? $mappings[$key] : null;
    }

    public function jsonOptions(){
        return json_encode($this->getOptions());
    }
    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'medias';
    }
}