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

$iAlbumsPerPage = 12;       // number of albums per page
$itemsPerPage   = 12;       // number of images per page
$iThumbWidth    = 150;      // width of thumbnails
//$iThumbHeight   = 85;       // height of thumbnails
$asExtensions   = array(".jpg",".png",".gif",".JPG",".PNG",".GIF");

/**
 * Create thumbnails from images
 * 
 * @param $sFolder     string
 * @param $sSrc        string
 * @param $sDest       string
 * @param $iThumbWidth integer
 * 
 * @return null
 */
function makeThumb($sFolder, $sSrc, $sDest, $iThumbWidth)
{
    $sFilename = $sFolder . '/' . $sSrc;
    $oSourceImage = imagecreatefromjpeg($sFilename);
    $iWidth = imagesx($oSourceImage);
    $iHeight = imagesy($oSourceImage);

    $iThumbHeight = floor($iHeight * ($iThumbWidth / $iWidth));

    $oVirtualImage = imagecreatetruecolor($iThumbWidth, $iThumbHeight);

    imagecopyresampled(
        $oVirtualImage,
        $oSourceImage,
        0,
        0,
        0, 
        0,
        $iThumbWidth,
        $iThumbHeight,
        $iWidth,
        $iHeight
    );

    imagejpeg($oVirtualImage, $sDest, 100);
}

/**
 * Display pagination
 * 
 * @param $iNumPages    integer
 * @param $sUrlVars     string
 * @param $iCurrentPage integer
 * 
 * @return null
 */
function printPagination($iNumPages, $sUrlVars, $iCurrentPage)
{        
    if ($iNumPages > 1) {
        if ($iCurrentPage > 1) {
            $iPrevPage = $iCurrentPage - 1;
            echo '<a href="?' . $sUrlVars . 'page=' . $iPrevPage . '">&laquo;</a> ';
        } 

        for ($e = 0; $e < $iNumPages; $e++) {
            $sPage = $e + 1;
            if ($sPage == $iCurrentPage) {
                $sClass = 'current-paginate';
            } else {
                $sClass = 'paginate';
            }
            echo '<a class="' . $sClass . '" href="?' . $sUrlVars . 'page=' .
                $sPage . '">' . $sPage . '</a>';
        }
        if ($iCurrentPage != $iNumPages) {
            $iNextPage = $iCurrentPage + 1;
            echo ' <a href="?' . $sUrlVars . 'page=' . $iNextPage . '">&raquo;</a>';
        }
    }
}

