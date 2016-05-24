<?php

namespace Apps\Cores\Views\Setting;

class StringControl extends Control
{

    public function __toString()
    {
        ob_start();
        $uid = 'control-' . $this->dom->id;
        ?>
        <div class="form-group row">
            <label class="control-label col-xs-4" for="<?php echo $uid ?>"><?php echo $this->dom->label ?>: </label>
            <div class="col-xs-8">
                <input type="text" name="<?php echo $this->dom->id ?>" id="<?php echo $uid ?>" value="<?php echo $this->dom->value ?>" 
                       placeholder="<?php echo $this->dom->placeholder ?>" class="form-control">
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handleValue($userInput)
    {
        return $userInput;
    }

}
