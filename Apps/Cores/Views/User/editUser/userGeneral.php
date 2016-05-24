<fieldset>
    <legend>Tài khoản</legend>
    <div class="form-group">
        <label class="control-label col-xs-3" for="user-account"><?php echo static::HTML_REQUIRED ?> Đăng nhập:</label>
        <div class="col-xs-9">
            <div style="display: flex">
                <div style="flex: 1;">
                    <input type="text" id="user-account" class="form-control" ng-model="editingUser.account" 
                           required minlength="3" ng-dom="userAccDom"/>
                    <div class="help-block" ng-if="!ajax.checkUniqueAccount && !editingUser.errcheckUniqueAccount" >
                        Tên tài khoản viết không dấu, không được trùng.
                    </div>
                    <div class="help-block" ng-if="ajax.checkUniqueAccount">
                        <i class="fa fa-circle-o-notch fa-spin"></i> Đang kiểm tra...
                    </div>
                    <div class="help-block error" ng-if="editingUser.errcheckUniqueAccount">
                        Tài khoản bị trùng, vui lòng chọn tên khác.
                    </div>
                </div>
                <div>
                    &nbsp;
                    <label class="check" ng-if="!editingUser.isAdmin">
                        <input type="checkbox" ng-model="editingUser.stt" />
                        <before></before>
                        <after></after>&nbsp;
                        Hoạt động
                    </label>
                </div>
            </div>
            <div ng-show="!editingUser.changePass">
                <div class="help-block">
                    <a href="javascript:;" ng-click="togglePassword()">Đổi mật khẩu</a>
                </div>
            </div>
        </div>
    </div>
    <div ng-show="editingUser.changePass">
        <div class="form-group">
            <label class="control-label col-xs-3" for="user-new-pass"><span ng-if="!editingUser.pk"><?php echo static::HTML_REQUIRED ?> </span>Mật khẩu mới:</label>
            <div class="col-xs-9">
                <input type="password" id="user-new-pass" class="form-control" ng-model="editingUser.newPass" 
                       ng-dom="newPassDom" ng-required="!editingUser.pk"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-xs-3" for="user-re-pass">Nhập lại mật khẩu:</label>
            <div class="col-xs-9">
                <input type="password" id="user-re-pass" class="form-control" ng-model="editingUser.rePass" ng-dom="rePassDom"/>
                <div class="help-block">
                    <div class="pull-left">
                        <a href="javascript:;" ng-click="togglePassword()" ng-if="editingUser.pk">Hủy đổi mật khẩu</a>&nbsp;
                    </div>
                    <div class="pull-right">
                        <span ng-if="editingUser.passError">{{editingUser.passError}}</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</fieldset>
<fieldset>
    <legend>Thông tin hành chính</legend>

    <div class="form-group">
        <label class="control-label col-xs-3" for="user-dep">Thuộc đơn vị:</label>
        <div class="col-xs-9">
            <div class="parent-dep">
                <input type="text" class="form-control" id="user-dep" readonly placeholder="Chọn đơn vị"
                       value="{{editingUser.parentDep.depName}}" ng-click="pickUserDep()"/>
                <span ng-if="editingUser.parentDep && editingUser.parentDep.pk" ng-click="clearUserDep()">&times;</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-3" for="user-name"><?php echo static::HTML_REQUIRED ?> Họ và tên:</label>
        <div class="col-xs-9">
            <input type="text" id="user-name" class="form-control" ng-model="editingUser.fullName" required/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-3" for="user-job">Chức vụ:</label>
        <div class="col-xs-9">
            <input type="text" id="user-job" class="form-control" ng-model="editingUser.jobTitle"/>
        </div>
    </div>
</fieldset>