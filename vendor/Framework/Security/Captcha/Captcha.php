<?php

namespace Framework\Security\Captcha;

class Captcha
{

    const REFRESH_GET_VAR = 'refresh';
    /**
     * @var integer how many times should the same CAPTCHA be displayed. Defaults to 3.
     * A value less than or equal to 0 means the test is unlimited (available since version 1.1.2).
     */
    public $testLimit = 3;
    /**
     * @var integer the width of the generated CAPTCHA image. Defaults to 120.
     */
    public $width = 120;
    /**
     * @var integer the height of the generated CAPTCHA image. Defaults to 50.
     */
    public $height = 50;

    public $fontSize = 35;
    /**
     * @var integer padding around the text. Defaults to 2.
     */
    public $padding = 2;
    /**
     * @var integer the background color. For example, 0x55FF00.
     * Defaults to 0xFFFFFF, meaning white color.
     */
    public $backColor = 0xFFFFFF;
    /**
     * @var integer the font color. For example, 0x55FF00. Defaults to 0x2040A0 (blue color).
     */
    public $foreColor = 0x2040A0;
    /**
     * @var boolean whether to use transparent background. Defaults to false.
     */
    public $transparent = false;
    /**
     * @var integer the minimum length for randomly generated word. Defaults to 6.
     */
    public $minLength = 6;
    /**
     * @var integer the maximum length for randomly generated word. Defaults to 7.
     */
    public $maxLength = 7;
    /**
     * @var integer the offset between characters. Defaults to -2. You can adjust this property
     * in order to decrease or increase the readability of the captcha.
     **/
    public $offset = -2;
    /**
     * @var string the TrueType font file. This can be either a file path or path alias.
     */
    public $fontFile = 'SpicyRice.ttf';
    /**
     * @var string the fixed verification code. When this property is set,
     * [[getVerifyCode()]] will always return the value of this property.
     * This is mainly used in automated tests where we want to be able to reproduce
     * the same verification code each time we run the tests.
     * If not set, it means the verification code will be randomly generated.
     */

    /**
     * Number of noise dots on image
     * Used twice - before and after transform
     *
     * @var int
     */
    protected $dotNoiseLevel = 100;

    /**
     * Number of noise lines on image
     * Used twice - before and after transform
     *
     * @var int
     */
    protected $lineNoiseLevel = 5;

    public $fixedVerifyCode;

    public function __construct()
    {
    	$this->width = Config::get('captcha.width');
    	$this->height = Config::get('captcha.height');
    	$this->fontSize = Config::get('captcha.fontSize');
    	$this->fontFile = Config::get('captcha.fontFile');
    	//$this->minLength = Config::get('captcha.minLength');
    	//$this->maxLength = Config::get('captcha.maxLength');
    }

    public function renderImageByGD($code)
    {
        $image = imagecreatetruecolor($this->width, $this->height);

        $backColor = imagecolorallocate($image,
            (int)($this->backColor % 0x1000000 / 0x10000),
            (int)($this->backColor % 0x10000 / 0x100),
            $this->backColor % 0x100);
        imagefilledrectangle($image, 0, 0, $this->width, $this->height, $backColor);
        imagecolordeallocate($image, $backColor);

        if ($this->transparent) {
            imagecolortransparent($image, $backColor);
        }

        $foreColor = imagecolorallocate($image,
            (int)($this->foreColor % 0x1000000 / 0x10000),
            (int)($this->foreColor % 0x10000 / 0x100),
            $this->foreColor % 0x100);

        $length = strlen($code);
        $box = imagettfbbox(60, 0, $this->fontFile, $code);
        $w = $box[4] - $box[0] + $this->offset * ($length - 1);
        $h = $box[1] - $box[5];
        $scale = min(($this->width - $this->padding * 2) / $w, ($this->height - $this->padding * 2) / $h);
        $x = 10;
        $y = round($this->height * 27 / 40);
        for ($i = 0; $i < $length; ++$i) {
            $fontSize = (int)(rand(26, 32) * $scale * 0.8);
            $angle = rand(-10, 10);
            $letter = $code[$i];
            $box = imagettftext($image, $fontSize, $angle, $x, $y, $foreColor, $this->fontFile, $letter);
            $x = $box[2] + $this->offset;
        }

        imagecolordeallocate($image, $foreColor);

        ob_start();
        imagepng($image);
        imagedestroy($image);
        return ob_get_clean();
    }


