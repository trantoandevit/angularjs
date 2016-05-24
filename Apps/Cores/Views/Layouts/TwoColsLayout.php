<?php

namespace Apps\Cores\Views\Layouts;

use \Libs\Layout;
use Libs\Menu;

class TwoColsLayout extends Layout
{

    const HTML_REQUIRED = '<span class="required">&#10033;</span>';

    protected $title;
    protected $brand = 'Brand';
    protected $companyWebsite;
    protected $ngApp = 'sb2';

    /** @var \Apps\Cores\Models\UserEntity */
    protected $user;

    /** @var Menu */
    protected $sideMenu;

    function setSideMenu($menus)
    {
        $this->sideMenu = $menus;
        return $this;
    }
    
    function setNgApp($string)
    {
        $this->ngApp = $string;
        return $this;
    }
    function setUser($user)
    {
        $this->user = $user;
        return $this;
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

    /**
     * 
     * @param type $brand
     * @param type $companyWebsite
     */
    function setBasicInfo($brand, $companyWebsite)
    {
        $this->brand = $brand;
        $this->companyWebsite = $companyWebsite;
        return $this;
    }

    protected function renderSideMenu($children, $level = 1)
    {
        $ulClasses = array(
            1 => 'nav nav-second-level',
            2 => 'nav nav-third-level'
        );
        foreach ($children as $menu)
        {
            /* @var $menu Menu */
            if ($menu->hasChildren())
            {
                echo '<li>';
                echo "<a href='{$menu->url}'>{$menu->label} <span class='fa arrow'></span></a>";
                echo "<ul class='" . $ulClasses[$level] . "'>";
                $this->renderSideMenu($menu->children, $level + 1);
                echo "</ul>";
                echo '</li>';
            }
            else
            {
                echo "<li><a href='{$menu->url}'>{$menu->label}</a>";
            }
        }
    }

    public function themeUrl()
    {
        return url('/themes/sb2');
    }

    protected function renderLayout($content)
    {
        ?>

        <!DOCTYPE html>
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

            <body ng-app="<?php echo $this->ngApp; ?>">
                <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div>

                <div id="wrapper">

                    <!-- Navigation -->
                    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="<?php echo url() ?>"><?php echo $this->brand ?></a>
                        </div>
                        <!-- /.navbar-header -->

                        <ul class="nav navbar-top-links navbar-right">
                            <!-- /.dropdown -->
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-user fa-fw"></i> <?php echo $this->user->fullName ?>  <i class="fa fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="<?php echo url('/admin/login/changePassword') ?>"><i class="fa fa-key fa-fw"></i> Đổi mật khẩu</a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?php echo url('/admin/login') ?>"><i class="fa fa-sign-out fa-fw"></i> Đăng xuất</a>
                                    </li>
                                </ul>
                                <!-- /.dropdown-user -->
                            </li>
                            <!-- /.dropdown -->
                        </ul>
                        <!-- /.navbar-top-links -->

                        <div class="navbar-default sidebar" role="navigation">
                            <div class="sidebar-nav navbar-collapse">
                                <ul class="nav" id="side-menu">
        <?php $this->renderSideMenu($this->sideMenu->children) ?>
                                </ul>
                            </div>
                            <!-- /.sidebar-collapse -->
                        </div>
                        <!-- /.navbar-static-side -->
                    </nav>

                    <div id="page-wrapper">
        <?php echo $content ?>

                        <footer>
                            <a href="<?php echo $this->companyWebsite ?>"><?php echo $this->companyWebsite ?></a>
                            &nbsp;|&nbsp;
                            @Copyright <?php echo date('Y')?>
                        </footer>
                    </div>
                    <!-- /#page-wrapper -->
                </div>
                <!-- /#wrapper -->

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
                <!--site root-->
                <script><?php echo "var SITEROOT = '" . SITE_ROOT ."'" ?></script>
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
