<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo empty($head_info['description'])?'':$head_info['description']; ?>">
    <meta name="author" content="<?php echo empty($head_info['author'])?'':$head_info['author']; ?>">
    <link rel="icon" href="/favicon.ico">
    <title><?php echo empty($head_info['title'])?'':$head_info['title']; ?></title>
    <!--#include virtual="/include/css_bootstrap.shtml"-->
    <!--#include virtual="/include/css_dialog.shtml"-->
    <!--#include virtual="/include/css_base.shtml"-->
    <!--#include virtual="/include/css_font_awesome.shtml"-->
    <!--#include virtual="/include/css_dashboard.shtml"-->

    <!--#include virtual="/include/js_jquery.shtml"-->
    <!--#include virtual="/include/js_dialog_diy.shtml"-->
    <!--#include virtual="/include/js_popper.shtml"-->
    <!--#include virtual="/include/js_bootstrap.shtml"-->
    <!--#include virtual="/include/js_base.shtml"-->
</head>

<body>
<?php
    $layout_menus_list = $app->model_user_center->select_menu_list($self_info['uid']);
?>
<nav class="navbar navbar-expand-sm navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <a class="navbar-brand col-md-2" href="<?php echo $config['website_index']?:'/'; ?>"><img src="<?php echo $app->lang('website_logo_img'); ?>" height="35"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topMenus" aria-controls="topMenus" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="topMenus">
        <ul class="navbar-nav mr-auto">
            <?php
                foreach($layout_menus_list as $menu):
                    if($menu['position']!='TOP'){
                        continue;
                    }
            ?>
                <?php if(empty($menu['children'])): ?>
                    <li class="nav-item
                        <?php if(preg_match('/'.$menu['active_preg'].'/', $_SERVER['REQUEST_URI'], $match)>0): ?>
                        active
                        <?php endif; ?>
                    ">
                        <a class="nav-link <?php echo $menu['link_class']; ?>" href="<?php echo $menu['href']; ?>">
                            <?php echo $app->lang($menu['lang_key']); ?>
                            <?php if(preg_match('/'.$menu['active_preg'].'/', $_SERVER['REQUEST_URI'], $match)>0): ?>
                                <span class="sr-only">(current)</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <?php if($menu['lang_key']=='nav-user-avatar'): ?>
                        <a class="nav-link dropdown-toggle" href="<?php echo $menu['href']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="nav-avatar" src="<?php echo $self_info['avatar']; ?>" width="20" height="20">
                            <?php echo $self_info['nickname']; ?>
                        </a>
                        <?php else: ?>
                            <a class="nav-link dropdown-toggle" href="<?php echo $menu['href']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo $app->lang($menu['lang_key']); ?>
                            </a>
                        <?php endif; ?>
                        <div class="dropdown-menu" aria-labelledby="dropdown03">
                            <?php foreach($menu['children'] as $child): ?>
                                <a class="dropdown-item <?php echo $child['link_class']; ?>
                                    <?php if(preg_match('/'.$child['active_preg'].'/', $_SERVER['REQUEST_URI'], $match)>0): ?>
                                    active
                                    <?php endif; ?>
                                " href="<?php echo $child['href']; ?>">
                                    <?php echo $app->lang($child['lang_key']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <li class="nav-item dropdown">
                <?php if($app->current_lang()=='cn'): ?>
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">中文</a>
                <?php endif; ?>
                <?php if($app->current_lang()=='en'): ?>
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">English</a>
                <?php endif; ?>
                <div class="dropdown-menu" aria-labelledby="dropdown03">
                    <?php if($app->current_lang()!='cn'): ?>
                        <a class="dropdown-item" href="javascript:changeLanguage('cn')">中文</a>
                    <?php endif; ?>
                    <?php if($app->current_lang()!='en'): ?>
                        <a class="dropdown-item" href="javascript:changeLanguage('en')">English</a>
                    <?php endif; ?>
                </div>
            </li>
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar left_sidebar_menu">
            <div class="sidebar-sticky" id="leftMenus">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link ajax_main_content <?php echo preg_match('/dashboard.*/', $_SERVER['REQUEST_URI'], $match)>0?'active':''; ?>" href="/dashboard">
                            <i class="fa fa-home fa-lg" aria-hidden="true"></i>
                            <?php echo $app->lang('home_page'); ?> <span class="sr-only">(current)</span>
                        </a>
                    </li>
                </ul>

                <?php
                    $isLoopFirst = null;
                    foreach($layout_menus_list as $menu):
                        if($menu['position']!='LEFT'){
                            continue;
                        }
                ?>
                <?php if(!empty($menu['children'])): ?>
                    <?php if($isLoopFirst): ?>
                        </ul>
                    <?php endif; $isLoopFirst=false; ?>
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-3 mb-1 text-muted">
                        <span><?php echo $app->lang($menu['lang_key']); ?></span>
                    </h6>
                    <ul class="nav flex-column">
                        <?php foreach($menu['children'] as $child): ?>
                        <li class="nav-item pl-2">
                            <a class="nav-link <?php echo $child['link_class']; ?> <?php echo preg_match('/'.$child['active_preg'].'/', $_SERVER['REQUEST_URI'], $match)>0?'active':''; ?>" href="<?php echo $child['href']; ?>">
                                <?php if(trim($child['icon'])!='' && substr(trim($child['icon']),0,1)=='<'): ?>
                                    <?php echo $child['icon']; ?>
                                <?php elseif( trim($child['icon'])!=''): ?>
                                    <i class="fa <?php echo $child['icon']; ?>" aria-hidden="true"></i>
                                <?php endif; ?>
                                <?php echo $app->lang($child['lang_key']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <?php if(empty($isLoopFirst)): ?>
                    <ul class="nav flex-column mb-2 mt-2">
                    <?php endif; $isLoopFirst=true; ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $menu['link_class']; ?> <?php echo preg_match('/'.$menu['active_preg'].'/', $_SERVER['REQUEST_URI'], $match)>0?'active':''; ?>" href="<?php echo $menu['href']; ?>">
                            <?php if(trim($menu['icon'])!='' && substr(trim($menu['icon']),0,1)=='<'): ?>
                                <?php echo $menu['icon']; ?>
                            <?php elseif( trim($menu['icon'])!=''): ?>
                                <i class="fa <?php echo $menu['icon']; ?>" aria-hidden="true"></i>
                            <?php endif; ?>
                            <?php echo $app->lang($menu['lang_key']); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php if($isLoopFirst): ?>
                    </ul>
                <?php endif; ?>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
            <!--{$contents}-->
        </main>
    </div>
</div>

<a class="fa fa-indent" id="left_menu_btn"></a>

<!--#include virtual="/include/js_dashboard.shtml"-->
<script src="/js/language/<?php echo $app->current_lang(); ?>.js"></script>
</body>
</html>