    protected function randomPhase()
    {
        // random phase from 0 to pi
        return mt_rand(541592, 3141592) / 1000000;
    }

    /**
     * Generate random character size
     *
     * @return int
     */
    protected function randomSize()
    {
        return mt_rand(300, 700) / 100;
    }


    protected function randomFreq()
    {
        return mt_rand(1000000, 1000000) / 13000000;
    }

    public function wave()
    {
        $w     = $this->width;
        $h     = $this->height;
        //$fsize = 30;

        $img = imagecreatetruecolor($w, $h);

        $img2     = imagecreatetruecolor($w, $h);
        $bgColor = imagecolorallocate($img2, 255, 255, 255);
        imagefilledrectangle($img2, 0, 0, $w-1, $h-1, $bgColor);

        // apply wave transforms
        $freq1 = $this->randomFreq();
        $freq2 = $this->randomFreq();
        $freq3 = $this->randomFreq();
        $freq4 = $this->randomFreq();

        $ph1 = $this->randomPhase();
        $ph2 = $this->randomPhase();
        $ph3 = $this->randomPhase();
        $ph4 = $this->randomPhase();

        $szx = $this->randomSize();
        $szy = $this->randomSize();

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $sx = $x + (sin($x*$freq1 + $ph1) + sin($y*$freq3 + $ph3)) * $szx;
                $sy = $y + (sin($x*$freq2 + $ph2) + sin($y*$freq4 + $ph4)) * $szy;

                if ($sx < 0 || $sy < 0 || $sx >= $w - 1 || $sy >= $h - 1) {
                    continue;
                } else {
                    $color   = (imagecolorat($img, $sx, $sy) >> 16)         & 0xFF;
                    $colorX  = (imagecolorat($img, $sx + 1, $sy) >> 16)     & 0xFF;
                    $colorY  = (imagecolorat($img, $sx, $sy + 1) >> 16)     & 0xFF;
                    $colorXY = (imagecolorat($img, $sx + 1, $sy + 1) >> 16) & 0xFF;
                }

                if ($color == 255 && $colorX == 255 && $colorY == 255 && $colorXY == 255) {
                    // ignore background
                    continue;
                } elseif ($color == 0 && $colorX == 0 && $colorY == 0 && $colorXY == 0) {
                    // transfer inside of the image as-is
                    $newcolor = 0;
                } else {
                    // do antialiasing for border items
                    $fracX  = $sx - floor($sx);
                    $fracY  = $sy - floor($sy);
                    $fracX1 = 1 - $fracX;
                    $fracY1 = 1 - $fracY;

                    $newcolor = $color   * $fracX1 * $fracY1
                        + $colorX  * $fracX  * $fracY1
                        + $colorY  * $fracX1 * $fracY
                        + $colorXY * $fracX  * $fracY;
                }

                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newcolor, $newcolor, $newcolor));
            }
        }
    }


    public function generateImage($word)
    {
        $font = $this->fontFile;

        if (empty($font)) {
           // throw new Exception\NoFontProvidedException('Image CAPTCHA requires font');
        }

        $w     = $this->width;
        $h     = $this->height;
        $fsize = $this->fontSize;

        //$imgFile   = $this->getImgDir() . $id . $this->getSuffix();

        if (empty($this->startImage)) {
            $img = imagecreatetruecolor($w, $h);
        } else {
            // Potential error is change to exception
            //ErrorHandler::start();
            $img   = imagecreatefrompng($this->startImage);
           // $error = ErrorHandler::stop();
            /* if (!$img || $error) {
               throw new Exception\ImageNotLoadableException(
                    "Can not load start image '{$this->startImage}'", 0, $error
                );
            }*/
            $w = imagesx($img);
            $h = imagesy($img);
        }

        $textColor = imagecolorallocate($img, 0, 0, 0);
        $bgColor   = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w-1, $h-1, $bgColor);
        $textbox = imageftbbox($fsize, 0, $font, $word);
        $x = ($w - ($textbox[2] - $textbox[0])) / 2;
        $y = ($h - ($textbox[7] - $textbox[1])) / 2;
        imagefttext($img, $fsize, 0, $x, $y, $textColor, $font, $word);

        /*
        // generate noise
        for ($i=0; $i < $this->dotNoiseLevel; $i++) {
            imagefilledellipse($img, mt_rand(0, $w), mt_rand(0, $h), 2, 2, $textColor);
        }
        for ($i=0; $i < $this->lineNoiseLevel; $i++) {
            imageline($img, mt_rand(0, $w), mt_rand(0, $h), mt_rand(0, $w), mt_rand(0, $h), $textColor);
        }
        */

        // transformed image
        $img2     = imagecreatetruecolor($w, $h);
        $bgColor = imagecolorallocate($img2, 255, 255, 255);
        imagefilledrectangle($img2, 0, 0, $w-1, $h-1, $bgColor);

        // apply wave transforms
        $freq1 = $this->randomFreq();
        $freq2 = $this->randomFreq();
        $freq3 = $this->randomFreq();
        $freq4 = $this->randomFreq();

        $ph1 = $this->randomPhase();
        $ph2 = $this->randomPhase();
        $ph3 = $this->randomPhase();
        $ph4 = $this->randomPhase();

        $szx = $this->randomSize();
        $szy = $this->randomSize();

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $sx = $x + (sin($x*$freq1 + $ph1) + sin($y*$freq3 + $ph3)) * $szx;
                $sy = $y + (sin($x*$freq2 + $ph2) + sin($y*$freq4 + $ph4)) * $szy;

                if ($sx < 0 || $sy < 0 || $sx >= $w - 1 || $sy >= $h - 1) {
                    continue;
                } else {
                    $color   = (imagecolorat($img, $sx, $sy) >> 16)         & 0xFF;
                    $colorX  = (imagecolorat($img, $sx + 1, $sy) >> 16)     & 0xFF;
                    $colorY  = (imagecolorat($img, $sx, $sy + 1) >> 16)     & 0xFF;
                    $colorXY = (imagecolorat($img, $sx + 1, $sy + 1) >> 16) & 0xFF;
                }

                if ($color == 255 && $colorX == 255 && $colorY == 255 && $colorXY == 255) {
                    // ignore background
                    continue;
                } elseif ($color == 0 && $colorX == 0 && $colorY == 0 && $colorXY == 0) {
                    // transfer inside of the image as-is
                    $newcolor = 0;
                } else {
                    // do antialiasing for border items
                    $fracX  = $sx - floor($sx);
                    $fracY  = $sy - floor($sy);
                    $fracX1 = 1 - $fracX;
                    $fracY1 = 1 - $fracY;

                    $newcolor = $color   * $fracX1 * $fracY1
                        + $colorX  * $fracX  * $fracY1
                        + $colorY  * $fracX1 * $fracY
                        + $colorXY * $fracX  * $fracY;
                }
                //var_dump(imagecolorallocate($img2, $newcolor, $newcolor, $newcolor));echo'<br>';
                $newcolor = $this->applyForeColor($newcolor, $newcolor, $newcolor);
                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newcolor[0], $newcolor[1], $newcolor[2]));
            }
        }

        /*
        // generate noise
        for ($i=0; $i<$this->dotNoiseLevel; $i++) {
            imagefilledellipse($img2, mt_rand(0, $w), mt_rand(0, $h), 2, 2, $textColor);
        }

        for ($i=0; $i<$this->lineNoiseLevel; $i++) {
            imageline($img2, mt_rand(0, $w), mt_rand(0, $h), mt_rand(0, $w), mt_rand(0, $h), $textColor);
        }
        */

        ob_start();
        imagepng($img2);
        imagedestroy($img);
        imagedestroy($img2);
        return ob_get_clean();

    }


    public function applyForeColor($r, $g, $b)
    {
        //205 39 13
        //20 160 37
        //16 71 187
        $nr = 205;
        $ng = 39;
        $nb = 13;

        $orp = $r/255;
        $ogp = $g/255;
        $obp = $b/255;

        $nrp = $orp * (255-$nr);
        $ngp = $ogp * (255-$ng);
        $nbp = $obp * (255-$nb);

        $nr += $nrp;
        $ng += $ngp;
        $nb += $nbp;

        return [$nr, $ng, $nb];
    }


    public static function generateCode()
    {
    	return strtolower(str_random(rand(Config::get('captcha.minLength'),Config::get('captcha.maxLength'))));
    }

    public static function getCode()
	{
		if (Session::has('_captcha'))
		{
			return Session::get('_captcha');
		}
		else
		{
			$code = static::generateCode();
			Session::set('_captcha', $code);
			//echo Session::get('_captcha');
			//echo Session::get('_captcha');exit;
			return $code;
		}
    }
    //usage
  //echo 0xfafffa >> 0;
//exit;
//header("Content-Type: image/png");
//echo (new captcha())->generateImage('sdsd');
}