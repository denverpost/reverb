<?php

class WpProQuiz_View_GlobalHelperTabs
{


    public function getHelperSidebar()
    {
        ob_start();

        $this->showHelperSidebar();

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    public function getHelperTab()
    {
        ob_start();

        //Commented below code beacause some unnecessary iframe code is genererated
        /* $this->showHelperTabContent(); */

        $content = ob_get_contents();

        ob_end_clean();

        return array(
            'id' => 'wp_pro_quiz_help_tab_1',
            'title' => __('SWIZ Pro', 'wp-pro-quiz'),
            'content' => $content,
        );
    }

    private function showHelperTabContent()
    {
        ?>

        <h2>SWIZ Pro</h2>

        <h4>Wp-Pro-Quiz on Github</h4>

        <iframe src="https://ghbtns.com/github-btn.html?user=xeno010&repo=Wp-Pro-Quiz&type=star&count=true"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
        <iframe src="https://ghbtns.com/github-btn.html?user=xeno010&repo=Wp-Pro-Quiz&type=watch&count=true&v=2"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
        <iframe src="https://ghbtns.com/github-btn.html?user=xeno010&repo=Wp-Pro-Quiz&type=fork&count=true"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>

        <?php
    }

    private function showHelperSidebar()
    {
        ?>

        <p>
            <strong><?php _e('For more information:'); ?></strong>
        </p>
        <p>
            <a href="mailto:support@highervisual.com"><?php _e('Support', 'wp-pro-quiz'); ?></a>
        </p>

        <p>
            <a target="_blank"
               href="https://www.highervisual.com"><?php _e('Higher Visual','wp-pro-quiz'); ?></a>
        </p>


        <?php
    }
}
