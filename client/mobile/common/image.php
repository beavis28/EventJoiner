<?php
class ImageUtil{

    public static function contentType($image) {          // Image binary
        $res = '';
        $head = substr($image, 0, 8);

        if (strncmp("\x89PNG\x0d\x0a\x1a\x0a", $head, 8) == 0) {
            $res = 'image/png';
        } else if (strncmp('BM', $head, 2) == 0) {
            $res = 'image/bmp';
        } else if (strncmp('GIF87a', $head, 6) == 0 || strncmp('GIF89a', $head, 6) == 0) {
            $res = 'image/gif';
        } else if (strncmp("\xff\xd8", $head, 2) == 0) {
            $res = 'image/jpeg';
        } else {
            $res = '';
        }
        return $res;
    }

    public static function isSupport($image) {
        $res = self::contentType($image);
        if (strcmp($res, 'image/png')) {
            return true;
        }
        else if (strcmp($res, 'image/gif')) {
            return true;
        }
        else if (strcmp($res, 'image/jpeg')) {
            return true;
        }
        return false;
    }
}
?>