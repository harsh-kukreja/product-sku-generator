<?php


namespace App\Helpers;


class ViewHelper {
    public static function controlLinkButton($icon_code, $color, $id, $link, $class_name, $text="") {
        return '<a href="'.$link.'" id="'.$id.'" class="'.$class_name.' '.$icon_code.' btn '.$color.'">'.$text.'</a>';
    }

    public static function controlModalButton($icon_code, $color, $id, $class_name, $modal_id) {
        return '<button id="'.$id.'" class="'.$class_name.' '.$icon_code.' btn '.$color.'" data-toggle="modal" data-target="#'.$modal_id.'"></button>';
    }

    public static function controlImage($link) {
        return "<img src='$link' class='img img-thumbnail' alt='Could not load image' />";
    }

    public static function controlText($text) {
        return "<p class='text-wrap text-xs font-weight-normal text-center mb-0'>".$text."</p>";
    }

}
