<?php

namespace Apps\Cores\Views\Setting;

class BooleanControl extends Control
{

    public function __toString()
    {
        ob_start();
        $checked = $this->dom->value == 'true' ? 'checked' : '';
        $id = 'control-' . $this->dom->id;
        ?>
        <div class="form-group row">
            <label class="control-label col-xs-4" for="<?php echo $id ?>"><?php echo $this->dom->label ?>:</label>
            <div class="col-xs-8">
                <label for="<?php echo $id ?>">
                    <div class="check">
                        <input type="checkbox" <?php echo $checked ?>  id="<?php echo $id ?>" name="<?php echo $this->dom->id ?>" value="true"/>
                        <before></before>
                        <after></after>
                    </div>
                </label>
            </div>
        </div>

        <?php
        return ob_get_clean();
    }

    public function handleValue($userInput)
    {
        return $userInput === true || $userInput === 'true' ? 'true' : 'false';
    }

}
