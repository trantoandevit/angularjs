<?php

namespace Apps\Cores\Views\Layouts;

use Libs\Layout;

class ContentOnlyLayout extends Layout
{

    protected $title;
    protected $brand;

    public function themeUrl()
    {
        return url('/themes/sb2');
    }

    function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    protected function renderLayout($content)
    {
        ?>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <meta name="description" content="">
                <meta name="author" content="">

                <title><?php echo $this->title ?></title>

                <!-- Bootstrap Core CSS -->
                <link href="<?php echo $this->themeUrl() ?>/css/bootstrap.min.css" rel="stylesheet">

                <!-- MetisMenu CSS -->
                <link href="<?php echo $this->themeUrl() ?>/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

                <!-- Custom CSS -->
                <link href="<?php echo $this->themeUrl() ?>/css/sb-admin-2.css" rel="stylesheet">

                <!-- Custom Fonts -->
                <link href="<?php echo $this->themeUrl() ?>/css/font-awesome.min.css" rel="stylesheet" type="text/css">
                <link href='<?php echo $this->themeUrl() ?>/fonts/google/roboto.css' rel='stylesheet' type='text/css'>

                <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
                <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
                <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
                <![endif]-->

                <link href="<?php echo $this->themeUrl() ?>/css/custom.css" rel="stylesheet" type="text/css">

                <?php
                foreach ($this->css as $css)
                {
                    echo "\n<link href='$css' rel='stylesheet' type='text/css'>";
                }
                ?>

                <!-- jQuery -->
                <script src="<?php echo $this->themeUrl() ?>/js/jquery.min.js"></script>
                <script src="<?php echo url('/admin/config.js') ?>"></script>
            </head>

            <body ng-app="sb2">
                <?php echo $content ?>
                <!--angular-->
                <script src="<?php echo $this->themeUrl() ?>/js/angular.min.js"></script>

                <!-- Bootstrap Core JavaScript -->
                <script src="<?php echo $this->themeUrl() ?>/js/bootstrap.min.js"></script>

                <!-- Metis Menu Plugin JavaScript -->
                <script src="<?php echo $this->themeUrl() ?>/plugins/metisMenu/metisMenu.min.js"></script>

                <!-- Custom Theme JavaScript -->
                <script src="<?php echo $this->themeUrl() ?>/js/sb-admin-2.js"></script>

                <!--validation-->
                <script src="<?php echo $this->themeUrl() ?>/plugins/validation/html5-validation.js"></script>

                <script src="<?php echo $this->themeUrl() ?>/js/custom.js"></script>
                <?php
                foreach ($this->js as $js)
                {
                    echo "\n<script src='$js'></script>";
                }
                ?>
            </body>

        </html>
        <?php
    }

}
