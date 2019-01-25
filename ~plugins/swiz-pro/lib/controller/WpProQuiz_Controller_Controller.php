<?php

class WpProQuiz_Controller_Controller
{
    protected $_post = null;
    protected $_cookie = null;

    /**
     * @deprecated
     */
    public function __construct()
    {
        if ($this->_post === null) {
            $this->_post = stripslashes_deep($_POST);
        }

        if ($this->_cookie === null && $_COOKIE !== null) {
            $this->_cookie = stripslashes_deep($_COOKIE);
        }
    }

    /**
     * checkImageInString
     *
     * Check if string contains any image url
     *
     * @param   string
     * @return  bool
     */
    public function checkImageInString($str)
    {
        if (preg_match('('.VALID_IMAGE_EXT.')', $str) === 1){
            return true; // image url is in the string
        }
        return false; // image url is not in the string
    }
}
