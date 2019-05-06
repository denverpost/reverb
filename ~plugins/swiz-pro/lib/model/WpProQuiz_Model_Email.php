<?php

class WpProQuiz_Model_Email extends WpProQuiz_Model_Model
{
    protected $_to = '';
    protected $_toUser = false;
    protected $_toForm = false;
    protected $_from = '';
    protected $_subject = '';
    protected $_html = false;
    protected $_message = '';

    public function setTo($_to)
    {
        $this->_to = (string)$_to;

        return $this;
    }

    public function getTo()
    {
        return $this->_to;
    }

    public function setToUser($_toUser)
    {
        $this->_toUser = (bool)$_toUser;

        return $this;
    }

    public function isToUser()
    {
        return $this->_toUser;
    }

    public function setToForm($_toForm)
    {
        $this->_toForm = (bool)$_toForm;

        return $this;
    }

    public function isToForm()
    {
        return $this->_toForm;
    }

    public function setFrom($_from)
    {
        $this->_from = (string)$_from;

        return $this;
    }

    public function getFrom()
    {
        return $this->_from;
    }

    public function setSubject($_subject)
    {
        $this->_subject = (string)$_subject;

        return $this;
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function setHtml($_html)
    {
        $this->_html = (bool)$_html;

        return $this;
    }

    public function isHtml()
    {
        return $this->_html;
    }

    public function setMessage($_message)
    {
        $this->_message = (string)$_message;

        return $this;
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public static function getDefault($adminEmail)
    {
        $email = new WpProQuiz_Model_Email();

        if ($adminEmail) {
            $email->setSubject(__('Wp-Pro-Quiz: One user completed a quiz', 'wp-pro-quiz'));
            $email->setMessage(__('
<div style="text-align: center;" align="center">
    <a href="'.SWIZPRO_LOGO.'" rel="attachment wp-att-157"><img class="alignnone size-large wp-image-157" src="'.SWIZPRO_LOGO.'" alt="" width="131" height="131" />
    </a>
</div>
<hr />
<div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Dear Admin,</div>
&nbsp;
<div style="font-size: 16px; text-align: center; letter-spacing: 1px;">
    There\'s a new submission for <strong>$quizname</strong>
</div>
&nbsp;
<div style="font-size: 16px; text-align: center; letter-spacing: 1px;">
    Please the user and results<strong> </strong>below:
</div>
<div></div>
<table class="aligncenter" style="font-size: 16px; letter-spacing: 1px;" width="200px" align="center">
  <tbody>
    <tr>
      <td>
        <div>
          <div>Points: <span style="font-size: inherit;"><strong><span style="font-family: inherit;">$points</span></strong></span></div>
        </div>
      </td>
    </tr>
    <tr>
      <td>
        <div>
          <div>Score: <span style="font-family: inherit; font-size: inherit;"><strong>$result</strong> </span>
          </div>
        </div>
      </td>
    </tr>
  </tbody>
</table>
<hr />
', 'wp-pro-quiz'));
        } else {
            $email->setSubject(__('Your quiz results from SWIZ Pro', 'wp-pro-quiz'));
            $email->setMessage(__('
<div style="text-align: center;" align="center">
  <a href="'.SWIZPRO_LOGO.'" rel="attachment wp-att-157"><img class="alignnone size-large wp-image-157" src="'.SWIZPRO_LOGO.'" alt="" width="131" height="131" />
  </a>
</div>
<hr />
<div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Hello,</div>
&nbsp;
<div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Thanks for taking the <strong>$quizname</strong>!</div>
&nbsp;
<div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Please see your results<strong> </strong>below:</div>
<div></div>
<table class=" aligncenter" style="font-size: 16px; letter-spacing: 1px;" width="200px" align="center">
  <tbody>
    <tr>
      <td>
        <div>
          <div>Points: <span style="font-size: inherit;"><strong><span style="font-family: inherit;">$points</span></strong></span></div>
        </div>
      </td>
    </tr>
    <tr>
      <td>
        <div>
          <div>Score: <span style="font-family: inherit; font-size: inherit;"><strong>$result</strong> </span>
          </div>
        </div>
      </td>
    </tr>
  </tbody>
</table>
<hr />
&nbsp;
<div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Like quizzes? Visit <a href="www.swizpro.com"><strong>Swiz Pro</strong></a>
</div>
', 'wp-pro-quiz'));
        }

        return $email;
    }

    public static function getDefaultPC($adminEmail)
    {
        $email = new WpProQuiz_Model_Email();

        if ($adminEmail) {
            $email->setSubject(__('WP-SWIZ PRO:One User submitted the entry pc', 'wp-pro-quiz'));
            $email->setMessage(__('
                <div style="text-align: center;" align="center">
                    <a href="'.SWIZPRO_LOGO.'" rel="attachment wp-att-157"><img class="alignnone size-large wp-image-157" src="'.SWIZPRO_LOGO.'" alt="" width="131" height="131" />
                    </a>
                </div>
                <hr />
                <div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Dear Admin,</div>
                &nbsp;
                <div style="font-size: 16px; text-align: center; letter-spacing: 1px;">
                    There\'s a new submission for <strong>$quizname</strong>
                </div>
                &nbsp;
                <div style="font-size: 16px; text-align: center; letter-spacing: 1px;">
                    Please visit the backend to see the user details.
                </div>
                <div></div>
                <hr />
                ', 'wp-pro-quiz')
            );
        } else {
            $email->setSubject(__('Thank you for participating in our contest', 'wp-pro-quiz'));
            $email->setMessage(__('
                <div style="text-align: center;" align="center">
                  <a href="'.SWIZPRO_LOGO.'" rel="attachment wp-att-157"><img class="alignnone size-large wp-image-157" src="'.SWIZPRO_LOGO.'" alt="" width="131" height="131" />
                  </a>
                </div>
                <hr />
                <div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Hello,</div>
                &nbsp;
                <div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Thanks for submitting your entry to <strong>$quizname</strong>!</div>
                &nbsp;
                <hr />
                &nbsp;
                <div style="font-size: 16px; text-align: center; letter-spacing: 1px;">Like quizzes? Visit <a href="www.swizpro.com"><strong>Swiz Pro</strong></a>
                </div>
                ', 'wp-pro-quiz')
            );
        }

        return $email;
    }
}
