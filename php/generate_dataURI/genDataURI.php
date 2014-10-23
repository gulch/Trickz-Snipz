<?php
/**
 * @author gulch <contact@gulch.in.ua>
 */

namespace gulch\phpTrickz;


class DataUri
{
    /**
     * @param $path
     * @return string
     */
    public static function make($path)
    {
        $dataURI = '';

        if(file_exists($path))
        {
            $mime_type = mime_content_type($path);
            $data = file_get_contents($path);
            if($data && $mime_type)
            {
                $dataURI = 'data:' . $mime_type . ';base64,' . base64_encode($data);
            }
        }
        else
        {
            throw new \Exception('image: '. $path. ' not found!;');
        }

        return $dataURI;
    }
}