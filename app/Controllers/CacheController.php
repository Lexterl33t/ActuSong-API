<?php

namespace App\Controllers;

class CacheController
{
    private $lifeTime;
    private $folder;

    public function __construct($lifeTime, $folder)
    {
        $this->lifeTime = $lifeTime;
        $this->folder = $folder;
    }

    public function write($filename, $data)
    {
        file_put_contents(dirname(__FILE__).'/'.$this->folder.'/'.$filename, $data);
    }

    public function read($filename)
    {
        if(file_exists(dirname(__FILE__).'/'.$this->folder.'/'.$filename)) {
            $life = (time()-filemtime(dirname(__FILE__).'/'.$this->folder.'/'.$filename))/60;
            if($life > $this->lifeTime) {
                unlink(dirname(__FILE__).'/'.$this->folder.'/'.$filename);
                return false;
            }
            return file_get_contents(dirname(__FILE__).'/'.$this->folder.'/'.$filename);
        } else {
            return false;
        }
    }
}