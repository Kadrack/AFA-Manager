<?php
// src/Service/PhotoUploader.php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class PhotoUploader
 * @package App\Service
 */
class PhotoUploader
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameters;

    /**
     * @var int
     */
    private int $height;

    /**
     * @var int
     */
    private int $width;

    /**
     * PhotoUploader constructor.
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;

        $this->height = 110;

        $this->width = 90;
    }

    /**
     * @param UploadedFile $file
     * @param int $compression
     * @return string
     */
    public function upload(UploadedFile $file, int $compression = 75): string
    {
        $extension = $file->guessExtension();

        $fileName = md5(microtime()).'.'.$extension;

        $filePath = $this->parameters->get('kernel.project_dir').'/private/image/';

        $file->move($filePath, $fileName);

        $image_info = getimagesize($filePath.$fileName);

        if( $image_info[2] == IMAGETYPE_JPEG )
        {
            $image = imagecreatefromjpeg($filePath.$fileName);
        }
        elseif( $image_info[2] == IMAGETYPE_GIF )
        {
            $image = imagecreatefromgif($filePath.$fileName);
        }
        elseif( $image_info[2] == IMAGETYPE_PNG )
        {
            $image = imagecreatefrompng($filePath.$fileName);
        }

        $ratio = $image_info[0] / $image_info[1];

        if ($this->width / $this->height > $ratio)
        {
            $this->width = $this->height * $ratio;
        }
        else
        {
            $this->height = $this->width / $ratio;
        }

        $new_image = imagecreatetruecolor($this->width, $this->height);

        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $this->width, $this->height, $image_info[0], $image_info[1]);

        $image = $new_image;

        if( $image_info[2] == IMAGETYPE_JPEG )
        {
            imagejpeg($image, $filePath.$fileName, $compression);
        }
        elseif( $image_info[2] == IMAGETYPE_GIF )
        {
            imagegif($image, $filePath.$fileName);
        }
        elseif( $image_info[2] == IMAGETYPE_PNG )
        {
            imagepng($image, $filePath.$fileName);
        }

        $imageFile   = fopen($filePath.$fileName, 'rb' );
        $imageInfo   = pathinfo($filePath.$fileName);
        $imageSize   = filesize ($filePath.$fileName);
        $imageBinary = fread ($imageFile, $imageSize);

        fclose ( $imageFile );

        $imageBase64 = "data:image/".$imageInfo['extension'].";base64,".str_replace ("\n", "", base64_encode($imageBinary));

        unlink($filePath.$fileName);

        return $imageBase64;
    }
}
