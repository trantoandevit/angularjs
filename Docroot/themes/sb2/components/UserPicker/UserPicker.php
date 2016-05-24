<style>
    .modal-user-picker .modal-body{padding: 0;}
    .modal-user-picker .tab-pane{padding: 15px;}
    .modal-user-picker .fa-folder-open{font-size: 1.5em; color: #FBC02D;}
    .modal-user-picker .breadcrumb{padding-left: 5px;padding-right: 5px;}
    .modal-user-picker .fa-users{color: gray;font-size: 1.5em;}
</style>

<!-- Modal -->
<div class="modal fade modal-user-picker" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Chọn người sử dụng</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation" ng-class="<?php echo '{active: tab == 0}' ?>" ng-click="setTab(0)"><a href="javascript:;">Tìm theo đơn vị</a></li>
                    <li role="presentation" ng-class="<?php echo '{active: tab == 1}' ?>"  ng-click="setTab(1)"><a href="javascript:;">Tìm theo nhóm</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane" ng-class="<?php echo '{active: tab == 0}' ?>" >
                        <ol class='breadcrumb'>
                            <li>
                                <div class="btn-group">
                                    <a href='javascript:;' class='btn btn-default btn-xs' ng-disabled="currentDep == 0 || ancestors.length == 0"
                                       ng-click="setDep(depInstance.ancestors[depInstance.ancestors.length - 1].pk || 0)">
                                        <i class='fa fa-arrow-left'></i>
                                    </a>
                                    <a href='javascript:;' class='btn btn-default btn-xs' ng-disabled="currentDep == 0" ng-click="setDep(0)"><i class='fa fa-home'></i></a>
                                </div>
                            </li>
                            <li ng-repeat="dep in depInstance.ancestors">
                                <a href="javascript:;" ng-click="setDep(dep.pk)">{{dep.depName}}</a>
                            </li>
                            <li class="active" ng-if="depInstance">{{depInstance.depName}}</li>
                        </ol>
                        <input type="text" class="form-control" style="max-width: 300px" placeholder="Tìm đơn vị, người dùng" ng-model="search"/>
                        <h4></h4>
                        <table class="table table-bordered table-hover table-striped table-record">
                            <tbody>
                                <tr ng-if="deps() && users() && !users().length && !deps().length">
                                    <td colspan="2" class="center">Chưa có phòng ban/tài khoản nào</td>
                                </tr>
                                <tr ng-repeat="dep in deps()" ng-click="setDep(dep.pk)" ng-class="<?php echo "{'stt-active': dep.stt==1, 'stt-inactive': dep.stt!=1}" ?>">
                                    <td style="min-width: 50px;" class="center">
                                        <i class="fa fa-folder-open"></i>
                                    </td>
                                    <td style="width: 100%;">
                                        <a href="javascript:;" ><label>{{dep.depName}}</label></a>
                                    </td>
                                </tr>
                                <tr ng-repeat="user in users()" ng-class="<?php echo "{'stt-active': user.stt==1, 'stt-inactive': user.stt!=1}" ?>">
                                    <td style="min-width: 50px;box-sizing: border-box;" class="center">
                                        <label class="check">
                                            <input type="checkbox" id="chk-user-picker-{{$index}}" ng-model="checked[user.pk]"/>
                                            <before></before>
                                            <after></after>
                                        </label>
                                    </td>
                                    <td style="width: 100%;">
                                        <label for="chk-user-picker-{{$index}}">{{user.fullName}}</label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" ng-class="<?php echo '{active: tab == 1}' ?>" >
                        <table class="table table-bordered table-hover table-striped table-record" ng-if="!selectedGroup">
                            <tbody>
                                <tr ng-repeat="group in groups" ng-click="setSelectedGroup(group)">
                                    <td style="min-width: 50px;box-sizing: border-box;" class="center">
                                        <i class="fa fa-users"></i>
                                    </td>
                                    <td style="width: 100%">
                                        <a href="javascript:;">
                                            {{group.groupName}}
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-bordered table-hover table-striped table-record" ng-if="selectedGroup">
                            <thead>
                                <tr>
                                    <td colspan="2">
                                        <button type="button" class="btn btn-default btn-xs" ng-click="setSelectedGroup(null)"><i class="fa fa-arrow-left"></i></button>
                                        &nbsp;
                                        <strong>{{selectedGroup.groupName}}</strong>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-if="selectedGroup.users && !selectedGroup.users.length">
                                    <td colspan="2" class="center">
                                        Không có người dùng trong nhóm này.
                                    </td>
                                </tr>
                                <tr ng-repeat="user in selectedGroup.users" ng-class="<?php echo "{'stt-active': user.stt==1, 'stt-inactive': user.stt!=1}" ?>">
                                    <td class="center" style="min-width: 50px;box-sizing: border-box;">
                                        <label class="check">
                                            <input type="checkbox" id="chk-user-picker2-{{$index}}" ng-model="checked[user.pk]"/>
                                            <before></before>
                                            <after></after>
                                        </label>
                                    </td>
                                    <td style="width: 100%;">
                                        <label for="chk-user-picker2-{{$index}}">{{user.fullName}}</label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy bỏ</button>
                <button type="button" class="btn btn-primary" ng-disabled="!isChecked()" ng-click="submit()">Chọn</button>
            </div>
        </div>
    </div>
</div>