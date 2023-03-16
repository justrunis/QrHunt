<?php
// Define namespace
namespace mod_qrhunt\output;

// Include the necessary files
use plugin_renderer_base;

// Define your renderer class
class renderer extends plugin_renderer_base {

    // Define a method to render the completion status
    public function render_completion_status($completionstatus) {

        $completionbutton = $this->output->render($completionbutton);
        $completionbutton = str_replace('<button', '<button class="disabled-button"', $completionbutton);

        return '<div class="completion-status">' . $completionstatus . '</div>';

    }

}
