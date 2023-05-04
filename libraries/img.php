<?php
function imageResize($thumbnailImage,$width,$height,$tarwidth,$tarheight) {
    // new thumbnail size
    $targetWidth = $tarwidth;
    $targetHeight = $tarheight;
    $resizeThumbnailImge=imagecreatetruecolor($targetWidth,$targetHeight);
    imagecopyresampled($resizeThumbnailImge,$thumbnailImage,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
    return $resizeThumbnailImge;
}

function createThumbnail($imageType, $file, $sourceWidth, $sourceHeight,$tarwidth,$tarheight,$folderPath, $fileNewName){
	
	switch ($imageType) {
            case IMAGETYPE_PNG:
                $thumbnailImage = imagecreatefrompng($file); 
                $resizeThumbnailImge = imageResize($thumbnailImage,$sourceWidth,$sourceHeight,$tarwidth,$tarheight);
                imagepng($resizeThumbnailImge, $folderPath . "/thumbnail/" . $fileNewName);
                break;
            case IMAGETYPE_GIF:
                $thumbnailImage = imagecreatefromgif($file); 
                $resizeThumbnailImge = imageResize($thumbnailImage,$sourceWidth,$sourceHeight,$tarwidth,$tarheight);
                imagegif($resizeThumbnailImge,$folderPath . "/thumbnail/" . $fileNewName);
                break;
            case IMAGETYPE_JPEG:
                $thumbnailImage = imagecreatefromjpeg($file); 
                $resizeThumbnailImge = imageResize($thumbnailImage,$sourceWidth,$sourceHeight,$tarwidth,$tarheight);
                imagejpeg($resizeThumbnailImge,$folderPath . "/thumbnail/" . $fileNewName );
                break;
            default:
                echo "Invalid Image type.";
                exit;
                break;
    }	
}
?>