if (!isset($_GET[$sSubFolder]) || !is_dir($sSrcPath)) {
    $asFolders = scandir($sMainFolder, 0);
    $asIgnore  = array('.', '..', 'thumbs');
    $asAlbums = array();
    $asCaptions = array();
    $asRandomPics = array();

    foreach ($asFolders as $sAlbum) {
        if (!in_array($sAlbum, $asIgnore)) {     
            array_push($asAlbums, $sAlbum);
            $sCaption = substr($sAlbum, 0, 20);
            array_push($asCaptions, $sCaption);
            $asRandDirs = glob(
                $sMainFolder . '/' . $sAlbum . '/thumbs/*.*',
                GLOB_NOSORT
            );
            $sRandPic  = $asRandDirs[array_rand($asRandDirs)];
            array_push($asRandomPics, $sRandPic);
        }
    }

    if (count($asAlbums) == 0) {
        echo 'There are currently no albums.';
    } else {
        $iNumPages = ceil(count($asAlbums) / $iAlbumsPerPage);

        if (isset($_GET['page'])) {
            $iCurrentPage = $_GET['page'];
            if ($iCurrentPage > $iNumPages) {
                $iCurrentPage = $iNumPages;
            }
        } else {
            $iCurrentPage = 1;
        }
        $start = ($iCurrentPage * $iAlbumsPerPage) - $iAlbumsPerPage;
        echo 
            '<div class="titlebar">
                <h1>Photo Gallery</h1>
                <h3>' . count($asAlbums) . ' albums</h3>
              </div>';
        echo '<div class="thumb-grid">';
        for ($i = $start; $i < $start + $iAlbumsPerPage; $i++) {
            if (isset($asAlbums[$i])) {
                echo 
                '<a href="'.$_SERVER['PHP_SELF'] . '?album=' . 
                    urlencode($asAlbums[$i]) . '">
                    <div class="thumb shadow">
						<div class="thumb-wrapper">
                            <img src="'. $asRandomPics[$i] . 
                                '" width="'. $iThumbWidth . '" />
					    </div>
					    <span class="caption">'. $asCaptions[$i] . '</span>
                    </div>
                </a>';
            }
        }
        echo '</div>';
        echo '<div align="center" class="paginate-wrapper">';
        $sUrlVars = "";
        printPagination($iNumPages, $sUrlVars, $iCurrentPage);
        echo '</div>';
    }
} else {
    // display photos in album
    $asSrcFiles = scandir($sSrcPath);
    $asFiles = array();
    $asCaptions = array();

    if (is_array($asSrcFiles)) {
        foreach ($asSrcFiles as $sFile) {
            $sExt = strrchr($sFile, '.');

            if (in_array($sExt, $asExtensions)) {
                array_push($asFiles, $sFile);
                $sCaption = substr(stristr($sFile, $sExt, true), 0, 25);
                array_push($asCaptions, $sCaption);

                if (!is_dir($sSrcPath . '/thumbs')) {
                    mkdir($sSrcPath . '/thumbs');
                    chmod($sSrcPath . '/thumbs', 777);
                    chown($sSrcPath.'/thumbs', 'apache');
                }

                $sThumb = $sSrcPath . '/thumbs/' . $sFile;
                if (!file_exists($sThumb)) {
                    makeThumb($sSrcPath, $sFile, $sThumb, $iThumbWidth);
                }
            }
        }
    }

    if (count($asFiles) == 0) {
        echo 'There are no photos in this album!';
    } else {
        $iNumPages = ceil(count($asFiles) / $itemsPerPage);
        if (isset($_GET['page'])) {
            $iCurrentPage = $_GET['page'];
            if ($iCurrentPage > $iNumPages) {
                $iCurrentPage = $iNumPages;
            }
        } else {
            $iCurrentPage = 1;
        }
        $start = ($iCurrentPage * $itemsPerPage) - $itemsPerPage;
        echo 
            '<div class="titlebar">
                <h1>' . $_GET[$sSubFolder] . '</h1>
                <h3>
                    <a href="' . $_SERVER['PHP_SELF'] . '">View All Albums</a>
                    | ' . count($asFiles) . ' images
                </h3>
            </div>';
        echo '<div class="thumb-grid">';
        for ($i = $start; $i < $start + $itemsPerPage; $i++) {
            if (isset($asFiles[$i]) && is_file($sSrcPath . '/'. $asFiles[$i])) {
                echo
                    '<a href="'. $sSrcPath . '/'. $asFiles[$i] .
                        '" class="photo-gallery" rel="photo-gallery">
                        <div class="thumb shadow">
	                        <div class="thumb-wrapper">
                                <img src="' . $sSrcPath . '/thumbs/' . $asFiles[$i] .
                                    '" width="' . $iThumbWidth . '" alt="" />
                            </div>
                            <span class="caption">'. $asCaptions[$i] . '</span>
                        </div>
                    </a>';
            } else {
                if (isset($asFiles[$i])) {
                    echo $asFiles[$i];
                }
            }
        }
        echo '</div>';
        echo '<div align="center" class="paginate-wrapper">';
        $sUrlVars = "album=".urlencode($_GET[$sSubFolder]) . "&amp;";
        printPagination($iNumPages, $sUrlVars, $iCurrentPage);
        echo '</div>';
    }
}
?>