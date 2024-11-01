<div id="wpbody" role="main">
    <div class="wrap">
        <h1>Sharp Login Settings</h1>
        <br><br>


        <!-- create tabs -->
        <h2 class="nav-tab-wrapper">
            <a href="?page=sharplogin&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <a href="?page=sharplogin&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>">Advanced</a>
        </h2>
        <!-- end tabs -->

        <br>

        <!-- display content -->
        <?php
        switch ($active_tab) {
            case 'general':
                include_once 'sharplogin_general_page.php';
                break;
            case 'advanced':
                include_once 'sharplogin_advanced_page.php';
                break;
        }
        ?>
        <!-- end content -->

    </div>

        
    </div>
</div>
</div>
</div>