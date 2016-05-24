<style>
    .modal-department-picker .fa{font-size: 1.3em;color: gray;width: 15px;text-align: center;}
    .modal-department-picker .fa-folder, .modal-department-picker .fa-folder-open{color: #FBC02D;margin-right: 5px;}
    .modal-department-picker ul{list-style-type: none; }
    .modal-department-picker ul li{min-height: 30px;line-height: 30px; cursor: pointer;clear: both;}
    .modal-department-picker ul li.selected{background: #337AB7;}
    .modal-department-picker ul li.selected ul, .modal-department-picker ul li.selected li{background: white;}
    .modal-department-picker ul li.selected > .fa-caret-down,
    .modal-department-picker ul li.selected > .fa-caret-right,
    .modal-department-picker ul li.selected > span{color: white;}
    .modal-department-picker .modal-body > ul{padding: 0;}
    .modal-department-picker li.hover{background: #eee;}
    .modal-department-picker ul{padding-left: 15px;}
    .modal-department-picker ul > li > ul{max-height: 0;overflow: hidden;}
    .modal-department-picker ul > li.expand > ul{max-height: none;}
    .modal-department-picker .modal-dialog{width: 400px;}
    .modal-department-picker .modal-body{height: 300px;overflow-y: auto;}
</style>

<?php

function ulTemplate($level, $maxDepth = 10)
{
    $parent = 'lvl' . ($level - 1);
    $class = 'lvl' . $level;
    ?>
    <ul class="<?php echo $class ?>" ng-if="<?php echo $parent ?>.deps.length">
        <li ng-repeat="<?php echo "{$class} in {$parent}.deps" ?>" 
            ng-click="setSelected(<?php echo $class ?>, $event)" ng-dblclick="submit()"
            ng-class="<?php echo "{expand: $class.expand, selected: $class.pk==selected.pk}" ?>">
            <i class='fa' ng-if="!<?php echo $class ?>.deps.length"></i>
            <i class='fa' ng-if="<?php echo $class ?>.deps.length" ng-click="toggleExpand(<?php echo $class ?>)"
               ng-class="<?php echo "{'fa-caret-right': !$class.expand, 'fa-caret-down': $class.expand}" ?>"></i>
            <span>
                <i class="fa" ng-class="<?php echo "{'fa-folder': !$class.expand, 'fa-folder-open': $class.expand}" ?>"></i>
                {{<?php echo $class ?>.depName}}
            </span>
            <?php
            if ($level < $maxDepth)
            {
                ulTemplate($level + 1);
            }
            ?>
        </li>
    </ul>
    <?php
}
?>

<!-- Modal -->
<div class="modal fade modal-department-picker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Chọn đơn vị/phòng ban</h4>
            </div>
            <div class="modal-body">
                <ul>
                    <li class="expand" ng-click="setSelected(root, $event)" ng-dblclick="submit()" ng-class="<?php echo "{selected: root.pk==selected.pk}" ?>">
                        <i class="fa fa-caret-down"></i>&nbsp;<span>{{root.depName}}</span>
                    </li>
                    <ul>
                        <li ng-repeat='lvl1 in tree.deps' ng-class="<?php echo "{expand: lvl1.expand, selected: lvl1.pk==selected.pk}" ?>" 
                            ng-click="setSelected(lvl1, $event)" ng-dblclick="submit()">
                            <i class='fa' ng-if="!lvl1.deps.length"></i>
                            <i class='fa' ng-if="lvl1.deps.length" ng-click="toggleExpand(lvl1)"
                               ng-class="<?php echo "{'fa-caret-right': !lvl1.expand, 'fa-caret-down': lvl1.expand}" ?>"></i>
                            <span>
                                <i class="fa" ng-class="<?php echo "{'fa-folder': !lvl1.expand, 'fa-folder-open': lvl1.expand}" ?>"></i> {{lvl1.depName}}
                            </span>
                            <?php ulTemplate(2) ?>
                        </li>
                    </ul>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy bỏ</button>
                <button type="button" class="btn btn-primary" ng-disabled="!selected" ng-click="submit()">Chọn</button>
            </div>
        </div>
    </div>
</div>
