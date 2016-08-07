<?php
if(!function_exists('sizecount')){
    function sizecount($size){
        if ($size < 0.001) {
            return round(($size * 1000 * 1000), 2).'B';
        } elseif ($size >= 0.001 && $size < 1) {
            return round(($size * 1000), 2).'KB';
        } elseif ($size >= 1 && $size < 1000) {
            return round($size, 2).'MB';
        } elseif ($size >= 1000) {
            return round(($size / 1000), 2).'GB';
        }
    }
}
if(!function_exists('replace_attr')){
    function replace_attr($context){
        global $config;
        $context = str_replace("{sitename}", $config['sitename'], $context);
        $context = str_replace("{size}", sizecount($config['size']), $context);
        $context = str_replace("{url}", sizecount($config['url']), $context);
        $context = str_replace("{total}", $config['total'], $context);
        $context = str_replace("{subtitle}", $config['subtitle'], $context);
        return $context;
    }
}

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
?>
    <ul class="nav nav-tabs">
        <li><a href="index.php">首頁</a></li>
        <li><a href="logout.php">登出</a></li>
        <?php
        $pages = $db->select('page');
        foreach ($pages as $page) {
            echo '<li><a href="page.php?id=' . $page['id'] . '">'.replace_attr($page['title']).'</a></li>';
        }
        ?>
    </ul>
<?php 
    } else {
?>
    <ul class="nav nav-tabs">
        <li><a href="index.php">首頁</a></li>
        <li><a href="login.php">登入</a></li>
        <?php if ($config['reg']) { ?>
            <li><a href="reg.php">註冊</a></li>
        <?php } ?>
        <?php
        $pages = $db->select('page');
        foreach ($pages as $page) {
            echo '<li><a href="page.php?id=' . $page['id'] . '">'.replace_attr($page['title']).'</a></li>';
        }
        ?>
    </ul>
<?php } ?>