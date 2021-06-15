<?php

namespace App\Service;

/**
 * Class Image
 * @package App\Service
 */
class Image
{
    /**
     * @param $entity
     * @param $link
     * @return int[]
     */
	public function getimagesize($entity, $link) {
        $width = $height = 0;
        $chemin = '';
        
        if($entity == 'event') {
        	$chemin = 'upload/cms/events-public/';
        } elseif ($entity == 'article') {
        	$chemin = 'upload/cms/articles-public/';
        } elseif ($entity == 'page') {
            $chemin = 'upload/cms/pages-public/';
        } elseif ($entity == 'project') {
            $chemin = 'upload/approved-projects/approved-gallery-photo/';
        } elseif ($entity == 'document') {
            $chemin = 'upload/slider/';
        }
        
        if (file_exists($chemin . $link)) {
            list($width, $height) =  getimagesize($chemin . $link);
        }
		return [$width,$height];
	}

}