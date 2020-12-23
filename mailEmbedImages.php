<?php
/**
 * mailEmbedImages : All images in eMail (src/background) will be downloaded and converted to base64.
 * 
 *
 * @author DEric Wagener
 * @copyright 2020 Eric Wagener <http://www.xites.nl>
 * @license MIT
 * @version 1.0.0
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * The MIT License
 */

class mailEmbedImages extends PluginBase {
    static protected $description = 'Embed all images as base64.';
    static protected $name = 'mailEmbedImages';
	private $imageReplacements = [];

    
    public function init() {
        $this->subscribe('beforeEmail','beforeEmail');
        $this->subscribe('beforeSurveyEmail','beforeEmail');
        $this->subscribe('beforeTokenEmail','beforeEmail');
    }

    /**
     * Set From and Bounce of PHPmailer to siteadminemail
     * @link https://manual.limesurvey.org/BeforeTokenEmail
     */
    public function beforeEmail() {
        /*$emailsmtpuser = Yii::app()->getConfig('emailsmtpuser');
        if(empty($emailsmtpuser)) {
            return;
        }*/
        $limeMailer = $this->getEvent()->get("mailer");
		
		preg_match_all('/(?<!-)(src|background)=["\']http(.*)["\']/Ui', $limeMailer->Body, $images);
		if (isset($images[2])) {
			foreach ($images[2] as $imgindex => $url) {
				$url = "http".$url;
				$key = "/".preg_quote($url, "/")."/";
				if (!isset($this->imageReplacements[$key])) {
					preg_match('/(?P<ext>gif|jpe?g|png)/i', $url, $match);
					if (isset($match["ext"])) {
						switch ($match["ext"]) {
							case "jpeg":
							case "jpg":
								$mime = "image/jpeg";
								break;
							default:
								$mime = "image/".$match["ext"];
								break;
						}
						if ($im = file_get_contents($url)) {
							$this->imageReplacements[$key] = "data:{$mime};base64,".base64_encode($im);
						}
					}
				}
			}
			if (count($this->imageReplacements)) {
				$this->getEvent()->set("body", preg_replace(array_keys($this->imageReplacements), $this->imageReplacements, $limeMailer->Body));
			}
		}
		
    }
	

	
}
