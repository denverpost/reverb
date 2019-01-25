<?php

class WpProQuiz_Model_Model
{

    /**
     * @var WpProQuiz_Model_QuizMapper
     */
    protected $_mapper = null;

    public function __construct($array = null)
    {
        $this->setModelData($array);
    }

    public function setModelData($array)
    {
        if ($array != null) {
//          foreach($array as $k => $v) {
//              if(strpos($k, '_') !== false) {
// //                   $k = str_replace(' ', '', ucwords(str_replace('_', ' ', $k)));
//                  $k = implode('', array_map('ucfirst', explode('_', $k)));
//              } else {
//                  $k = ucfirst($k);   
//              }

// //               $this->{'set'.ucfirst($k)}($v);
//              $this->{'set'.$k}($v);
//          }

            //3,4x faster
            $n = explode(' ', implode('', array_map('ucfirst', explode('_', implode(' _', array_keys($array))))));

            $a = array_combine($n, $array);

            foreach ($a as $k => $v){
                $this->{'set' . $k}($v);

                if($k == 'ShareButtonsSettings'){
                    in_array('facebookShare', $v) ? $this->setFacebookShare(1) : "";
                    in_array('twitterShare', $v) ? $this->setTwitterShare(1) : "";
                    in_array('linkedinShare', $v) ? $this->setLinkedinShare(1) : "";
                    in_array('emailShare', $v) ? $this->setEmailShare(1) : "";
                }

                if($k=='StylesheetName'){
                    $check_keyword = '.css';
                    if (strpos($v, $check_keyword) == false) {
                        //if '.css' keyword doesn't found
                        $strQuizStyle=sanitize_title($v).".css";
                    }else{
                        //if '.css' keyword found
                        $strPlainStyleName=str_replace('.css', '', $v);
                        $strQuizStyle=sanitize_title($strPlainStyleName).".css";
                    }
                    $this->setQuizStyle($strQuizStyle);
                }
            }
        }
    }

    public function __call($name, $args)
    {
    }

    /**
     *
     * @return WpProQuiz_Model_QuizMapper
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {
            $this->_mapper = new WpProQuiz_Model_QuizMapper();
        }

        return $this->_mapper;
    }

    /**
     * @param WpProQuiz_Model_QuizMapper $mapper
     * @return WpProQuiz_Model_Model
     */
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;

        return $this;
    }
}
