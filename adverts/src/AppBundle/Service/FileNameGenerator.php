<?php

namespace AppBundle\Service;


class FileNameGenerator
{
    public function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}