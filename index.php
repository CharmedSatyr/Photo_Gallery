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
             * @author CharmedSatyr <z@charmed.tech>
             *
             * @license GNU General Public License v3.0
             *
             * @link https://github.com/CharmedSatyr/Photo_Gallery
             */
            require './globals.php';
            if (isset($_GET[$sSubFolder]) && is_dir($sSrcPath)) {
                echo $_GET[$sSubFolder];
            } else {
                echo 'Photo Gallery';
            }
            ?>
            </title>
            <link rel="stylesheet" type="text/css" href="./styles/gallery.css" />
            <link rel="stylesheet" type="text/css" 
                href="./styles/colorbox/colorbox.css" />
            <link rel="stylesheet" type="text/css" href="./styles/index.css" />
            <link href="https://fonts.googleapis.com/css?family=Lato&display=swap"
                rel="stylesheet">
        </head>
<body>
    <div>
        <a href="https://www.charmed.tech">Go back</a>
    </div>
    <div class="gallery">  
        <?php require "./gallery.php"; ?>
    </div>
    <script src="./js/jquery-3.4.1.min.js">
    </script>
    <script type="text/javascript" src="./js/jquery.colorbox-min.js"></script>
    <script type="text/javascript">
        $(document).ready(() => {
            $(".albumpix").colorbox({rel:'albumpix'});
        });
    </script>
</body>
</html>
