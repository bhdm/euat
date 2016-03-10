<?php
namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('anons', array($this, 'anonsFilter')),
            new \Twig_SimpleFilter('minidesc', array($this, 'minidescFilter')),
        );
    }

    public function anonsFilter($text)
    {
        mb_internal_encoding("UTF-8");
        $text = strip_tags($text);
//        $text = str_replace('&nbsp;','',$text);
//        $text = str_replace('&nbsp;','',$text);
        $text = mb_substr($text,0,400);
        return $text.'...';
    }
    public function minidescFilter($text)
    {
        mb_internal_encoding("UTF-8");
        $text = strip_tags($text);
        $text = mb_substr($text,0,200);
        return $text.'...';
    }

    public function getName()
    {
        return 'app_extension';
    }
}