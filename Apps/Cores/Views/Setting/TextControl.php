<?php

namespace Apps\Cores\Views\Setting;

class TextControl extends Control
{

    public function __toString()
    {
        ob_start();
        $uid = 'control-' . $this->dom->id;
        ?>
        <div class="form-group row">
            <label class="control-label col-xs-4" for="<?php echo $uid ?>"><?php echo $this->dom->label ?>: </label>
            <div class="col-xs-8">
                <textarea name="<?php echo $this->dom->id ?>" id="<?php echo $uid ?>"
                          placeholder="<?php echo $this->dom->placeholder ?>" rows="8"
                          class="form-control"><?php echo $this->dom->value ?></textarea>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handleValue($userInput)
    {
        return (string) $userInput;
    }

}
