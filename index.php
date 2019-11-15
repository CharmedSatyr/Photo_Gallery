<!DOCTYPE html>
<html>
<head>
<title>
<?php
/** 
 * This file displays a photo gallery.
 * PHP Version 7.3
 * 
 * @category PHP
 * 
 * @package Photo_Gallery
 * 
 * @author Joseph <z@charmed.tech>
 *
 * @license GNU General Public License v3.0
 *
 * @link https://github.com/CharmedSatyr/Photo_Gallery
 */
if (isset($_GET['album'])) {
    echo $_GET['album'];
} else {
    echo 'Photo Gallery';
}
?>
</title>
<link rel="stylesheet" type="text/css" href="gallery.css" />
<link rel="stylesheet" type="text/css" href="colorbox/colorbox.css" />
<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.4.css" />
</head>
<body>
    <div>
        <a href="https://www.charmed.tech">Go back</a>
    </div>
    <div class="gallery">  
        <?php require "gallery.php"; ?>
    </div>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".albumpix").colorbox({rel:'albumpix'});
            $("a.albumpix").fancybox({
                'autoScale': true, 
                'hideOnOverlayClick': true,
            'hideOnContentClick': true
        });
    });
    </script>
</body>
</html>
