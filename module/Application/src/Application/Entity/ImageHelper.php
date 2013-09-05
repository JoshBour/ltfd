<?php
/**
 * User: Josh
 * Date: 5/9/2013
 * Time: 12:49 μμ
 */

namespace Application\Entity;


class ImageHelper {

    public static function getImageExtension($name){
        $extension = '';
        switch($name){
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            default:
                return false;
        }
        return $extension;
    }
}