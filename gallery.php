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
$sMainFolder    = 'albums';
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
        echo 'Image ' . $iCurrentPage . ' of ' . $iNumPages;

        if ($iCurrentPage > 1) {
            $iPrevPage = $iCurrentPage - 1;
            echo '<a href="?' . $sUrlVars . 'p=' . $iPrevPage . 
                '">&laquo;&laquo;</a> ';
        } 

        for ($e = 0; $e < $iNumPages; $e++) {
            $sPage = $e + 1;
            if ($sPage == $iCurrentPage) {
                $sClass = 'current-paginate';
            } else {
                $sClass = 'paginate';
            }
            echo '<a class="' . $sClass . '" href="?' . $sUrlVars . 'p=' . 
                $sPage . '">' . $sPage . '</a>';
        }
        if ($iCurrentPage != $iNumPages) {
            $iNextPage = $iCurrentPage + 1;
            echo ' <a href="?' . $sUrlVars . 'p=' . $iNextPage . 
                '">&raquo;&raquo;</a>';
        }
    }
}

if (!isset($_GET['album'])) {
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

        if (isset($_GET['p'])) {
            $iCurrentPage = $_GET['p'];
            if ($iCurrentPage > $iNumPages) {
                $iCurrentPage = $iNumPages;
            }
        } else {
            $iCurrentPage = 1;
        }
        $start = ($iCurrentPage * $iAlbumsPerPage) - $iAlbumsPerPage;
        echo '<div class="titlebar">
                <div class="float-left">
                    <span class="title">Photo Gallery</span> - Albums
                </div>
                <div class="float-right">'.count($asAlbums) . ' albums</div>
              </div>';
        echo '<div class="clear"></div>';
        for ($i = $start; $i < $start + $iAlbumsPerPage; $i++) {
            if (isset($asAlbums[$i])) {
                echo '<div class="thumb-album shadow">
						<div class="thumb-wrapper">
						    <a href="'.$_SERVER['PHP_SELF'] . '?album='. urlencode($asAlbums[$i]) . '">
                                <img src="'. $asRandomPics[$i] . 
                                    '" width="'. $iThumbWidth . '" />
						   </a>
					    </div>
						<div class="p5"></div>
						<a href="' . $_SERVER['PHP_SELF'] . '?album=' . urlencode($asAlbums[$i]) . '">
						<span class="caption">'. $asCaptions[$i] . '</span>
						</a>
                      </div>';
            }
        }
        echo '<div class="clear"></div>';
        echo '<div align="center" class="paginate-wrapper">';
        $sUrlVars = "";
        printPagination($iNumPages, $sUrlVars, $iCurrentPage);
        echo '</div>';
    }
} else {
    // display photos in album
    $sSrcFolder = $sMainFolder . '/'.$_GET['album'];
    $asSrcFiles  = scandir($sSrcFolder);
    $asFiles = array();
    foreach ($asSrcFiles as $sFile) {
        $sExt = strrchr($sFile, '.');
        if (in_array($sExt, $asExtensions)) {
            array_push($asFiles, $sFile);
            if (!is_dir($sSrcFolder . '/thumbs')) {
                mkdir($sSrcFolder . '/thumbs');
                chmod($sSrcFolder . '/thumbs', 0777);
                //chown($sSrcFolder.'/thumbs', 'apache'); 
            }
            $thumb = $sSrcFolder . '/thumbs/'.$sFile;
            if (!file_exists($thumb)) {
                makeThumb($sSrcFolder, $sFile, $thumb, $iThumbWidth); 
            }        
        }
    }
    if (count($asFiles) == 0) {
        echo 'There are no photos in this album!';
    } else {
        $iNumPages = ceil(count($asFiles) / $itemsPerPage);
        if (isset($_GET['p'])) {
            $iCurrentPage = $_GET['p'];
            if ($iCurrentPage > $iNumPages) {
                $iCurrentPage = $iNumPages;
            }
        } else {
            $iCurrentPage = 1;
        }
        $start = ($iCurrentPage * $itemsPerPage) - $itemsPerPage;
        echo '<div class="titlebar">
           <div class="float-left"><span class="title">' . 
               $_GET['album'] . '</span> - <a href="' . $_SERVER['PHP_SELF'] . 
               '">View All Albums</a></div>
           <div class="float-right">'.count($asFiles) . ' images</div>
        </div>'; 
        echo '<div class="clear"></div>';
        for ($i=$start; $i<$start + $itemsPerPage; $i++) {
            if (isset($asFiles[$i]) && is_file($sSrcFolder . '/'. $asFiles[$i])) { 
                echo '<div class="thumb shadow">
	                <div class="thumb-wrapper">
                    <a href="'. $sSrcFolder . '/'. $asFiles[$i] . 
                        '" class="albumpix" rel="albumpix">
                      <img src="' . $sSrcFolder . '/thumbs/' . $asFiles[$i] .
                      '" width="' . $iThumbWidth . '" alt="" />
				    </a>
					</div>  
			      </div>'; 
            } else {
                if (isset($asFiles[$i])) {
                    echo $asFiles[$i];
                }
            }
        }
        echo '<div class="clear"></div>';
        echo '<div align="center" class="paginate-wrapper">';
        $sUrlVars = "album=".urlencode($_GET['album']) . "&amp;";
        printPagination($iNumPages, $sUrlVars, $iCurrentPage);
        echo '</div>';
    }
}
?>