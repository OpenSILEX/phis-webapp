<?php
//******************************************************************************
//                         FullScreenImageSliderWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 03 Oct, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * A widget used to generate a simple customizable background image slider.
 * To get more animation, go to see the link below
 * @see https://tympanus.net/Tutorials/CSS3FullscreenSlideshow/index.html
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class FullScreenImageSliderWidget extends Widget {

    const DURATION_PER_IMAGE = "durationPerImage";
    const IMAGES_URL_LINK = "imagesUrlsLinks";

    /**
     * the number of second between two images.
     * @var int 
     */
    public $durationPerImage = 6;

    /**
     * The images links need to be showed.
     * The path start from web directory but it's a relative path.
     * @example ["background/wallpaper_green_house.jpg"]
     * @var array
     */
    public $imagesUrlsLinks = [];

    public function init() {
        parent::init();
        // imagesUrlsLinks be not null and greater than 2 
        if ($this->imagesUrlsLinks === null && count($this->imagesUrlsLinks) > 2) {
            throw new \Exception("Images aren't set");
        }
        // durationPerImage need to be an integer and greater than 1
        if (!is_int($this->durationPerImage) && $this->durationPerImage > 1) {
            throw new \Exception("Wrong duration per image");
        }
    }

    /**
     * Render the annotation button
     * @return string the string rendered
     */
    public function run() {
        // set slider animation configuration
        $this->getView()->registerCssFile('@web/css/full-slider.css');
        // create html tempalte
        $html = Html::ul($this->imagesUrlsLinks, 
                [
                'item' => function($item, $index) {
                            return Html::tag('li', 
                                    Html::tag('span', "Image " . $index));
                        },
                'class' => 'cb-slideshow'
                ]
            );
        // generate right css information
        $css = "";            
        foreach ($this->imagesUrlsLinks as $index => $item) {
            $css .= $this->addImageToCss($item, $index);
        }
        $css .= $this->getWholeCssDuration();
        // register css
        $this->getView()->registerCss($css);

        return $html;
    }
    /**
     * Generate css to render the behaviour per image
     * @param string $item the relative path of the image
     * @param string $index the index of the image in the array
     * @return string the rendered css
     */
    private function addImageToCss($item, $index) {
        if ($index == 0) {
            return ".cb-slideshow li:nth-child(1) span { background-image: url(" . $item . ") }" . PHP_EOL;
        } else {
        $duration = $index * $this->durationPerImage;
        $imageNumber = ( $index + 1 );
        return ".cb-slideshow li:nth-child(" . $imageNumber . ") span {
                        background-image: url(" . $item . ");
                        -webkit-animation-delay: " . $duration . "s;
                        -moz-animation-delay: " . $duration . "s;
                        -o-animation-delay: " . $duration . "s;
                        -ms-animation-delay: " . $duration . "s;
                        animation-delay: " . $duration . "s;
                    }" 
                . PHP_EOL
                .".cb-slideshow li:nth-child(" . $imageNumber . ") div {
                        -webkit-animation-delay: " . $duration . "s;
                        -moz-animation-delay: " . $duration . "s;
                        -o-animation-delay: " . $duration . "s;
                        -ms-animation-delay: " . $duration . "s;
                        animation-delay: " . $duration . "s;
                    }" 
                . PHP_EOL;
        }
    }

    /**
     * Generate css which define the global behaviour of the slider
     * @return string the rendered css
     */
    private function getWholeCssDuration() {
        $numberOfImages = count($this->imagesUrlsLinks);
        $totalDuration = $numberOfImages * $this->durationPerImage;
        return ".cb-slideshow li span {
                    width: 100%;
                    height: 100%;
                    position: absolute;
                    top: 0px;
                    left: 0px;
                    color: transparent;
                    background-size: cover;
                    background-position: 50% 50%;
                    background-repeat: none;
                    opacity: 0;
                    z-index: 0;
                    -webkit-backface-visibility: hidden;
                    -webkit-animation: imageAnimation " . $totalDuration . "s linear infinite ;
                    -moz-animation: imageAnimation " . $totalDuration . "s linear infinite ;
                    -o-animation: imageAnimation " . $totalDuration . "s linear infinite ;
                    -ms-animation: imageAnimation " . $totalDuration . "s linear infinite ;
                    animation: imageAnimation " . $totalDuration . "s linear infinite ;
                }
                .cb-slideshow li div {
                    z-index: 1000;
                    position: absolute;
                    bottom: 30px;
                    left: 0px;
                    width: 100%;
                    text-align: center;
                    opacity: 0;
                    -webkit-animation: titleAnimation " . $totalDuration . "s linear infinite 0s;
                    -moz-animation: titleAnimation " . $totalDuration . "s linear infinite 0s;
                    -o-animation: titleAnimation " . $totalDuration . "s linear infinite 0s;
                    -ms-animation: titleAnimation " . $totalDuration . "s linear infinite 0s;
                    animation: titleAnimation " . $totalDuration . "s linear infinite 0s;
                }";
    }
}
