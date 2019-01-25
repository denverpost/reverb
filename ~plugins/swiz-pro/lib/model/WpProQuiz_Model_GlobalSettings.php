<?php

class WpProQuiz_Model_GlobalSettings extends WpProQuiz_Model_Model
{

    protected $_addRawShortcode = false;
    protected $_jsLoadInHead = false;
    protected $_touchLibraryDeactivate = false;
    protected $_corsActivated = false;

    protected $_facebookAppId = "";
    protected $_thumbnailWidth= "";
    protected $_thumbnailHeight = "";
    protected $_privacyPolicy = '';
    protected $_termsConditions = '';
    protected $_contestRules = '';

    public function setAddRawShortcode($_addRawShortcode)
    {
        $this->_addRawShortcode = (bool)$_addRawShortcode;

        return $this;
    }

    public function isAddRawShortcode()
    {
        return $this->_addRawShortcode;
    }

    public function setJsLoadInHead($_jsLoadInHead)
    {
        $this->_jsLoadInHead = (bool)$_jsLoadInHead;

        return $this;
    }

    public function isJsLoadInHead()
    {
        return $this->_jsLoadInHead;
    }

    public function setTouchLibraryDeactivate($_touchLibraryDeactivate)
    {
        $this->_touchLibraryDeactivate = (bool)$_touchLibraryDeactivate;

        return $this;
    }

    public function isTouchLibraryDeactivate()
    {
        return $this->_touchLibraryDeactivate;
    }

    public function setCorsActivated($_corsActivated)
    {
        $this->_corsActivated = (bool)$_corsActivated;

        return $this;
    }

    public function isCorsActivated()
    {
        return $this->_corsActivated;
    }

    /**
     * Sets the facebook id for plugin
     * @param string $_facebookAppId
     * @return WpProQuiz_Model_GlobalSettings
     */
    public function setFacebookAppId($_facebookAppId)
    {
        $_facebookAppId = !empty($_facebookAppId) ? $_facebookAppId : FACEBOOK_APP_ID;
        $this->_facebookAppId = (string)$_facebookAppId;

        return $this;
    }

    /**
     * Gets the facebook id for plugin
     * @return string
     */
    public function getFacebookAppId()
    {
        return $this->_facebookAppId;
    }

    /**
     * Sets the thumbnail width size
     * @param int $_thumbnailWidth
     * @return WpProQuiz_Model_GlobalSettings
     */
    public function setThumbnailWidth($_thumbnailWidth)
    {
        $_thumbnailWidth = !empty($_thumbnailWidth) ? $_thumbnailWidth : DEFAULT_CUSTOM_THUMBNAIL_WIDTH;
        $this->_thumbnailWidth = (string)$_thumbnailWidth;

        return $this;
    }

    /**
     * Gets the thumbnail width
     * @return int
     */
    public function getThumbnailWidth()
    {
        return $this->_thumbnailWidth;
    }

    /**
     * Sets the thumbnail height size
     * @param int $_thumbnailHeight
     * @return WpProQuiz_Model_GlobalSettings
     */
    public function setThumbnailHeight($_thumbnailHeight)
    {
        $_thumbnailHeight = !empty($_thumbnailHeight) ? $_thumbnailHeight : DEFAULT_CUSTOM_THUMBNAIL_HEIGHT;
        $this->_thumbnailHeight = (string)$_thumbnailHeight;

        return $this;
    }

    /**
     * Gets the thumbnail height
     * @return int
     */
    public function getThumbnailHeight()
    {
        return $this->_thumbnailHeight;
    }

    /**
     * Sets the privacy policy content.
     * @param string $_privacyPolicy
     * @return WpProQuiz_Model_GlobalSettings
     */
    public function setPrivacyPolicy($_privacyPolicy)
    {
        $this->_privacyPolicy = (string)$_privacyPolicy;

        return $this;
    }

    /**
     * Gets the privacy policy content.
     *
     * @return string privacy policy content
     */
    public function getPrivacyPolicy()
    {
        return $this->_privacyPolicy;
    }

    /**
     * Sets the terms conditions content.
     * @param string $_termsConditions
     * @return WpProQuiz_Model_GlobalSettings
     */
    public function setTermsConditions($_termsConditions)
    {
        $this->_termsConditions = (string)$_termsConditions;

        return $this;
    }

    /**
     * Gets the terms conditions content.
     *
     * @return string terms conditions content
     */
    public function getTermsConditions()
    {
        return $this->_termsConditions;
    }

    /**
     * Sets the contest rules content.
     * @param string $_contestRules
     * @return WpProQuiz_Model_GlobalSettings
     */
    public function setContestRules($_contestRules)
    {
        $this->_contestRules = (string)$_contestRules;

        return $this;
    }

    /**
     * Gets the contest rules content.
     *
     * @return string contest rules content
     */
    public function getContestRules()
    {
        return $this->_contestRules;
    }
}
