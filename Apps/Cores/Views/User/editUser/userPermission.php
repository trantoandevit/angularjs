<div ng-init="selectedAppPem = '0'"></div>
<select class="form-control" ng-model="selectedAppPem">
    <option ng-repeat="app in permissions" value="{{$index}}">{{app.name}}</option>
</select>
<h4></h4>
<fieldset ng-repeat="group in permissions[selectedAppPem]['groups']">
    <legend>{{group.name}}</legend>

    <table class='table-bordered table table-striped table-hover'>
        <tr ng-repeat="pem in group.permissions">
            <td>
                <label class="check" value="pem.name">
                    <input type="checkbox" value="{{pem.id}}" ng-checked="editingUser.permissions.indexOf(pem.id) != -1" ng-click="togglePermission($event)"/>
                    <before></before>
                    <after></after>&nbsp;
                    {{pem.name}}
                </label>
            </td>
        </tr>

    </table>
</fieldset>