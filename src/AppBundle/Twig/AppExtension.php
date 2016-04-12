<?php
namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('anons', array($this, 'anonsFilter')),
            new \Twig_SimpleFilter('minidesc', array($this, 'minidescFilter')),
            new \Twig_SimpleFilter('succesAttempt', array($this, 'succesAttemptFilter')),
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

    public function succesAttemptFilter($date){
        $date = time($date)+(60*20);
        $nowDate = time();
        $date = $date - $nowDate;
        if ($date > 0){
            $date = $date / 60;
        }else{
            $date = 0;
        }
        return $date;
    }

    public function getName()
    {
        return 'app_extension';
    }
}