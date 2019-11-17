<?php
/**
 * This file contains variables used by multiple files.
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
$sMainFolder = 'albums';
$sSubFolder = 'album';

$sSrcPath = '';
if (isset($_GET[$sSubFolder])) {
    $sSrcPath = $sMainFolder . '/' . $_GET[$sSubFolder]; // individual album folder
}
?>
