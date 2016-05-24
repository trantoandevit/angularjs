<?php

namespace Apps\Cores\Controllers;

use Apps\Cores\Views\Setting\StringControl;
use Apps\Cores\Views\Setting\BooleanControl;
use Apps\Cores\Views\Setting\SelectControl;
use Apps\Cores\Views\Setting\TextControl;
use Libs\Setting;

class SettingCtrl extends CoresCtrl
{

    function application()
    {
        $this->requireAdmin();

        if ($this->req->get('activate') || $this->req->get('deactivate'))
        {
            $appId = $this->req->get('activate');
            $status = 'true';
            if (!$appId)
            {
                $appId = $this->req->get('deactivate');
                $status = 'false';
            }
            $setting = new Setting($appId);
            $setting->xml->attributes()->active = $status;
            file_put_contents(BASE_DIR . '/Config/Settings/' . $appId . '.xml', $this->formatXml($setting->xml));

            $this->resp->redirect(url('/admin/application'));
            return;
        }

        $this->twoColsLayout->render('Setting/application.phtml');
    }

    function setting()
    {
        $this->requireAdmin();

        $appId = $this->req->get('appId');
        $groupName = $this->req->get('group');

        if (!$appId || !$groupName)
        {
            foreach (\Libs\Setting::getAllApp() as $appId)
            {
                $setting = new \Libs\Setting($appId);
                if (!$setting->xml->settings)
                {
                    continue;
                }
                foreach ($setting->xml->settings->group as $groupXml)
                {
                    $this->resp->redirect(url('/admin/setting?appId=' . $appId . '&group=' . $groupXml->attributes()->name));
                    return;
                }
            }
        }

        $this->twoColsLayout->render('Setting/setting.phtml', array(
            'pAppId'     => $appId,
            'pGroupName' => $groupName
        ));
    }

    function update()
    {
        $this->requireAdmin();

        $appId = $this->req->post('appId');
        $groupName = $this->req->post('groupName');
        $setting = new Setting($appId);

        foreach ($setting->xml->settings->group as $group)
        {
            if ($group->attributes()->name != $groupName)
            {
                continue;
            }
            foreach ($group->field as $field)
            {
                switch ((string) $field->type) {
                    case 'string':
                        $control = new StringControl($field);
                        $field->value = $control->handleValue($this->req->post((string) $field->id));
                        break;
                    case 'boolean':
                        $control = new BooleanControl($field);
                        $field->value = $control->handleValue($this->req->post((string) $field->id));
                        break;
                    case 'select':
                        $control = new SelectControl($field);
                        $field->value = $control->handleValue($this->req->post((string) $field->id));
                        break;
                    case 'text':
                        $control = new TextControl($field);
                        $field->value = $control->handleValue($this->req->post((string) $field->id));
                        break;
                }
            }
        }

        file_put_contents(BASE_DIR . '/Config/Settings/' . $appId . '.xml', $this->formatXml($setting->xml));

        $this->resp->redirect(url('/admin/setting?appId=' . $appId . '&group=' . $groupName));
    }

    protected function formatXml(\SimpleXMLElement $xml)
    {
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        /* @var $xml SimpleXMLElement */
        $domxml->loadXML($xml->asXML());
        return $domxml->saveXML();
    }

}
