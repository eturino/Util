<?php


class EtuDev_Util_Gravatar
{

    /**
     * @param null $mail
     *
     * @return EtuDev_Util_Gravatar
     */
    static public function newPicture($mail = null)
    {
        $n = new EtuDev_Util_Gravatar();

        $n->mail = $mail ? : null;
        return $n;
    }

    /**
     * @var string
     */
    protected $url;

    /**
     * @var bool
     */
    protected $use_ssl = true;

    /**
     * @var string
     */
    protected $mail;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var int
     */
    protected $size = 80;

    /**
     * @var string
     */
    protected $default_image = 'mm';

    /**
     * @var string
     */
    protected $rating;


    /**
     * @param $mail
     *
     * @return $this
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
        $this->resetHash();
        $this->cleanUrl();
        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        if (!$this->hash) {
            $this->resetHash();
        }
        return $this->hash;
    }

    /**
     * @return $this
     */
    protected function resetHash()
    {
        $this->hash = $this->mail ? md5(strtolower(trim($this->mail))) : null;
        return $this;
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (!$this->url) {
            $this->createUrl();
        }
        return $this->url;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl();
    }

    /**
     * @return $this
     */
    protected function cleanUrl()
    {
        $this->url = null;
        return $this;
    }

    /**
     * @return $this
     */
    protected function createUrl()
    {
        $h = $this->getHash();
        if (!$h) {
            $this->url = null;
            return $this;
        }

        $url = $this->use_ssl ? 'https://secure.gravatar.com/avatar/' : 'http://www.gravatar.com/avatar/';
        $url .= $h;

        $p = array();
        if ($this->rating) {
            $p['r'] = $this->rating;
        }

        if ($this->size && $this->size != 80) {
            $p['s'] = $this->size;
        }

        if ($this->default_image) {
            $p['d'] = $this->default_image;
        }

        $url = EtuDev_Util_URL::build_path_url($url, $p);

        $this->url = $url;
        return $this;
    }

    /**
     * @param string $default_image
     *
     * @return EtuDev_Util_Gravatar
     */
    public function setDefaultImage($default_image = 'mm')
    {
        $this->default_image = $default_image;
        $this->cleanUrl();
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultImage()
    {
        return $this->default_image;
    }

    /**
     * @param string $rating
     *
     * @return EtuDev_Util_Gravatar
     */
    public function setRating($rating = null)
    {
        $this->rating = in_array($rating, array(self::RATING_G, self::RATING_PG, self::RATING_R, self::RATING_X,)) ? $rating : null;
        $this->cleanUrl();
        return $this;
    }

    /**
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $size
     *
     * @return EtuDev_Util_Gravatar
     */
    public function setSize($size = 80)
    {
        $size = (int) $size;

        $this->size = ($size > 0 && $size < 2048) ? $size : null;
        $this->cleanUrl();
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param boolean $use_ssl
     *
     * @return EtuDev_Util_Gravatar
     */
    public function setUseSsl($use_ssl = true)
    {
        $this->use_ssl = (bool) $use_ssl;
        $this->cleanUrl();
        return $this;
    }

    /**
     * @return boolean
     */
    public function getUseSsl()
    {
        return $this->use_ssl;
    }

    /**
     * @return bool
     * alias of getUseSsl()
     */
    public function isUseSSL()
    {
        return $this->use_ssl;
    }


    /**
     * suitable for display on all websites with any audience type
     */
    const RATING_G = 'g';

    /**
     * may contain rude gestures, provocatively dressed individuals, the lesser swear words, or mild violence
     */
    const RATING_PG = 'pg';

    /**
     * may contain such things as harsh profanity, intense violence, nudity, or hard drug use
     */
    const RATING_R = 'r';

    /**
     * may contain hardcore sexual imagery or extremely disturbing violence
     */
    const RATING_X = 'r';

    /**
     * a simple, cartoon-style silhouetted outline of a person (does not vary by email hash)
     */
    const IMAGE_MISTERYMAN = 'mm';

    /**
     * do not load any image if none is associated with the email hash, instead return an HTTP 404 (File Not Found) response
     */
    const IMAGE_404 = '404';

    /**
     * a geometric pattern based on an email hash
     */
    const IMAGE_IDENTICON = 'identicon';

    /**
     * a generated 'monster' with different colors, faces, etc
     */
    const IMAGE_MONSTERID = 'monsterid';

    /**
     * generated faces with differing features and backgrounds
     */
    const IMAGE_WAVATAR = 'wavatar';

    /**
     * awesome generated, 8-bit arcade-style pixelated faces
     */
    const IMAGE_RETRO = 'retro';


    /**
     * a transparent PNG image (border added to HTML below for demonstration purposes)
     */
    const IMAGE_BLANK = 'blank';


}
