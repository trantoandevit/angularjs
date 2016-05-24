<?php

namespace Apps\Cores\Views\Setting;

class SelectControl extends Control
{

    public function __toString()
    {
        ob_start();
        $id = 'control-' . $this->dom->id;
        ?>

        <div class="form-group row">
            <label class="control-label col-xs-4" for="<?php echo $id ?>"><?php echo $this->dom->label ?>:</label>
            <div class="col-xs-8">
                <select id="<?php echo $id ?>" name="<?php echo $this->dom->id ?>" class="form-control">
                    <option>-- Không chọn --</option>
                    <?php
                    foreach ($this->dom->options->option as $option)
                    {
                        $selected = (string) $option->attributes()->value == (string) $this->dom->value ? 'selected' : '';
                        echo "<option value='" . $option->attributes()->value . "' $selected>$option</option>";
                    }
                    ?>
                </select>
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
