<?php

namespace Drupal\setup_project\Services;

class Utilities{
    function breadcrumb($links, $text){
        $output = '<div class="position-absolute top-9">';
        foreach($links as $link){
            $output = $output . $link . ' > ' ;
        }
        $output .= $text . '</div>';
        return $output;
    }
}
